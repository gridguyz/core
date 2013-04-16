<?php

namespace Grid\Image\Model\Paragraph\Structure;

use Grid\Paragraph\Model\Paragraph\Structure\AbstractLeaf;

/**
 * Image paragraph
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Image extends AbstractLeaf
{

    /**
     * Paragraph type
     *
     * @var string
     */
    protected static $type = 'image';

    /**
     * Paragraph-render view-open
     *
     * @var string
     */
    protected static $viewOpen = 'grid/paragraph/render/image';

    /**
     * Locale-aware properties
     *
     * @var array
     */
    protected static $localeAwareProperties = array(
        'caption'   => true,
        'alternate' => true,
        'linkTo'    => true,
    );

    /**
     * Image-url
     *
     * @var string
     */
    public $url             = '';

    /**
     * Image-caption
     *
     * @var string
     */
    public $caption         = '';

    /**
     * Image-alternate
     *
     * @var string
     */
    public $alternate       = '';

    /**
     * Image-width
     *
     * @var string
     */
    protected $width        = null;

    /**
     * Image-height
     *
     * @var string
     */
    protected $height       = null;

    /**
     * Image-method
     *
     * @var string
     */
    public $method          = null;

    /**
     * Image-method
     *
     * @var string
     */
    public $bgColor         = null;

    /**
     * Image-link to
     *
     * @var string
     */
    public $linkTo          = null;

    /**
     * Image-link target
     *
     * @var string
     */
    public $linkTarget      = null;

    /**
     * Lightbox effect
     *
     * @var string
     */
    protected $lightBox     = false;

    /**
     * Get width attribute
     *
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set width attribute
     *
     * @param int $width
     * @return \Image\Model\Paragraph\Structure\Image
     */
    public function setWidth( $width )
    {
        $this->width = empty( $width ) ? null : (int) $width;
        return $this;
    }

    /**
     * Get height attribute
     *
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Set height attribute
     *
     * @param int $height
     * @return \Image\Model\Paragraph\Structure\Image
     */
    public function setHeight( $height )
    {
        $this->height = empty( $height ) ? null : (int) $height;
        return $this;
    }

    /**
     * Get status of lightbox effect (off/on) in a boolen value.
     *
     * @return bool
     */
    public function getLightBox()
    {
        return $this->lightBox ;
    }

    /**
     * Set status of lightbox effect (off/on).
     *
     * @param bool $lightBox   Status True/False -> On/Off
     * @return \Image\Model\Paragraph\Structure\Image
     */
    public function setLightBox( $lightBox )
    {
        $this->lightBox = (bool) $lightBox;
        return $this;
    }

}
