<?php

namespace Grid\Paragraph\Model\Paragraph\Structure;

use Zend\View\Renderer\RendererInterface;

/**
 * Html
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Html extends AbstractLeaf
{

    /**
     * Paragraph type
     *
     * @var string
     */
    protected static $type = 'html';

    /**
     * Locale-aware properties
     *
     * @var array
     */
    protected static $localeAwareProperties = array(
        'html' => true,
    );

    /**
     * Html content
     *
     * @var string
     */
    public $html = '';

    /**
     * Render view-open
     *
     * @param \Zend\View\Renderer\RendererInterface $renderer
     * @return string
     */
    public function renderOpen( RendererInterface $renderer )
    {
        return $this->html;
    }

    /**
     * @return \Paragraph\Model\Paragraph\Structure\Html
     */
    public function prepareCreate()
    {
        if ( empty( $this->html ) )
        {
            $this->html = @ file_get_contents( 'data/paragraph/html.html' );
        }

        return parent::prepareCreate();
    }

}
