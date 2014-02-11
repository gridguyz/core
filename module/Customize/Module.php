<?php

namespace Grid\Customize;

use Zork\Stdlib\ModuleAbstract;
use Zend\ModuleManager\ModuleManagerInterface;
use Zend\ModuleManager\Feature\InitProviderInterface;
use Zend\ModuleManager\Feature\ViewHelperProviderInterface;

/**
 * Grid\Customize\Module
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Module extends ModuleAbstract
          implements InitProviderInterface,
                     ViewHelperProviderInterface
{

    /**
     * Module base-dir
     *
     * @const string
     */
    const BASE_DIR = __DIR__;

    /**
     * Initialize workflow
     *
     * @param   ModuleManagerInterface  $manager
     * @return  void
     */
    public function init( ModuleManagerInterface $manager )
    {
        $this->serviceLocator = $manager->getEvent()
                                        ->getParam( 'ServiceManager' );
    }

    /**
     * Get `customCss` view-helper instance
     *
     * @return  View\Helper\CustomCss
     */
    public function getCustomCssHelper()
    {
        return $this->serviceLocator
                    ->get( 'Grid\Customize\View\Helper\CustomCss' );
    }

    /**
     * Expected to return \Zend\ServiceManager\Config object or array to
     * seed such an object.
     *
     * @return array|\Zend\ServiceManager\Config
     */
    public function getViewHelperConfig()
    {
        return array(
            'invokables'    => array(
                'formCustomizeProperties'   => 'Grid\Customize\Form\View\Helper\FormCustomizeProperties',
            ),
            'factories'     => array(
                'customCss'                 => array( $this, 'getCustomCssHelper' ),
            ),
        );
    }

}
