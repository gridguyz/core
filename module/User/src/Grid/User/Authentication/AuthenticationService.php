<?php

namespace Grid\User\Authentication;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Authentication\AuthenticationService as ZendAuthenticationService;

/**
 * AuthenticationService
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class AuthenticationService extends ZendAuthenticationService
                         implements ServiceLocatorAwareInterface
{

    use ServiceLocatorAwareTrait;

    /**
     * @var \Grid\User\Model\User\Model
     */
    protected $userModel;

    /**
     * @var bool
     */
    protected $identityRefreshed = false;

    /**
     * Constructor
     *
     * @param   ServiceLocatorInterface $serviceLocator
     * @param   StorageInterface        $storage
     * @param   AdapterInterface        $adapter
     */
    public function __construct( ServiceLocatorInterface    $serviceLocator,
                                 StorageInterface           $storage = null,
                                 AdapterInterface           $adapter = null )
    {
        $this->setServiceLocator( $serviceLocator );
        parent::__construct( $storage, $adapter );
    }

    /**
     * Get user model
     *
     * @return  \Grid\User\Model\User\Model
     */
    protected function getUserModel()
    {
        if ( null === $this->userModel )
        {
            $this->userModel = $this->getServiceLocator()
                                    ->get( 'Grid\User\Model\User\Model' );
        }

        return $this->userModel;
    }

    /**
     * Returns the identity from storage or null if no identity is available
     *
     * @return \Grid\User\Model\User\Structure|null
     */
    public function getIdentity()
    {
        $identity = parent::getIdentity();

        if ( null !== $identity && ! $this->identityRefreshed )
        {
            $user = $this->getUserModel()
                         ->find( $identity->id );

            if ( ! empty( $user ) )
            {
                $this->getStorage()
                     ->write( $identity = $user );

                $this->identityRefreshed = true;
            }
        }

        return $identity;
    }

    /**
     * Authenticates against the supplied adapter
     *
     * @param   AdapterInterface    $adapter
     * @return  \Zend\Authentication\Result
     * @throws  \Zend\Authentication\Exception\RuntimeException
     */
    public function authenticate( AdapterInterface $adapter = null )
    {
        $result = parent::authenticate( $adapter );

        if ( $result->isValid() )
        {
            $this->identityRefreshed = true;
        }

        return $result;
    }

}
