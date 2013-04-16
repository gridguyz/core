<?php

namespace Grid\Paragraph\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Grid\Paragraph\Model\Paragraph\MiddleLayoutModel;
use Grid\Paragraph\Model\Paragraph\Structure\LayoutAwareInterface;

/**
 * MetaContent
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class MetaContent extends AbstractHelper
{

    /**
     * @var \Paragraph\Model\Paragraph\MiddleLayoutModel
     */
    protected $middleLayoutModel;

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
     * Get middle-layout-model
     *
     * @return \Paragraph\Model\Paragraph\MiddleLayoutModel
     */
    public function getMiddleLayoutModel()
    {
        return $this->middleLayoutModel;
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
     * Invoking the helper
     *
     * @param   string $name
     * @param   string $content
     * @return  \Paragraph\View\Helper\MetaContent
     */
    public function __invoke( $name = null, $content = '' )
    {
        if ( ! empty( $name ) )
        {
            return $this->renderMetaContent( $name, $content );
        }

        return $this;
    }

    /**
     * Set meta-content
     *
     * @param   string $name
     * @param   string $content
     * @return  string
     */
    public function renderMetaContent( $name, $content = '' )
    {
        $middle     = $this->getMiddleLayoutModel();
        $paragraph  = $middle->getParagraphModel();
        $renderList = $paragraph->findRenderList( $name );
        $meta       = reset( $renderList )[1];

        if ( empty( $meta ) )
        {
            return $content;
        }

        $sm = $this->getView()->appService();
        $ao = $sm->getAllowOverride();

        if ( ! $ao )
        {
            $sm->setAllowOverride( true );
        }

        $sm->setService( 'RenderedContent', $meta );

        if ( ! $ao )
        {
            $sm->setAllowOverride( false );
        }

        if ( $meta instanceof LayoutAwareInterface )
        {
            $this->getView()
                 ->plugin( 'layout' )
                 ->setMiddleLayout(
                     $middle->findMiddleParagraphLayoutById(
                         $meta->getLayoutId()
                     )
                 );
        }

        return $this->getView()
                    ->render( 'grid/paragraph/render/paragraph', array(
                        'paragraphRenderList'  => $renderList,
                        'content'              => $content,
                    ) );
    }

}
