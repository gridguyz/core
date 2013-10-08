<?php

namespace Grid\Paragraph\Mvc\View\Http;

use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\AbstractListenerAggregate;
use Grid\Paragraph\View\Model\MetaContent;
use Grid\Paragraph\Model\Paragraph\MiddleLayoutModel;
use Grid\Paragraph\Model\Paragraph\Structure\LayoutAwareInterface;

/**
 * InjectMetaContentListener
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class InjectMetaContentListener extends AbstractListenerAggregate
{

    /**
     * @const string
     */
    const SHARED_EVENT_ID = 'Zend\Stdlib\DispatchableInterface';

    /**
     * @var \Paragraph\Model\Paragraph\MiddleLayoutModel
     */
    protected $middleLayoutModel;

    /**
     * Get middle-layout-model
     *
     * @return \Paragraph\Model\Paragraph\MiddleLayoutModel
     */
    public function getMiddleLayoutModel()
    {
        return $this->middleLayoutModel;
    }

    /**
     * Set middle-layout-model
     *
     * @param   \Paragraph\Model\Paragraph\MiddleLayoutModel $paragraphMiddleLayoutModel
     * @return  \Paragraph\View\Helper\MetaContent
     */
    public function setMiddleLayoutModel( MiddleLayoutModel $paragraphMiddleLayoutModel )
    {
        $this->middleLayoutModel = $paragraphMiddleLayoutModel;
        return $this;
    }

    /**
     * Constructor
     *
     * @param \Paragraph\Model\Paragraph\MiddleLayoutModel $paragraphMiddleLayoutModel
     */
    public function __construct( MiddleLayoutModel $paragraphMiddleLayoutModel )
    {
        $this->setMiddleLayoutModel( $paragraphMiddleLayoutModel );
    }

    /**
     * {@inheritDoc}
     */
    public function attach( EventManagerInterface $events )
    {
        $priority       = -95;
        $method         = array( $this, 'injectMetaContent' );
        $sharedEvents   = $events->getSharedManager();

        $this->listeners[] = $sharedEvents->attach(
            static::SHARED_EVENT_ID,
            MvcEvent::EVENT_DISPATCH,
            $method,
            $priority
        );

        $this->listeners[] = $sharedEvents->attach(
            static::SHARED_EVENT_ID,
            MvcEvent::EVENT_DISPATCH_ERROR,
            $method,
            $priority
        );

        $this->listeners[] = $sharedEvents->attach(
            static::SHARED_EVENT_ID,
            MvcEvent::EVENT_RENDER_ERROR,
            $method,
            $priority
        );
    }

    /**
     * {@inheritDoc}
     */
    public function detach( EventManagerInterface $events )
    {
        $sharedEvents = $events->getSharedManager();

        foreach ( $this->listeners as $index => $callback )
        {
            if ( $sharedEvents->detach( static::SHARED_EVENT_ID, $callback ) )
            {
                unset( $this->listeners[$index] );
            }
        }

        parent::detach( $events );
    }

    /**
     * Insert the meta content into the view model
     *
     * @param   MvcEvent    $event
     * @return  void
     */
    public function injectMetaContent( MvcEvent $event )
    {
        $result = $event->getResult();

        if ( ! $result instanceof MetaContent )
        {
            return;
        }

        $middle         = $this->getMiddleLayoutModel();
        $paragraph      = $middle->getParagraphModel();
        $renderList     = $paragraph->findRenderList( $result->getName() );

        if ( empty( $renderList ) )
        {
            return;
        }

        $meta = reset( $renderList )[1];

        if ( empty( $meta ) )
        {
            return;
        }

        $application    = $event->getApplication();
        $serviceManager = $application->getServiceManager();
        $allowOverride  = $serviceManager->getAllowOverride();

        if ( ! $allowOverride )
        {
            $serviceManager->setAllowOverride( true );
        }

        $serviceManager->setService( 'RenderedContent', $meta );

        if ( ! $allowOverride )
        {
            $serviceManager->setAllowOverride( false );
        }

        if ( $meta instanceof LayoutAwareInterface )
        {
            $event->getViewModel()
                  ->setVariable(
                        'middleLayout',
                        $middle->findMiddleParagraphLayoutById(
                            $meta->getLayoutId()
                        )
                    );
        }

        $model = new ViewModel( array(
            'paragraphRenderList' => $renderList,
        ) );

        $model->setTemplate( 'grid/paragraph/render/paragraph' );
        $model->addChild( $result, 'content' );
        $event->setResult( $model );
    }

}
