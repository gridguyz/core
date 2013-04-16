<?php

namespace Grid\Tag\Model\Paragraph\Structure;

use Grid\Tag\Model\Tag\Model as TagModel;
use Grid\Paragraph\Model\Paragraph\Structure\AbstractLeaf;

/**
 * TagCloud
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class TagCloud extends AbstractLeaf
{

    /**
     * @const string
     */
    const MODE_PRIMARY = 'primary';

    /**
     * @const string
     */
    const MODE_STRICT = 'strict';

    /**
     * @const string
     */
    const DEFAULT_MODE = self::MODE_PRIMARY;

    /**
     * Paragraph type
     *
     * @var string
     */
    protected static $type = 'tagCloud';

    /**
     * Paragraph-render view-open
     *
     * @var string
     */
    protected static $viewOpen = 'grid/paragraph/render/tagCloud';

    /**
     * @var \Tag\Model\Tag\Model
     */
    private $_tagModel;

    /**
     * @var string
     */
    protected $mode = self::DEFAULT_MODE;

    /**
     * Get tag-model
     *
     * @return \Tag\Model\Tag\Model
     */
    public function getTagModel()
    {
        if ( null === $this->_tagModel )
        {
            $this->_tagModel = $this->getServiceLocator()
                                     ->get( 'Grid\Tag\Model\Tag\Model' );
        }

        return $this->_tagModel;
    }

    /**
     * Set tag-model
     *
     * @param   \Tag\Model\Tag\Model $tagModel
     * @return  \Tag\Model\Paragraph\Structure\TagCloud
     */
    public function setTagModel( TagModel $tagModel )
    {
        $this->_tagModel = $tagModel;
        return $this;
    }

    /**
     * Set mode
     *
     * @param   string  $mode
     * @return  \Tag\Model\Paragraph\Structure\TagCloud
     */
    public function setMode( $mode )
    {
        $mode = (string) $mode;

        if ( static::MODE_STRICT === $mode ||
             static::MODE_PRIMARY === $mode )
        {
            $this->mode = $mode;
        }

        return $this;
    }

    /**
     * Is in primary mode
     *
     * @return bool
     */
    public function isPrimary()
    {
        return static::MODE_PRIMARY === $this->mode;
    }

    /**
     * Is in strict mode
     *
     * @return bool
     */
    public function isStrict()
    {
        return static::MODE_STRICT === $this->mode;
    }

    /**
     * Find tags in current locale(s)
     *
     * @return  array
     */
    public function findTagsUsage()
    {
        $model   = $this->getTagModel();
        $mapper  = $this->getMapper();
        $locales = array();

        switch ( $this->mode )
        {
            case static::MODE_STRICT:
                $locales = array( $mapper->getLocale() );
                break;

            case static::MODE_PRIMARY:
            default:
                $locales = array( $mapper->getLocale(),
                                  $mapper->getPrimaryLanguage() );
                break;
        }

        return $model->findUsagesByLocales( $locales );
    }

}
