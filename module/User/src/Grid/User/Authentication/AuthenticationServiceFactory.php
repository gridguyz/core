<?php

namespace Grid\User\Authentication;

use Zend\Authentication\Storage\Session;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * AuthenticationServiceFactory
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class AuthenticationServiceFactory implements FactoryInterface
{

    /**
     * Create authentication service
     *
     * @param   ServiceLocatorInterface $services
     * @return  AuthenticationService
     */
    public function createService( ServiceLocatorInterface $services )
    {
        $userModel  = $services->get( 'Grid\User\Model\User\Model' );
        $manager    = $services->get( 'Zend\Session\ManagerInterface' );
        $session    = new Session( null, null, $manager );
        return new AuthenticationService( $userModel, $session );
    }

}
