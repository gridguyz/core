<?php

namespace Grid\Core\Model\Settings;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * DefinitionServiceFactory
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class DefinitionServiceFactory implements FactoryInterface
{

    /**
     * Create the locale-service
     *
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return \Core\Model\Settings\Definitions
     */
    public function createService( ServiceLocatorInterface $serviceLocator )
    {
        // Configure the definitions
        $config     = $serviceLocator->get( 'Configuration' );
        $srvConfig  = isset( $config['modules']['Grid\Core']['settings'] )
                    ? $config['modules']['Grid\Core']['settings']
                    : array();
        return new Definitions( $srvConfig );
    }

}
