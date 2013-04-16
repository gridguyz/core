<?php

namespace Grid\Paragraph;

use Zork\Stdlib\ModuleAbstract;
use Zend\ModuleManager\ModuleManagerInterface;
use Zend\ModuleManager\Feature\InitProviderInterface;
use Zend\ModuleManager\Feature\ViewHelperProviderInterface;
use Zend\ModuleManager\Feature\ControllerPluginProviderInterface;

/**
 * Grid\Paragraph\Module
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Module extends ModuleAbstract
          implements InitProviderInterface,
                     ViewHelperProviderInterface,
                     ControllerPluginProviderInterface
{

    /**
     * Module base-dir
     *
     * @const string
     */
    const BASE_DIR = __DIR__;

    /**
     * Stored service-locator
     *
     * @var \Zend\ServiceManager\ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * Initialize workflow
     *
     * @param  ModuleManagerInterface $manager
     * @return void
     */
    public function init( ModuleManagerInterface $manager )
    {
        $this->serviceLocator = $manager->getEvent()
                                        ->getParam( 'ServiceManager' );
    }

    /**
     * Expected to return \Zend\ServiceManager\Config object or array to
     * seed such an object.
     *
     * @return array|\Zend\ServiceManager\Config
     */
    public function getControllerPluginConfig()
    {
        $serviceLocator = $this->serviceLocator;

        return array(
            'factories'     => array(
                'paragraphLayout' => function ( $sm ) use ( $serviceLocator ) {
                    return new Controller\Plugin\ParagraphLayout(
                        $serviceLocator->get(
                            'Grid\Paragraph\Model\Paragraph\MiddleLayoutModel'
                        )
                    );
                },
            ),
        );
    }

    /**
     * Expected to return \Zend\ServiceManager\Config object or array to
     * seed such an object.
     *
     * @return array|\Zend\ServiceManager\Config
     */
    public function getViewHelperConfig()
    {
        $serviceLocator = $this->serviceLocator;

        return array(
            'invokables'    => array(
                'formColumnsPercentages' => 'Grid\Paragraph\Form\View\Helper\FormColumnsPercentages',
            ),
            'factories'     => array(
                'metaContent' => function ( $sm ) use ( $serviceLocator ) {
                    return new View\Helper\MetaContent(
                        $serviceLocator->get(
                            'Grid\Paragraph\Model\Paragraph\MiddleLayoutModel'
                        )
                    );
                },
            ),
        );
    }

}
