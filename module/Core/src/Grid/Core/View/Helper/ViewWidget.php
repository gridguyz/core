<?php

namespace Grid\Core\View\Helper;

use Zend\Stdlib\PriorityQueue;
use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * Grid\Core\View\Helper\ViewWidget
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class ViewWidget extends AbstractHelper
              implements ServiceLocatorAwareInterface
{

    use ServiceLocatorAwareTrait
    {
        ServiceLocatorAwareTrait::setServiceLocator as protected setExactServiceLocator;
    }

    /**
     * Set service locator
     *
     * @param   ServiceLocatorInterface $serviceLocator
     * @return  AppService
     */
    public function setServiceLocator( ServiceLocatorInterface $serviceLocator )
    {
        if ( null === $this->serviceLocator )
        {
            $this->setExactServiceLocator( $serviceLocator );
        }

        return $this;
    }

    /**
     * @const string
     */
    const WIDGET_INTERFACE = 'Grid\Core\View\Widget\WidgetInterface';

    /**
     * @var \Zend\Stdlib\PriorityQueue[]
     */
    protected $widgets = array();

    /**
     * Add a widget
     *
     * @param   string      $widget
     * @param   string      $service
     * @param   int|null    $priority
     * @return  ViewWidget
     */
    public function addWidget( $widget, $service, $priority = null )
    {
        if ( ! isset( $this->widgets[$widget] ) )
        {
            $this->widgets[$widget] = new PriorityQueue;
        }

        if ( ! is_a( $service, static::WIDGET_INTERFACE, true ) )
        {
            throw new \InvalidArgumentException( sprintf(
                '%s: $service must implement "%s"',
                __METHOD__,
                static::WIDGET_INTERFACE
            ) );
        }

        $this->widgets[$widget]->insert(
            $service,
            null === $priority ? 1 : (int) $priority
        );

        return $this;
    }

    /**
     * Add widgets
     *
     * @param   string              $widget
     * @param   \Traversable|array  $partials
     * @return  ViewWidget
     */
    public function addWidgets( $widget, $services )
    {
        foreach ( $services as $key => $description )
        {
            if ( null === $description || is_scalar( $description ) )
            {
                $service    = $key;
                $priority   = null === $description ? null : (int) $description;
            }
            else
            {
                if ( $description instanceof \Traversable )
                {
                    $description = iterator_to_array( $description );
                }
                else if ( ! $description instanceof \ArrayAccess )
                {
                    $description = (array) $description;
                }

                if ( ! isset( $description['service'] ) )
                {
                    continue;
                }

                $service = $description['service'];

                if ( isset( $description['priority'] ) )
                {
                    $priority = (int) $description['priority'];
                }
                else
                {
                    $priority = null;
                }
            }

            $this->addWidget( $widget, $service, $priority );
        }

        return $this;
    }

    /**
     * Constructor
     *
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     */
    public function __construct( ServiceLocatorInterface $serviceLocator )
    {
        $this->setServiceLocator( $serviceLocator );
    }

    /**
     * Factory method
     *
     * @param   \Zend\ServiceManager\ServiceLocatorInterface    $serviceLocator
     * @param   \Traversable|array                              $widgets
     * @return  ViewWidget
     */
    public static function factory( ServiceLocatorInterface $serviceLocator, $widgets = array() )
    {
        $viewWidget = new static( $serviceLocator );

        foreach ( $widgets as $widget => $services )
        {
            $viewWidget->addWidgets( $widget, $services );
        }

        return $viewWidget;
    }

    /**
     * Render widgets
     *
     * @param   string              $widget
     * @param   string              $content
     * @param   \Traversable|array  $params
     * @return  string
     */
    public function render( $widget, $content = '', $params = array() )
    {
        $view    = $this->getView();
        $content = (string) $content;

        if ( empty( $view ) || empty( $widget ) ||
             ! array_key_exists( $widget, $this->widgets ) )
        {
            return $content;
        }

        if ( $params instanceof \Traversable )
        {
            $params = iterator_to_array( $params );
        }
        else if ( ! is_array( $params ) )
        {
            $params = (array) $params;
        }

        $serviceLocator = $this->getServiceLocator();

        foreach ( $this->widgets[$widget] as $service )
        {
            if ( is_scalar( $service ) )
            {
                $service = $serviceLocator->get( $service );
            }

            if ( $service instanceof ServiceLocatorAwareInterface )
            {
                $service->setServiceLocator( $serviceLocator );
            }

            $content = $service->render( $view, $content, $params );
        }

        return $content;
    }

    /**
     * Invokable helper
     *
     * @param   string|null         $widget
     * @param   string              $content
     * @param   \Traversable|array  $params
     * @return  string|ViewWidget
     */
    public function __invoke( $widget = null, $content = '', $params = array() )
    {
        if ( null === $widget )
        {
            return $this;
        }
        else
        {
            return $this->render( $widget, $content, $params );
        }
    }

}
