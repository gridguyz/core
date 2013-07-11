<?php

namespace Grid\Tag\Model\Paragraph\Structure;

use Grid\Tag\Model\TagsAwareInterface;
use Grid\Paragraph\Model\Paragraph\Structure\AbstractLeaf;
use Zend\ServiceManager\Exception\ServiceNotFoundException;

/**
 * Tags
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Tags extends AbstractLeaf
{

    /**
     * Paragraph type
     *
     * @var string
     */
    protected static $type = 'tags';

    /**
     * Locale-aware properties
     *
     * @var array
     */
    protected static $localeAwareProperties = array();

    /**
     * Paragraph-render view-open
     *
     * @var string
     */
    protected static $viewOpen = 'grid/paragraph/render/tags';

    /**
     * Get the rendered content
     *
     * @return mixed|null
     */
    protected function getRenderedContent()
    {
        try
        {
            return $this->getServiceLocator()
                        ->get( 'RenderedContent' );
        }
        catch ( ServiceNotFoundException $ex )
        {
            return null;
        }
    }

    /**
     * Get tags
     *
     * @return array
     */
    public function getRenderedLocaleTags()
    {
        $rendered = $this->getRenderedContent();

        if ( $rendered instanceof TagsAwareInterface )
        {
            return $rendered->getLocaleTags();
        }

        return null;
    }

}
