<?php

namespace Grid\Core\View\BeforeContentWidget;

use Zend\View\Renderer\RendererInterface;

/**
 * BeforeContentWidgetInterface
 *
 * @author Kristof Matos <kristof.matos@megaweb.hu>
 */
interface BeforeContentWidgetInterface
{
    /**
     * @var integer
     */
    const DEFAULT_PRIORITY = 1;
    
    /**
     * @param   RendererInterface   $renderer
     * @return  string
     */
    public function render( RendererInterface $renderer );

    /**
     * @return integer
     */
    public function getPriority();
    
}
