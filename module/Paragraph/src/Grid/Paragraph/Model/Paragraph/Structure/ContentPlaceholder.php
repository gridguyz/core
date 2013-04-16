<?php

namespace Grid\Paragraph\Model\Paragraph\Structure;

/**
 * Content-placeholder
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class ContentPlaceholder extends AbstractLeaf
{

    /**
     * Paragraph type
     *
     * @var string
     */
    protected static $type = 'contentPlaceholder';

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
    protected static $viewOpen = 'grid/paragraph/render/contentPlaceholder';

    /**
     * This paragraph-type properties
     *
     * @return array
     */
    public static function getAllowedFunctions()
    {
        return array_diff(
            parent::getAllowedFunctions(),
            array( static::PROPERTY_EDIT,
                   static::PROPERTY_DELETE )
        );
    }

}
