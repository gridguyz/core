<?php

namespace Grid\Core\View\Widget;

use Zend\View\Renderer\RendererInterface;

/**
 * WidgetInterface
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class WidgetInterface
{

    /**
     * Render the widget
     *
     * @param   RendererInterface   $renderer
     * @param   string              $content
     * @param   array               $params
     * @return  string
     */
    public function render( RendererInterface $renderer, $content, array $params );

}
