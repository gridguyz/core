<?php

namespace Grid\Paragraph\Model\Dashboard;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * DefinitionServiceFactory
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class CustomizationServiceFactory implements FactoryInterface
{

    /**
     * Create the customization-service
     *
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return \Paragraph\Model\Dashboard\Customization
     */
    public function createService( ServiceLocatorInterface $serviceLocator )
    {
        // Configure the definitions
        $config     = $serviceLocator->get( 'Configuration' );
        $srvConfig  = isset( $config['modules']['Grid\Paragraph'] )
                    ? $config['modules']['Grid\Paragraph']
                    : array();

        return new Customization(
            isset( $srvConfig['customizeSelectors'] )
                ? $srvConfig['customizeSelectors']
                : array(),
            isset( $srvConfig['customizeMapForms'] )
                ? $srvConfig['customizeMapForms']
                : array()
        );
    }

}
