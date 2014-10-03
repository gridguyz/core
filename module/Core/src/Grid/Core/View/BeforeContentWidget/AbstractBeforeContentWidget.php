<?php

namespace Grid\Core\View\BeforeContentWidget;

use Zend\View\Renderer\RendererInterface;

/**
 * AbstractBeforeContentWidget
 *
 * @author Kristof Matos <kristof.matos@megaweb.hu>
 */
abstract class AbstractBeforeContentWidget implements BeforeContentWidgetInterface
{
    /**
     * @var string
     * @abstract
     */
    protected $template;

    /**
     * @var array 
     */
    protected $variables = array();
    
    /**
     * @var integer
     */
    protected $priority = BeforeContentWidgetInterface::DEFAULT_PRIORITY;
   
    
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
     * @return  array
     */
    protected function getVariables()
    {
        return $this->variables;
    }
    
    /**
     * @return integer
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Render the widget
     */
    public function render( RendererInterface $renderer )
    {
        return $renderer->render(
            $this->getTemplate(),
            $this->getVariables()
        );
    }

}
