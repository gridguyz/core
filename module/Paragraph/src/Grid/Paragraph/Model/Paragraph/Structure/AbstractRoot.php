<?php

namespace Grid\Paragraph\Model\Paragraph\Structure;

/**
 * Grid\Paragraph\Model\Paragraph\Structure\AbstractRoot
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
abstract class AbstractRoot extends AbstractContainer
{

    /**
     * This paragraph can be only child of nothing
     *
     * @var string
     */
    protected static $onlyChildOf = null;

    /**
     * Get root paragraph
     *
     * @return  \Paragraph\Model\Paragraph\Structure\AbstractRoot
     */
    public function getRootParagraph()
    {
        return $this;
    }

    /**
     * This paragraph-type properties
     *
     * @return  array
     */
    public static function getAllowedFunctions()
    {
        return array_diff(
            parent::getAllowedFunctions(),
            array( static::PROPERTY_DRAG )
        );
    }

}
