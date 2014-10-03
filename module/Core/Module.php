<?php

namespace Grid\Core;

use Exception;
use Zend\Mvc\MvcEvent;
use Zork\Stdlib\ModuleAbstract;
use Zend\EventManager\EventInterface;
use Zend\Http\Request as HttpRequest;
use Zend\Http\Response as HttpResponse;
use Zend\ModuleManager\ModuleManagerInterface;
use Zork\Mvc\View\Http\InjectTemplateListener;
use Zork\Mvc\Controller\LocaleSelectorInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ModuleManager\Feature\InitProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\ViewHelperProviderInterface;
use Zend\ModuleManager\Feature\ControllerPluginProviderInterface;

/**
 * Grid\Core\Module
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Module extends ModuleAbstract
          implements InitProviderInterface,
                     ServiceProviderInterface,
                     BootstrapListenerInterface,
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
     * Constructor
     */
    public function __construct()
    {
        set_exception_handler( array( $this, 'exceptionHandler' ) );
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        restore_exception_handler();
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

        $response = $event->getResponse();

        if ( $response instanceof HttpResponse &&
             $serviceManager->has( 'RedirectToDomain' ) )
        {
            $redirect   = $serviceManager->get( 'RedirectToDomain' );
            $path       = '';
            $request    = $event->getRequest();

            if ( $request instanceof HttpRequest && $redirect->getUsePath() )
            {
                $path = $request->getRequestUri();
            }

            $url = $redirect->getUrl( $path );

            $response->setStatusCode( 302 )
                     ->getHeaders()
                     ->addHeaders( array(
                         'Location'          => $url,
                         'X-Redirect-Reason' => $redirect->getReason(),
                     ) );

            $this->response = $response->setContent( sprintf(
                '<meta http-equiv="refresh" content="0;url=%1$s">'
                    . '<a href="%1$s">%1$s</a>',
                htmlspecialchars( $url )
            ) );
        }
    }

    /**
     * Exception handler
     *
     * @param   Exception   $exception
     * @param   bool        $display
     * @return  void
     */
    public function exceptionHandler( Exception $exception, $display = true )
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

        if ( $display )
        {
            if ( PHP_SAPI == 'cli' )
            {
                $write  = (string) $exception;
                $stderr = @ fopen( 'php://stderr', 'a' );

                if ( is_resource( $stderr ) )
                {
                    fwrite( $stderr, $write );
                    fclose( $stderr );
                }
                else
                {
                    echo $write;
                }
            }
            else
            {
                $status = '500 Internal server error';
                @ header( $_SERVER['SERVER_PROTOCOL'] . ' ' . $status );
                @ header( 'Status: ' . $status );
                @ header( 'Expires: -1' );
                @ header( 'Pragma: no-cache' );
                @ header( 'Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0' );
                @ header( 'Connection: close' );
                @ header( 'Content-type: text/html; charset=utf-8' );
                include __DIR__ . '/view/error/500.phtml';
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
            $this->exceptionHandler( $exception, false );
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
            $request = $event->getRequest();

            if ( $request instanceof HttpRequest )
            {
                $header = $request->getHeader( 'Accept-Language' );

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
     * Get session manager instance
     *
     * @return  \Zend\Session\ManagerInterface
     */
    public function getSessionManager()
    {
        return $this->serviceLocator
                    ->get( 'Zend\Session\ManagerInterface' );
    }

    /**
     * Get `mimicSiteInfos` controller plugin instance
     *
     * @return  Controller\Plugin\MimicSiteInfos
     */
    public function getMimicSiteInfosControllerPlugin()
    {
        return new Controller\Plugin\MimicSiteInfos(
            $this->serviceLocator
        );
    }

    /**
     * Expected to return \Zend\ServiceManager\Config object or array to
     * seed such an object.
     *
     * @return array|\Zend\ServiceManager\Config
     */
    public function getControllerPluginConfig()
    {
        return array(
            'aliases'   => array(
                'Zend\Session\SessionManager'   => 'sessionManager',
                'Zend\Session\ManagerInterface' => 'sessionManager',
            ),
            'factories' => array(
                'sessionManager' => array( $this, 'getSessionManager' ),
                'mimicSiteInfos' => array( $this, 'getMimicSiteInfosControllerPlugin' ),
            ),
        );
    }

    /**
     * Get `config` view-helper instance
     *
     * @return  \Zork\View\Helper\Config
     */
    public function getConfigViewHelper()
    {
        return new \Zork\View\Helper\Config(
            $this->serviceLocator
                 ->get( 'Config' )
        );
    }

    /**
     * Get `domain` view-helper instance
     *
     * @return  \Zork\View\Helper\Domain
     */
    public function getDomainViewHelper()
    {
        return new \Zork\View\Helper\Domain(
            $this->serviceLocator
                 ->get( 'Zork\Db\SiteInfo' )
        );
    }

    /**
     * Get `locale` view-helper instance
     *
     * @return  \Zork\View\Helper\Locale
     */
    public function getLocaleViewHelper()
    {
        return new \Zork\View\Helper\Locale(
            $this->serviceLocator
                 ->get( 'Locale' )
        );
    }

    /**
     * Get `adminLocale` view-helper instance
     *
     * @return  \Zork\Mvc\AdminLocale
     */
    public function getAdminLocaleViewHelper()
    {
        return $this->serviceLocator
                    ->get( 'AdminLocale' );
    }

    /**
     * Get `appService` view-helper instance
     *
     * @return  View\Helper\AppService
     */
    public function getAppServiceViewHelper()
    {
        return new View\Helper\AppService( $this->serviceLocator );
    }

    /**
     * Get `authentication` view-helper instance
     *
     * @return  View\Helper\Authentication
     */
    public function getAuthenticationViewHelper()
    {
        return new View\Helper\Authentication(
            $this->serviceLocator
                 ->get( 'Zend\Authentication\AuthenticationService' )
        );
    }

    /**
     * Get `viewWidget` view-helper instance
     *
     * @return  View\Helper\ViewWidget
     */
    public function getViewWidgetViewHelper()
    {
        $config = $this->serviceLocator
                       ->get( 'Config' );

        return View\Helper\ViewWidget::factory(
            $this->serviceLocator,
            empty( $config['view_widgets'] ) ? array() : $config['view_widgets']
        );
    }
    
    
    /**
     * Get `BeforeContentWidget` view-helper instance
     *
     * @return \Grid\Core\View\Helper\BeforeContentWidget
     */
    public function getBeforeContentWidgetViewHelper()
    {
        return View\Helper\BeforeContentWidget::factory(
            $this->serviceLocator
        );
    }
    
    /**
     * Get `isModuleLoaded` view-helper instance
     *
     * @return  View\Helper\IsModuleLoaded
     */
    public function getIsModuleLoadedViewHelper()
    {
        return new View\Helper\IsModuleLoaded( $this->moduleManager );
    }

    /**
     * Get `siteInfo` view-helper instance
     *
     * @return  View\Helper\SiteInfo
     */
    public function getSiteInfoViewHelper()
    {
        return new View\Helper\SiteInfo(
            $this->serviceLocator
                 ->get( 'Zork\Db\SiteInfo' )
        );
    }

    /**
     * Get `uploads` view-helper instance
     *
     * @return  View\Helper\Uploads
     */
    public function getUploadsViewHelper()
    {
        return new View\Helper\Uploads(
            $this->serviceLocator
                 ->get( 'Zork\Db\SiteInfo' )
                 ->getSchema()
        );
    }

    /**
     * Get `rowSet` view-helper instance
     *
     * @return  View\Helper\RowSet
     */
    public function getRowSetViewHelper()
    {
        return new View\Helper\RowSet(
            $this->serviceLocator
                 ->get( 'Request' )
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
        return array(
            'invokables'            => array(
                'layout'                    => 'Zork\View\Helper\Layout',
                'messenger'                 => 'Zork\View\Helper\Messenger',
                'htmlTag'                   => 'Zork\View\Helper\HtmlTag',
                'headTitle'                 => 'Zork\View\Helper\HeadTitle',
                'openGraph'                 => 'Zork\View\Helper\OpenGraph',
                'escapeCss'                 => 'Zork\View\Helper\EscapeCss',
                'escapeHtml'                => 'Zork\View\Helper\EscapeHtml',
                'escapeHtmlAttr'            => 'Zork\View\Helper\EscapeHtmlAttr',
                'escapeJs'                  => 'Zork\View\Helper\EscapeJs',
                'escapeUrl'                 => 'Zork\View\Helper\EscapeUrl',
                'date'                      => 'Zork\I18n\View\Helper\Date',
                'dateTime'                  => 'Zork\I18n\View\Helper\DateTime',
                'relativeTime'              => 'Zork\I18n\View\Helper\RelativeTime',
                'form'                      => 'Zork\Form\View\Helper\Form',
                'formRadio'                 => 'Zork\Form\View\Helper\FormRadio',
                'formSelect'                => 'Zork\Form\View\Helper\FormSelect',
                'formCheckbox'              => 'Zork\Form\View\Helper\FormCheckbox',
                'formPassword'              => 'Zork\Form\View\Helper\FormPassword',
                'formRadioGroup'            => 'Zork\Form\View\Helper\FormRadioGroup',
                'formElement'               => 'Zork\Form\View\Helper\FormElement',
                'formFieldset'              => 'Zork\Form\View\Helper\FormFieldset',
                'formCollection'            => 'Zork\Form\View\Helper\FormCollection',
                'formMultiCheckbox'         => 'Zork\Form\View\Helper\FormMultiCheckbox',
                'formMultiCheckboxGroup'    => 'Zork\Form\View\Helper\FormMultiCheckboxGroup',
                'captcha/regeneratable'     => 'Zork\Form\View\Helper\Captcha\Regeneratable',
                'markdown'                  => 'Grid\Core\View\Helper\Markdown',
            ),
            'aliases'               => array(
                'Zend\Session\SessionManager'   => 'sessionManager',
                'Zend\Session\ManagerInterface' => 'sessionManager',
            ),
            'factories'             => array(
                'sessionManager'    => array( $this, 'getSessionManager' ),
                'config'            => array( $this, 'getConfigViewHelper' ),
                'domain'            => array( $this, 'getDomainViewHelper' ),
                'locale'            => array( $this, 'getLocaleViewHelper' ),
                'adminLocale'       => array( $this, 'getAdminLocaleViewHelper' ),
                'appService'        => array( $this, 'getAppServiceViewHelper' ),
                'authentication'    => array( $this, 'getAuthenticationViewHelper' ),
                'viewWidget'        => array( $this, 'getViewWidgetViewHelper' ),
                'isModuleLoaded'    => array( $this, 'getIsModuleLoadedViewHelper' ),
                'siteInfo'          => array( $this, 'getSiteInfoViewHelper' ),
                'uploads'           => array( $this, 'getUploadsViewHelper' ),
                'rowSet'            => array( $this, 'getRowSetViewHelper' ),
                'beforeContentWidget' => array( $this, 'getBeforeContentWidgetViewHelper' ),
            ),
        );
    }

}
