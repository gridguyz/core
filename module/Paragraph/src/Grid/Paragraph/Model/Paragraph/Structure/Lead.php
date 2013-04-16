<?php

namespace Grid\Paragraph\Model\Paragraph\Structure;

use ArrayIterator;
use Zend\ServiceManager\Exception\ServiceNotFoundException;

/**
 * Lead
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Lead extends AbstractLeaf
{

    /**
     * Paragraph type
     *
     * @var string
     */
    protected static $type = 'lead';

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
    protected static $viewOpen = 'grid/paragraph/render/lead';

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
     * Get lead image
     *
     * @return string
     */
    public function getRenderedImage()
    {
        $rendered = $this->getRenderedContent();

        if ( $rendered instanceof Content )
        {
            return $rendered->leadImage;
        }

        return null;
    }

    /**
     * Get lead text
     *
     * @return string
     */
    public function getRenderedText()
    {
        $rendered = $this->getRenderedContent();

        if ( $rendered instanceof Content )
        {
            return $rendered->leadText;
        }

        return null;
    }

    /**
     * Get root's lead-image
     *
     * @return  string|null
     */
    public function getRootImage()
    {
        $root = $this->getRootParagraph();
        return $root instanceof Content ? $root->leadImage : null;
    }

    /**
     * Set root's lead-image
     *
     * @param   string  $value
     * @return  \Paragraph\Model\Paragraph\Structure\Lead
     */
    public function setRootImage( $value )
    {
        $root = $this->getRootParagraph();

        if ( $root instanceof Content )
        {
            $root->leadImage = $value;
        }

        return $this;
    }

    /**
     * Get root's lead-text
     *
     * @return  string|null
     */
    public function getRootText()
    {
        $root = $this->getRootParagraph();
        return $root instanceof Content ? $root->leadText : null;
    }

    /**
     * Set root's lead-text
     *
     * @param   string  $value
     * @return  \Paragraph\Model\Paragraph\Structure\Lead
     */
    public function setRootText( $value )
    {
        $root = $this->getRootParagraph();

        if ( $root instanceof Content )
        {
            $root->leadText = $value;
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
                'rootImage' => $this->getRootImage(),
                'rootText'  => $this->getRootText(),
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

        if ( $root instanceof Content )
        {
            $dependents[] = $root;
        }

        return $dependents;
    }

}
