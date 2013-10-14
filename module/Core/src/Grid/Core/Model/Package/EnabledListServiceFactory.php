<?php

namespace Grid\Core\Model\Package;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * EnabledListServiceFactory
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class EnabledListServiceFactory implements FactoryInterface
{

    /**
     * Create the enabled-list-service
     *
     * @param   \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return  \Grid\Core\Model\Package\EnabledList
     */
    public function createService( ServiceLocatorInterface $serviceLocator )
    {
        // Configure the definitions
        $config     = $serviceLocator->get( 'Configuration' );
        $packages   = isset( $config['modules']['Grid\Core']['enabledPackages'] )
                    ? (array) $config['modules']['Grid\Core']['enabledPackages']
                    : array();
        return new EnabledList( $packages );
    }

}
