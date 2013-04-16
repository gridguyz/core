<?php

namespace Grid\Menu\Model\Paragraph\Structure;

use Grid\Menu\Model\Menu\Model as MenuModel;
use Grid\Paragraph\Model\Paragraph\Structure\AbstractLeaf;

/**
 * Image paragraph
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Breadcrumb extends AbstractLeaf
{

    /**
     * Paragraph type
     *
     * @var string
     */
    protected static $type = 'breadcrumb';

    /**
     * Paragraph-render view-open
     *
     * @var string
     */
    protected static $viewOpen = 'grid/paragraph/render/breadcrumb';

    /**
     * Locale-aware properties
     *
     * @var array
     */
    protected static $localeAwareProperties = array();

    /**
     * Separator
     *
     * @var string
     */
    protected $separator    = '';

    /**
     * @var \Menu\Model\Menu\Model
     */
    private $_menuModel;

    /**
     * Get separator attribute
     *
     * @return string
     */
    public function getSeparator()
    {
        return $this->separator;
    }

    /**
     * Set separator attribute
     *
     * @param string $separator
     * @return \Menu\Model\Paragraph\Structure\Breadcrumb
     */
    public function setSeparator( $separator )
    {
        $this->separator = (string) $separator;
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
     * Get navigation
     *
     * @return \Zend\Navigation\AbstractContainer
     */
    public function getNavigation()
    {
        return $this->getMenuModel()
                    ->findNavigation();
    }

}
