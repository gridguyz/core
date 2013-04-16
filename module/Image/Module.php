<?php

namespace Grid\Image;

use Zork\Stdlib\ModuleAbstract;
use Zend\ModuleManager\Feature\ViewHelperProviderInterface;

/**
 * Grid\Core\Module
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Module extends ModuleAbstract
          implements ViewHelperProviderInterface
{

    /**
     * Module base-dir
     *
     * @const string
     */
    const BASE_DIR = __DIR__;

    /**
     * Expected to return \Zend\ServiceManager\Config object or array to
     * seed such an object.
     *
     * @return array|\Zend\ServiceManager\Config
     */
    public function getViewHelperConfig()
    {
        return array(
            'factories'     => array(
                'thumbnail' => function ( $serviceLocator ) {
                    return new View\Helper\Thumbnail(
                        $serviceLocator->get( 'SiteInfo' )
                                       ->getModel()
                                       ->getSchema()
                    );
                },
            ),
        );
    }

}
