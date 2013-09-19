<?php

namespace Grid\Core\View\Helper;

use Zend\Stdlib\PriorityQueue;
use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * Grid\Core\View\Helper\ViewWidget
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class ViewWidget extends AbstractHelper
              implements ServiceLocatorAwareInterface
{

    /**
     * @var \Zend\ServiceManager\ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * @var \Zend\Stdlib\PriorityQueue[]
     */
    protected $widgets = array();

    /**
     * @return \Zend\ServiceManager\ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return \Core\View\Helper\AppService
     */
    public function setServiceLocator( ServiceLocatorInterface $serviceLocator )
    {
        if ( null === $this->serviceLocator )
        {
            $this->serviceLocator = $serviceLocator;
        }

        return $this;
    }

    /**
     * Add widgets
     *
     * @param   string              $widget
     * @param   \Traversable|array  $partials
     * @return  ViewWidget
     */
    protected function addWidgets( $widget, $partials )
    {
        if ( ! isset( $this->widgets[$widget] ) )
        {
            $this->widgets[$widget] = new PriorityQueue;
        }

        foreach ( $partials as $partialDescription )
        {
            $priority = null;

            if ( $partialDescription instanceof \Traversable )
            {
                $partialDescription = iterator_to_array( $partialDescription );
            }
            else if ( ! is_array( $partialDescription ) )
            {
                $partialDescription = (array) $partialDescription;
            }

            if ( empty( $partialDescription['partial'] ) )
            {
                continue;
            }

            if ( array_key_exists( 'priority', $partialDescription ) )
            {
                $priority = $partialDescription['priority'];
                unset( $partialDescription['priority'] );
            }

            if ( array_key_exists( 'services', $partialDescription ) )
            {
                if ( $partialDescription['services'] instanceof \Traversable )
                {
                    $partialDescription['services'] = iterator_to_array( $partialDescription['services'] );
                }
                else if ( ! is_array( $partialDescription['services'] ) )
                {
                    $partialDescription['services'] = (array) $partialDescription['services'];
                }
            }
            else
            {
                $partialDescription['services'] = array();
            }

            $this->widgets[$widget]->insert(
                $partialDescription,
                null === $priority ? 1 : (int) $priority
            );
        }

        return $this;
    }

    /**
     * Add a widget
     *
     * @param   string  $widget
     * @param   string  $partial
     * @param   array   $services
     * @param   int     $priority
     * @return  ViewWidget
     */
    public function addWidget( $widget, $partial, $services = array(), $priority = null )
    {
        return $this->addWidgets( $widget, array( array(
            'partial'   => (string) $partial,
            'services'  => $services,
            'priority'  => $priority,
        ) ) );
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
     */
    public static function factory( ServiceLocatorInterface $serviceLocator, $widgets = array() )
    {
        $viewWidget = new static( $serviceLocator );

        foreach ( $widgets as $widget => $partials )
        {
            $viewWidget->addWidgets( $widget, $partials );
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

        foreach ( $this->widgets[$widget] as $partialDescription )
        {
            $services = array();

            foreach ( $partialDescription['services'] as $serviceAlias => $serviceName )
            {
                $services[$serviceAlias] = $serviceLocator->get( $serviceName );
            }

            $content = $view->render(
                $partialDescription['partial'],
                array_merge(
                    $services,
                    $params,
                    array(
                        'content'   => $content,
                        'services'  => $services,
                        'params'    => $params,
                    )
                )
            );
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
