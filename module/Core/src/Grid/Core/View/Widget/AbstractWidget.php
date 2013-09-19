<?php

namespace Grid\Core\View\Widget;

use Zend\View\Renderer\RendererInterface;

/**
 * AbstractWidget
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
abstract class AbstractWidget implements WidgetInterface
{

    /**
     * @var string
     * @abstract
     */
    protected $template;

    /**
     * Get the widget's template
     *
     * @return  string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Set the widget's template
     *
     * @param   string  $template
     * @return  AbstractWidget
     */
    public function setTemplate( $template = null )
    {
        $this->template = (string) $template;
        return $this;
    }

    /**
     * Get the widget's variables
     *
     * @param   array   $base
     * @return  array
     */
    abstract protected function getVariables( array $base );

    /**
     * Render the widget
     *
     * @param   RendererInterface   $renderer
     * @param   string              $content
     * @param   array               $params
     * @return  string
     */
    public function render( RendererInterface $renderer, $content, array $params )
    {
        return $renderer->render(
            $this->getTemplate(),
            $this->getVariables( array_merge( $params, array(
                'content' => $content,
            ) ) )
        );
    }

}
