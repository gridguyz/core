<?php 
namespace Grid\Core\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Stdlib\PriorityQueue;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Grid\Core\View\BeforeContentWidget\BeforeContentWidgetInterface;

/**
 * BeforeContentWidget
 * 
 * @author Kristof Matos <kristof.matos@megaweb.hu>
 */
class BeforeContentWidget extends AbstractHelper
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
     * @var \Zend\Stdlib\PriorityQueue
     */
    protected $widgets = null;

    /**
     * Constructor
     *
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     */
    public function __construct( ServiceLocatorInterface $serviceLocator )
    {
        $this->setServiceLocator( $serviceLocator );
        $this->widgets = new PriorityQueue();
    }

    /**
     * Factory method
     * 
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return \Grid\Core\View\Helper\BeforeContentWidget
     */
    public static function factory( ServiceLocatorInterface $serviceLocator )
    {
        $beforeConzentWidget = new static( $serviceLocator );
        return $beforeConzentWidget;
    }

    /**
     * 
     * @param \Grid\Core\View\BeforeContentWidget\BeforeContentWidgetInterface $widget
     * @return \Grid\Core\View\Helper\BeforeContentWidget
     */
    public function addWidget( BeforeContentWidgetInterface $widget )
    {
        $this->widgets->insert( $widget, (int) $widget->getPriority() );
        return $this;
    }
    
    /**
     * Render widgets
     *
     * @param   string              $widget
     * @param   string              $content
     * @param   \Traversable|array  $params
     * @return  string
     */
    public function toString()
    {
        $serviceLocator = $this->getServiceLocator();
        
        $config = $serviceLocator->get('Config');
        $configWidgets = $config['modules']['Grid\Core']['beforeContentWidget'];
        foreach ( $configWidgets as $serviceClass )
        {
            $service = $serviceLocator->get( $serviceClass );
            
            if ( $service instanceof ServiceLocatorAwareInterface )
            {
                $service->setServiceLocator( $serviceLocator );
            }
            
            $this->addWidget($service);
        }
        
        $view    = $this->getView();
        $content = '';

        /* @var $widget \Grid\Core\View\BeforeContentWidget\BeforeContentWidgetInterface */
        foreach ( $this->widgets as $widget )
        {
            $content .= $widget->render($view);
        }

        return $content;
    }

    /**
     * Invokable helper
     * 
     * @return \Grid\Core\View\Helper\BeforeContentWidget
     */
    public function __invoke()
    {
        return $this;
    }

}
