<?php

namespace Grid\User\Authentication;

use Grid\User\Model\User\Model as UserModel;
use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Authentication\AuthenticationService as ZendAuthenticationService;

/**
 * AuthenticationService
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class AuthenticationService extends ZendAuthenticationService
{

    /**
     * @var UserModel
     */
    protected $userModel;

    /**
     * @var bool
     */
    protected $identityRefreshed = false;

    /**
     * Constructor
     *
     * @param   UserModel           $userModel
     * @param   StorageInterface    $storage
     * @param   AdapterInterface    $adapter
     */
    public function __construct( UserModel          $userModel,
                                 StorageInterface   $storage = null,
                                 AdapterInterface   $adapter = null )
    {
        $this->userModel = $userModel;
        parent::__construct( $storage, $adapter );
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
            $user = $this->userModel
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
