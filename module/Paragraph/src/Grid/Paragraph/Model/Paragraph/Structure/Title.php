<?php

namespace Grid\Paragraph\Model\Paragraph\Structure;

use ArrayIterator;
use Zend\ServiceManager\Exception\ServiceNotFoundException;

/**
 * Title
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Title extends AbstractLeaf
         implements MetaContentDependentAwareInterface
{

    use ContentDependentAwareTrait;

    /**
     * Paragraph type
     *
     * @var string
     */
    protected static $type = 'title';

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
    protected static $viewOpen = 'grid/paragraph/render/title';

    /**
     * Separator
     *
     * @var string
     */
    protected $separator = '/';

    /**
     * Set separator
     *
     * @param string $separator
     * @return \Paragraph\Model\Paragraph\Structure\Title
     */
    public function setSeparator( $separator )
    {
        $this->separator = trim( (string) $separator );
        return $this;
    }

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
     * Get rendered title
     *
     * @return string
     */
    public function getRenderedTitle()
    {
        $rendered = $this->getRenderedContent();

        if ( $rendered instanceof Content || $rendered instanceof MetaContent )
        {
            return $rendered->title;
        }

        return null;
    }

    /**
     * Get root title
     *
     * @return string
     */
    public function getRootTitle()
    {
        $content = $this->getDependentContent();

        if ( $content )
        {
            return $content->title;
        }

        return null;
    }

    /**
     * Set root title
     *
     * @return string
     */
    public function setRootTitle( $title )
    {
        $content = $this->getDependentContent();

        if ( $content )
        {
            $content->title = $title;
        }

        return $this;
    }

    /**
     * Get iterator
     *
     * @return \AppendIterator
     */
    public function getIterator()
    {
        $iterator   = parent::getIterator();
        $content    = $this->getDependentContent();

        if ( $content )
        {
            $iterator->append( new ArrayIterator( array(
                'rootTitle' => $content->title,
            ) ) );
        }

        return $iterator;
    }

}
