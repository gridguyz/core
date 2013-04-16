<?php

namespace Grid\Paragraph\Controller\Plugin;

use Zend\Mvc\Exception;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Grid\Paragraph\Model\Paragraph\MiddleLayoutModel;

/**
 * ParagraphLayout
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class ParagraphLayout extends AbstractPlugin
{

    /**
     * @var \Paragraph\Model\Paragraph\MiddleLayoutModel
     */
    protected $middleLayoutModel;

    /**
     * Constructor
     *
     * @param \Paragraph\Model\Paragraph\MiddleLayoutModel $middleLayoutModel
     */
    public function __construct( MiddleLayoutModel $middleLayoutModel )
    {
        $this->middleLayoutModel = $middleLayoutModel;
    }

    /**
     * Set middle-layout for paragraph-layout
     *
     * @param int|null $layoutParagraphId
     * @return \Paragraph\Controller\Plugin\ParagraphLayout
     * @throws \Zend\Mvc\Exception\LogicException if controller not pluggable
     */
    public function setMiddleParagraphLayoutId( $layoutParagraphId = null )
    {
        $middleLayout = $this->middleLayoutModel
                             ->findMiddleParagraphLayoutById(
                                 $layoutParagraphId
                             );

        if ( ! empty( $middleLayout ) )
        {
            $controller = $this->getController();

            if ( ! method_exists( $controller, 'plugin' ) )
            {
                throw new Exception\LogicException(
                    'Controller used with paragraphLayout plugin must be pluggable'
                );
            }

            $controller->plugin( 'layout' )
                       ->setMiddleLayout( $middleLayout );
        }

        return $this;
    }

    /**
     * Invokable plugin
     *
     * @param int|null $layoutParagraphId
     * @return \Paragraph\Controller\Plugin\ParagraphLayout
     * @throws \Zend\Mvc\Exception\LogicException if controller not pluggable
     */
    public function __invoke( $layoutParagraphId = null )
    {
        return $this->setMiddleParagraphLayoutId( $layoutParagraphId );
    }

}
