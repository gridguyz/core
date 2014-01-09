<?php

namespace Grid\Paragraph\Model\Paragraph\Structure;

use Zork\Stdlib\String;
use Zend\View\Renderer\RendererInterface;

/**
 * Html
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Html extends AbstractLeaf
        implements RepresentsTextContentInterface
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
     * @return  string
     */
    public function getRepresentedTextContent()
    {
        return String::stripHtml( $this->html ) ?: null;
    }

    /**
     * @return \Paragraph\Model\Paragraph\Structure\Html
     */
    public function prepareCreate()
    {
        if ( empty( $this->html ) )
        {
            $this->html = <<<HTML
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.
Nam eu condimentum est. In mattis odio non elit fermentum porttitor.
Nam et felis quis dui vestibulum faucibus. Curabitur molestie rutrum porta.
Mauris cursus, nisl quis tincidunt auctor, mi neque facilisis nisl,
quis adipiscing elit magna eget libero. Nam imperdiet pretium dolor,
in mattis lectus sodales luctus. Vestibulum ante ipsum primis in faucibus
orci luctus et ultrices posuere cubilia Curae.</p>
HTML;
        }

        return parent::prepareCreate();
    }

}
