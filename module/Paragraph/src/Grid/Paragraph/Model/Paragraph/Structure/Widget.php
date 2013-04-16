<?php

namespace Grid\Paragraph\Model\Paragraph\Structure;

use Zend\Stdlib\ArrayUtils;

/**
 * Widget
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Widget extends AbstractLeaf
{

    /**
     * Paragraph type
     *
     * @var string
     */
    protected static $type = 'widget';

    /**
     * Locale-aware properties
     *
     * @var array
     */
    protected static $localeAwareProperties = array(
        'code' => true,
    );

    /**
     * Paragraph-render view-open
     *
     * @var string
     */
    protected static $viewOpen = 'grid/paragraph/render/widget';

    /**
     * Widget code
     *
     * @var string
     */
    public $code = '';

    /**
     * Widget's snippets
     *
     * @var array
     */
    protected $snippets = array();

    /**
     * Get snippets
     *
     * @return array
     */
    public function getSnippets()
    {
        return $this->snippets;
    }

    /**
     * Set snippets
     *
     * @param   array|string|\Traversable  $snippets
     * @return  \Paragraph\Model\Paragraph\Structure\Widget
     */
    public function setSnippets( $snippets )
    {
        if ( is_string( $snippets ) )
        {
            $snippets = explode( "\n", $snippets );
        }
        else if ( is_null( $snippets ) || is_scalar( $snippets ) )
        {
            $snippets = (array) $snippets;
        }

        $this->snippets = array_unique(
            array_map(
                'strval',
                array_filter( ArrayUtils::iteratorToArray( $snippets ) )
            )
        );

        return $this;
    }

}
