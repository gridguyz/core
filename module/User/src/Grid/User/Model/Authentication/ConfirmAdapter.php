<?php

namespace Grid\User\Model\Authentication;

use Zend\Authentication\Result;
use Zork\Model\ModelAwareTrait;
use Zork\Model\ModelAwareInterface;
use Zork\Model\Structure\StructureAbstract;
use Zork\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zork\Factory\AdapterInterface as FactoryAdapterInterface;
use Zend\Authentication\Adapter\AdapterInterface as AuthAdapterInterface;

/**
 * ConfirmAdapter
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class ConfirmAdapter extends StructureAbstract
                  implements ModelAwareInterface,
                             AuthAdapterInterface,
                             FactoryAdapterInterface,
                             ServiceLocatorAwareInterface
{

    use ModelAwareTrait,
        ServiceLocatorAwareTrait;

    /**
     * @var string
     */
    protected $hash;

    /**
     * Return true if and only if $options accepted by this adapter
     * If returns float as likelyhood the max of these will be used as adapter
     *
     * @param array $options;
     * @return float
     */
    public static function acceptsOptions( array $options )
    {
        return isset( $options['hash'] );
    }

    /**
     * Return a new instance of the adapter by $options
     *
     * @param array $options;
     * @return Grid\User\Model\Authentication\DefaultAdapter
     */
    public static function factory( array $options = null )
    {
        return new static( $options );
    }

    /**
     * Performs an authentication attempt
     *
     * @return \Zend\Authentication\Result
     * @throws \Zend\Authentication\Adapter\Exception\ExceptionInterface
     *         If authentication cannot be performed
     */
    public function authenticate()
    {
        $confirm = $this->getServiceLocator()
                        ->get( 'Grid\User\Model\ConfirmHash' );

        if ( $confirm->has( $this->hash ) )
        {
            $user = $this->getModel()
                         ->findByEmail( $confirm->find( $this->hash ) );

            if ( ! empty( $user ) )
            {
                $user->confirmed = true;

                if ( $user->save() )
                {
                    $confirm->delete( $this->hash );
                    return new Result( Result::SUCCESS, $user );
                }
                else
                {
                    return new Result( Result::FAILURE_UNCATEGORIZED, null );
                }
            }
        }
        else
        {
            return new Result( Result::FAILURE_CREDENTIAL_INVALID, null );
        }

        return new Result( Result::FAILURE_IDENTITY_NOT_FOUND, null );
    }

}
