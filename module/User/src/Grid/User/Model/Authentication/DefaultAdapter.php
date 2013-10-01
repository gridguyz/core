<?php

namespace Grid\User\Model\Authentication;

use Zend\Authentication\Result;
use Zork\Model\ModelAwareTrait;
use Zork\Model\ModelAwareInterface;
use Zork\Model\Structure\StructureAbstract;
use Zork\Factory\AdapterInterface as FactoryAdapterInterface;
use Zend\Authentication\Adapter\AdapterInterface as AuthAdapterInterface;

/**
 * DefaultAdapter
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class DefaultAdapter extends StructureAbstract
                  implements ModelAwareInterface,
                             AuthAdapterInterface,
                             FactoryAdapterInterface
{

    use ModelAwareTrait;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $password;

    /**
     * Return true if and only if $options accepted by this adapter
     * If returns float as likelyhood the max of these will be used as adapter
     *
     * @param array $options;
     * @return float
     */
    public static function acceptsOptions( array $options )
    {
        return isset( $options['email'] ) &&
               isset( $options['password'] );
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
        /* @var $user \Grid\User\Model\User\Structure */
        $user = $this->getModel()
                     ->findByEmail( $this->email );

        if ( empty( $user ) )
        {
            return new Result( Result::FAILURE_IDENTITY_NOT_FOUND, null );
        }

        if ( ! $user->isActive() ||
             ! $user->verifyPassword( $this->password, true ) )
        {
            return new Result( Result::FAILURE_CREDENTIAL_INVALID, null );
        }

        return new Result( Result::SUCCESS, $user );
    }

}
