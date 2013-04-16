<?php

namespace Grid\Menu\Model\Menu;

/**
 * Grid\Menu\Model\Paragraph\StructureInterface
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
interface StructureInterface
{

    /**
     * @const null
     */
    const TARGET_DEFAULT    = null;

    /**
     * @const string
     */
    const TARGET_SELF       = '_self';

    /**
     * @const string
     */
    const TARGET_BLANK      = '_blank';

    /**
     * @const string
     */
    const TARGET_PARENT     = '_parent';

    /**
     * @const string
     */
    const TARGET_TOP        = '_top';

    /**
     * Get ID of the menu
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get type of the menu
     *
     * @return string|null
     */
    public function getType();

    /**
     * Get label of the menu
     *
     * @return string|null
     */
    public function getLabel();

    /**
     * Get target of the menu
     *
     * @return string|null
     */
    public function getTarget();

}
