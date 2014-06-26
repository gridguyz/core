<?php

namespace Grid\Paragraph\Model\Paragraph\Structure;

use ArrayIterator;
use Zork\Stdlib\String;
use Zend\ServiceManager\Exception\ServiceNotFoundException;

/**
 * Lead
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Lead extends AbstractLeaf
        implements ContentDependentAwareInterface,
                   RepresentsTextContentInterface,
                   RepresentsImageContentsInterface
{

    use ContentDependentAwareTrait;

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
     * Image-width
     *
     * @var string
     */
    protected $imageWidth   = null;

    /**
     * Image-height
     *
     * @var string
     */
    protected $imageHeight  = null;

    /**
     * Image-method
     *
     * @var string
     */
    public $imageMethod     = null;

    /**
     * Image-method
     *
     * @var string
     */
    public $imageBgColor    = null;

    /**
     * Get image width attribute
     *
     * @return  int
     */
    public function getImageWidth()
    {
        return $this->imageWidth;
    }

    /**
     * Set image width attribute
     *
     * @param   int $width
     * @return  Lead
     */
    public function setImageWidth( $width )
    {
        $this->imageWidth = empty( $width ) ? null : (int) $width;
        return $this;
    }

    /**
     * Get image height attribute
     *
     * @return  int
     */
    public function getImageHeight()
    {
        return $this->imageHeight;
    }

    /**
     * Set image height attribute
     *
     * @param   int $height
     * @return  Lead
     */
    public function setImageHeight( $height )
    {
        $this->imageHeight = empty( $height ) ? null : (int) $height;
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
        $content = $this->getDependentContent();

        if ( $content )
        {
            return $content->leadImage;
        }

        return null;
    }

    /**
     * Set root's lead-image
     *
     * @param   string  $value
     * @return  \Paragraph\Model\Paragraph\Structure\Lead
     */
    public function setRootImage( $value )
    {
        $content = $this->getDependentContent();

        if ( $content )
        {
            $content->leadImage = $value;
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
        $content = $this->getDependentContent();

        if ( $content )
        {
            return $content->leadText;
        }

        return null;
    }

    /**
     * Set root's lead-text
     *
     * @param   string  $value
     * @return  \Paragraph\Model\Paragraph\Structure\Lead
     */
    public function setRootText( $value )
    {
        $content = $this->getDependentContent();

        if ( $content )
        {
            $content->leadText = $value;
        }

        return $this;
    }

    /**
     * @return  string
     */
    public function getRepresentedTextContent()
    {
        return String::stripHtml( $this->getRootText() ) ?: null;
    }

    /**
     * @return  array
     */
    public function getRepresentedImageContentUrls()
    {
        $urls = array();
        $root = $this->getRootImage();

        if ( ! empty( $root ) )
        {
            $urls[] = $root;
        }

        return $urls;
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
                'rootImage' => $content->leadImage,
                'rootText'  => $content->leadText,
            ) ) );
        }

        return $iterator;
    }

}
