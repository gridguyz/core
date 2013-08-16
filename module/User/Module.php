<?php

namespace Grid\User;

use Zork\Stdlib\ModuleAbstract;
use Zend\ModuleManager\ModuleManagerInterface;
use Zend\ModuleManager\Feature\InitProviderInterface;
use Zend\ModuleManager\Feature\ViewHelperProviderInterface;
use Zend\EventManager\EventInterface;
use Grid\User\Datasheet\Event As DatasheetEvent;
use Grid\User\Datasheet\EventHandler As DatasheetEventHandler;

/**
 * Grid\Core\Module
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
     * On bootstrap
     * 
     * @param \Zend\EventManager\EventInterface $event
     */
    public function onBootstrap( EventInterface $event )
    {
        $application    = $event->getApplication();
        $serviceManager = $application->getServiceManager();
        $sharedEvents   = $application->getEventManager()
                                      ->getSharedManager();
        
        $datasheetEventHandler = new DatasheetEventHandler($serviceManager);
        
        $sharedEvents->attach( 
            'Grid\User\Datasheet\Service', 
            DatasheetEvent::EVENT_REGISTER, 
            array($datasheetEventHandler,'onRegister'),
            1000
        );
        
        $sharedEvents->attach( 
            'Grid\User\Datasheet\Service', 
            DatasheetEvent::EVENT_SAVE, 
            array($datasheetEventHandler,'onSave'),
            1000
        );
            
       $sharedEvents->attach( 
            'Grid\User\Datasheet\Service', 
            DatasheetEvent::EVENT_DELETE, 
            array($datasheetEventHandler,'onDelete'),
            1000
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
                'userLocale'    => 'Grid\User\View\Helper\UserLocale',
            ),
            'factories'         => array(
                'isAllowed'     => function ( $sm ) use ( $serviceLocator ) {
                    return new View\Helper\IsAllowed(
                        $serviceLocator->get( 'Grid\User\Model\Permissions\Model' )
                    );
                },
            ),
        );
    }

}
