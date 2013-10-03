<?php

namespace Grid\Paragraph\Model\Paragraph\Structure;

use Zend\ServiceManager\Exception\ServiceNotFoundException;

/**
 * Title
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Title extends AbstractLeaf
{

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
        $root = $this->getRootParagraph();

        if ( $root instanceof Content || $root instanceof MetaContent )
        {
            return $root->title;
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
        $root = $this->getRootParagraph();

        if ( $root instanceof Content || $root instanceof MetaContent )
        {
            $root->title = (string) $title;
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
        $iterator = parent::getIterator();

        if ( $this->rootId )
        {
            $iterator->append( new ArrayIterator( array(
                'rootTitle' => $this->getRootTitle(),
            ) ) );
        }

        return $iterator;
    }

    /**
     * Get dependent structures
     *
     * @return \Zork\Model\Structure\MapperAwareAbstract[]
     */
    public function getDependentStructures()
    {
        $dependents = parent::getDependentStructures();
        $root       = $this->getRootParagraph();

        if ( $root instanceof Content || $root instanceof MetaContent )
        {
            $dependents[] = $root;
        }

        return $dependents;
    }

}
