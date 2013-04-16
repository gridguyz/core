<?php

namespace Grid\Menu\Model\Paragraph\Structure;

use Grid\Menu\Model\Menu\Model as MenuModel;
use Grid\Menu\Model\Menu\StructureInterface as MenuStructureInterface;
use Grid\Paragraph\Model\Paragraph\Structure\AbstractLeaf;

/**
 * Image paragraph
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Menu extends AbstractLeaf
{

    /**
     * Paragraph type
     *
     * @var string
     */
    protected static $type = 'menu';

    /**
     * Paragraph-render view-open
     *
     * @var string
     */
    protected static $viewOpen = 'grid/paragraph/render/menu';

    /**
     * Locale-aware properties
     *
     * @var array
     */
    protected static $localeAwareProperties = array();

    /**
     * Menu ID
     *
     * @var int
     */
    protected $menuId       = null;

    /**
     * Is horizontal
     *
     * @var bool
     */
    protected $horizontal   = true;

    /**
     * @var \Menu\Model\Menu\Model
     */
    private $_menuModel;

    /**
     * @var \Menu\Model\Menu\StructureInterface
     */
    private $_menu;

    /**
     * Get menu-id attribute
     *
     * @return int
     */
    public function getMenuId()
    {
        return $this->menuId;
    }

    /**
     * Set menu-id attribute
     *
     * @param int $menuId
     * @return \Menu\Model\Paragraph\Structure\Menu
     */
    public function setMenuId( $menuId )
    {
        $this->menuId = empty( $menuId ) ? null : (int) $menuId;

        if ( null !== $this->_menu &&
             $this->_menu->id != $this->menuId )
        {
            $this->_menu = null;
        }

        return $this;
    }

    /**
     * Get horizontal attribute
     *
     * @return bool
     */
    public function isHorizontal()
    {
        return $this->horizontal;
    }

    /**
     * Set horizontal attribute
     *
     * @param bool $horizontal
     * @return \Menu\Model\Paragraph\Structure\Menu
     */
    public function setHorizontal( $horizontal )
    {
        $this->horizontal = (bool) $horizontal;
        return $this;
    }

    /**
     * Get menu-model
     *
     * @return \Menu\Model\Menu\Model
     */
    public function getMenuModel()
    {
        if ( null === $this->_menuModel )
        {
            $this->_menuModel = $this->getServiceLocator()
                                     ->get( 'Grid\Menu\Model\Menu\Model' );
        }

        return $this->_menuModel;
    }

    /**
     * Set menu-model
     *
     * @param \Menu\Model\Menu\Model $menuModel
     * @return \Menu\Model\Paragraph\Structure\Menu
     */
    public function setMenuModel( MenuModel $menuModel )
    {
        $this->_menuModel = $menuModel;
        return $this;
    }

    /**
     * Get inner menu
     *
     * @return \Menu\Model\Menu\StructureInterface
     */
    public function getMenu()
    {
        if ( null === $this->_menu &&
             ! empty( $this->menuId ) )
        {
            $this->_menu = $this->getMenuModel()
                                ->find( $this->menuId );
        }

        return $this->_menu;
    }

    /**
     * Set inner menu
     *
     * @param \Menu\Model\Menu\StructureInterface $menu
     * @return \Menu\Model\Paragraph\Structure\Menu
     */
    public function setMenu( MenuStructureInterface $menu )
    {
        $this->_menu    = $menu;
        $this->menuId   = $menu->getId();
        return $this;
    }

    /**
     * Get navigation
     *
     * @return \Zend\Navigation\Page\AbstractPage
     */
    public function getNavigation()
    {
        if ( empty( $this->menuId ) )
        {
            return null;
        }

        return $this->getMenuModel()
                    ->findNavigation( $this->menuId );
    }

}
