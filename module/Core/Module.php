<?php

namespace Grid\Core;

use Exception;
use Zend\Mvc\MvcEvent;
use Zork\Stdlib\ModuleAbstract;
use Zend\EventManager\EventInterface;
use Zend\ModuleManager\ModuleManagerInterface;
use Zork\Mvc\View\Http\InjectTemplateListener;
use Zork\Mvc\Controller\LocaleSelectorInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ModuleManager\Feature\InitProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\ViewHelperProviderInterface;

/**
 * Grid\Core\Module
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Module extends ModuleAbstract
          implements InitProviderInterface,
                     ServiceProviderInterface,
                     BootstrapListenerInterface,
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
     * Stored module-manager
     *
     * @var ModuleManagerInterface
     */
    protected $moduleManager;

    /**
     * Stored response
     *
     * @var \Zend\Stdlib\ResponseInterface
     */
    protected $response;

    /**
     * Previous exception handler
     *
     * @var callable|null
     */
    protected $previousExceptionHandler;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->previousExceptionHandler = set_exception_handler(
            array( $this, 'exceptionHandler' )
        );
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        set_exception_handler( $this->previousExceptionHandler );
    }

    /**
     * Initialize workflow
     *
     * @param  ModuleManagerInterface $manager
     * @return void
     */
    public function init( ModuleManagerInterface $manager )
    {
        $this->moduleManager  = $manager;
        $this->serviceLocator = $manager->getEvent()
                                        ->getParam( 'ServiceManager' );

        $shared = $manager->getEventManager()
                          ->getSharedManager();

        $shared->attach(
            'Zend\Stdlib\DispatchableInterface',
            MvcEvent::EVENT_DISPATCH,
            array( new InjectTemplateListener, 'injectTemplate' ),
            -85
        );

        $shared->attach(
         // 'Zend\Mvc\Controller\AbstractController',
            'Zend\Stdlib\DispatchableInterface',
            MvcEvent::EVENT_DISPATCH,
            array( $this, 'onDispatch' ),
            100
        );
    }

    /**
     * Listen to the bootstrap event
     *
     * @param \Zend\EventManager\EventInterface $event
     * @return array
     */
    public function onBootstrap( EventInterface $event )
    {
        /* @var $event          \Zend\Mvc\MvcEvent */
        /* @var $application    \Zend\Mvc\Application */
        /* @var $serviceManager \Zend\ServiceManager\ServiceManager */

        $application    = $event->getApplication();
        $serviceManager = $application->getServiceManager();

        $application->getEventManager()
                    ->getSharedManager()
                    ->attach(
                        'Zend\Mvc\Application',
                        MvcEvent::EVENT_DISPATCH_ERROR,
                        array( $this, 'onDispatchError' )
                    );

        if ( $serviceManager->has( 'RedirectToDomain' ) )
        {
            $redirect   = $serviceManager->get( 'RedirectToDomain' );
            $url        = 'http://' . $redirect->getDomain();
            $response   = $event->getResponse();
            /* @var $response \Zend\Http\PhpEnvironment\Response */

            if ( $redirect->getUsePath() )
            {
                $request = $event->getRequest();
                $url    .= $request->getRequestUri();
            }

            $response->setStatusCode( 302 )
                     ->getHeaders()
                     ->addHeaders( array(
                         'Location'          => $url,
                         'X-Redirect-Reason' => $redirect->getReason(),
                     ) );

            $this->response = $response->setContent( sprintf(
                '<meta http-equiv="refresh" content="0;url=%1$s"><a href="%1$s">%1$s</a>',
                htmlspecialchars( $url )
            ) );
        }
    }

    /**
     * Exception handler
     *
     * @param   Exception   $exception
     * @return  void
     */
    public function exceptionHandler( Exception $exception )
    {
        if ( $this->serviceLocator &&
             $this->serviceLocator instanceof ServiceLocatorInterface &&
             $this->serviceLocator->has( 'Zork\Log\LoggerManager' ) )
        {
            try
            {
                /* @var $loggerManager \Zork\Log\LoggerManager */
                $loggerManager = $this->serviceLocator->get( 'Zork\Log\LoggerManager' );

                if ( $loggerManager->hasLogger( 'exception' ) )
                {
                    $loggerManager->getLogger( 'exception' )
                                  ->crit( '<pre>' . $exception . '</pre>' . PHP_EOL );
                }
            }
            catch ( Exception $ex )
            {
                // do nothing
            }
        }
    }

    /**
     * Listen to the dispatch.error event
     *
     * @param \Zend\Mvc\MvcEvent $event
     */
    public function onDispatchError( MvcEvent $event )
    {
        /* @var $exception \Exception  */
        $exception = $event->getParam( 'exception' );

        if ( $exception && $exception instanceof Exception )
        {
            $this->exceptionHandler( $exception );
        }

        if ( $this->response )
        {
            $event->stopPropagation();
            return $this->response;
        }
    }

    /**
     * General dispatch listener
     *
     * @param \Zend\Mvc\MvcEvent $event
     */
    public function onDispatch( MvcEvent $event )
    {
        if ( $this->response )
        {
            $event->stopPropagation();
            return $this->response;
        }

        $routeMatch = $event->getRouteMatch();
        $sm         = $event->getApplication()
                            ->getServiceManager();

        // Set current timezone, when first get
        $sm->get( 'Timezone' );

        if ( $routeMatch )
        {
            $locale = $routeMatch->getParam( 'locale' );
        }

        if ( ! $locale )
        {
            $header = $event->getRequest()
                            ->getHeader( 'Accept-Language' );

            if ( $header )
            {
                $availables = null;
                $controller = $event->getController();

                if ( $controller instanceof LocaleSelectorInterface )
                {
                    $availables = $controller->getAvailableLocales();
                }

                $locale = $sm->get( 'Locale' )
                             ->acceptFromHttp( $header->getFieldValue(),
                                               $availables );
            }
        }

        if ( $locale )
        {
            $sm->get( 'Locale' )
               ->setCurrent( $locale );
        }
    }

    /**
     * Expected to return \Zend\ServiceManager\Config object or array to
     * seed such an object.
     *
     * @return array|\Zend\ServiceManager\Config
     */
    public function getServiceConfig()
    {
        return array(
            'instances' => array(
                'moduleManager' => $this->moduleManager,
            ),
            'aliases' => array(
                'Zend\ModuleManager\ModuleManagerInterface' => 'moduleManager',
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
        $moduleManager  = $this->moduleManager;
        $serviceLocator = $this->serviceLocator;

        return array(
            'invokables'    => array(
                'layout'                    => 'Zork\View\Helper\Layout',
                'messenger'                 => 'Zork\View\Helper\Messenger',
                'htmlTag'                   => 'Zork\View\Helper\HtmlTag',
                'headTitle'                 => 'Zork\View\Helper\HeadTitle',
                'openGraph'                 => 'Zork\View\Helper\OpenGraph',
                'date'                      => 'Zork\I18n\View\Helper\Date',
                'dateTime'                  => 'Zork\I18n\View\Helper\DateTime',
                'relativeTime'              => 'Zork\I18n\View\Helper\RelativeTime',
                'form'                      => 'Zork\Form\View\Helper\Form',
                'formRadio'                 => 'Zork\Form\View\Helper\FormRadio',
                'formSelect'                => 'Zork\Form\View\Helper\FormSelect',
                'formCheckbox'              => 'Zork\Form\View\Helper\FormCheckbox',
                'formPassword'              => 'Zork\Form\View\Helper\FormPassword',
                'formRadioGroup'            => 'Zork\Form\View\Helper\FormRadioGroup',
                'formHashCollection'        => 'Zork\Form\View\Helper\FormHashCollection',
                'formMultiCheckbox'         => 'Zork\Form\View\Helper\FormMultiCheckbox',
                'formMultiCheckboxGroup'    => 'Zork\Form\View\Helper\FormMultiCheckboxGroup',
                'captcha/regeneratable'     => 'Zork\Form\View\Helper\Captcha\Regeneratable',
                'markdown'                  => 'Grid\Core\View\Helper\Markdown',
            ),
            'factories'         => array(
                'config'            => function () use ( $serviceLocator ) {
                    return new \Zork\View\Helper\Config( $serviceLocator->get( 'Config' ) );
                },
                'viewWidget'        => function () use ( $serviceLocator ) {
                    $config = $serviceLocator->get( 'Config' );
                    $widgetConfig = empty( $config['view_widgets'] ) ? array() : $config['view_widgets'];
                    return \Zork\View\Helper\ViewWidget::factory( $serviceLocator, $widgetConfig );
                },
                'domain'            => function () use ( $serviceLocator ) {
                    return new \Zork\View\Helper\Domain(
                        $serviceLocator->get( 'Zork\Db\SiteInfo' )
                    );
                },
                'locale'            => function () use ( $serviceLocator ) {
                    $locale = $serviceLocator->get( 'Locale' );
                    return new \Zork\View\Helper\Locale( $locale );
                },
                'adminLocale'       => function () use ( $serviceLocator ) {
                    return $serviceLocator->get( 'AdminLocale' );
                },
                'headDefaults'      => function () use ( $serviceLocator ) {
                    $config = $serviceLocator->get( 'Config' );
                    $config = $config['view_manager'];

                    if ( isset( $config['head_defaults'] ) )
                    {
                        $definitions = $config['head_defaults'];
                    }
                    else
                    {
                        $definitions = array();
                    }

                    return new \Zork\View\Helper\HeadDefaults( $definitions );
                },
                'appService'        => function () use ( $serviceLocator ) {
                    return new View\Helper\AppService( $serviceLocator );
                },
                'isModuleLoaded'    => function () use ( $moduleManager ) {
                    return new View\Helper\IsModuleLoaded( $moduleManager );
                },
                'siteInfo'          => function () use ( $serviceLocator ) {
                    return new View\Helper\SiteInfo(
                        $serviceLocator->get( 'Zork\Db\SiteInfo' )
                    );
                },
                'uploads'           => function () use ( $serviceLocator ) {
                    return new View\Helper\Uploads(
                        $serviceLocator->get( 'Zork\Db\SiteInfo' )
                                       ->getSchema()
                    );
                },
                'rowSet'            => function () use ( $serviceLocator ) {
                    return new View\Helper\RowSet(
                        $serviceLocator->get( 'Request' )
                    );
                },
            ),
        );
    }

}
