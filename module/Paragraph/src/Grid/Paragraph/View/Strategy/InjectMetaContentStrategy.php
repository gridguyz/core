<?php

namespace Grid\Paragraph\View\Strategy;

use Zend\View\ViewEvent;
use Zend\View\Renderer\PhpRenderer;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\AbstractListenerAggregate;
use Grid\Paragraph\View\Model\MetaContent;
use Grid\Paragraph\Model\Paragraph\MiddleLayoutModel;
use Grid\Paragraph\Model\Paragraph\Structure\LayoutAwareInterface;

/**
 * InjectMetaContentStrategy
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class InjectMetaContentStrategy extends AbstractListenerAggregate
{

    /**
     * @var PhpRenderer
     */
    protected $renderer;

    /**
     * @var MiddleLayoutModel
     */
    protected $middleLayoutModel;

    /**
     * Get renderer
     *
     * @return  View
     */
    public function getRenderer()
    {
        return $this->renderer;
    }

    /**
     * Set renderer
     *
     * @param   PhpRenderer $renderer
     * @return  InjectMetaContentStrategy
     */
    public function setRenderer( PhpRenderer $renderer )
    {
        $this->renderer = $renderer;
        return $this;
    }

    /**
     * Get middle-layout-model
     *
     * @return  MiddleLayoutModel
     */
    public function getMiddleLayoutModel()
    {
        return $this->middleLayoutModel;
    }

    /**
     * Set middle-layout-model
     *
     * @param   MiddleLayoutModel   $paragraphMiddleLayoutModel
     * @return  InjectMetaContentStrategy
     */
    public function setMiddleLayoutModel( MiddleLayoutModel $paragraphMiddleLayoutModel )
    {
        $this->middleLayoutModel = $paragraphMiddleLayoutModel;
        return $this;
    }

    /**
     * Constructor
     *
     * @param  PhpRenderer          $renderer
     * @param  MiddleLayoutModel    $paragraphMiddleLayoutModel
     */
    public function __construct( PhpRenderer        $renderer,
                                 MiddleLayoutModel  $paragraphMiddleLayoutModel )
    {
        $this->setRenderer( $renderer )
             ->setMiddleLayoutModel( $paragraphMiddleLayoutModel );
    }

    /**
     * {@inheritDoc}
     */
    public function attach( EventManagerInterface $events, $priority = -1 )
    {
        $this->listeners[] = $events->attach(
            ViewEvent::EVENT_RENDERER,
            array($this, 'selectRenderer'),
            $priority
        );

        $this->listeners[] = $events->attach(
            ViewEvent::EVENT_RESPONSE,
            array($this, 'injectResponse'),
            $priority
        );
    }

    /**
     * Select the PhpRenderer; typically, this will be registered last or at
     * low priority.
     *
     * @param   ViewEvent   $event
     * @return  PhpRenderer
     */
    public function selectRenderer( ViewEvent $event )
    {
        return $this->renderer;
    }

    /**
     * Populate the response object from the View
     *
     * Populates the content of the response object from the view rendering
     * results.
     *
     * @param   ViewEvent $event
     * @return  void
     */
    public function injectResponse( ViewEvent $event )
    {
        $renderer = $event->getRenderer();

        if ( $renderer !== $this->renderer )
        {
            return;
        }

        $model = $event->getModel();

        if ( $model instanceof MetaContent )
        {
            $result     = $event->getResult();
            $response   = $event->getResponse();
            $middle     = $this->getMiddleLayoutModel();
            $paragraph  = $middle->getParagraphModel();
            $renderList = $paragraph->findRenderList( $model->getName() );

            if ( empty( $renderList ) )
            {
                return;
            }

            $meta = reset( $renderList )[1];

            if ( empty( $meta ) )
            {
                return;
            }

            $serviceManager = $renderer->plugin( 'appService' );
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
                $renderer->plugin( 'layout' )
                         ->setMiddleLayout(
                             $middle->findMiddleParagraphLayoutById(
                                 $meta->getLayoutId()
                             )
                         );
            }

            $response->setContent( $renderer->render(
                'grid/paragraph/render/paragraph',
                array(
                    'paragraphRenderList'  => $renderList,
                    'content'              => $result,
                )
            ) );
        }
    }

}
