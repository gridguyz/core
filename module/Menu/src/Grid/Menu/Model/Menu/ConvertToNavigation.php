<?php

namespace Grid\Menu\Model\Menu;

use Zend\Navigation;
use Zork\Iterator\DepthList;

/**
 * ConvertToNavigation
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class ConvertToNavigation
{

    /**
     * @var Navigation\Navigation
     */
    protected $root;

    /**
     * @var Navigation\Page\Uri
     */
    protected $last = null;

    /**
     * @var array
     */
    protected $cache = array();

    /**
     * Constructor
     *
     * @param   array|\Traversable  $renderList
     */
    public function __construct( $renderList )
    {
        $this->root = new Navigation\Navigation;
        $depthList  = new DepthList( $renderList );

        $depthList->runin(
            array( $this, 'onOpen' ),
            array( $this, 'onClose' )
        );
    }

    /**
     * @param   StructureInterface      $menu
     * @param   StructureInterface|null $parent
     */
    public function onOpen( $menu, $parent )
    {
        $container              = $this->root;
        $this->cache[$menu->id] = new Navigation\Page\Uri( array(
            'label'         => $menu->getLabel() ?: '#',
            'target'        => $menu->getTarget(),
            'visible'       => $menu->isVisible(),
            'uri'           => '#',
            'priority'      => 1,
            'changefreq'    => 'never',
        ) );

        if ( $parent )
        {
            $parent->addChild( $menu );

            if ( isset( $this->cache[$parent->id] ) )
            {
                $container = $this->cache[$parent->id];

                $this->cache[$menu->id]->set(
                    'priority',
                    $container->get( 'priority' ) * 0.8
                );
            }
        }

        $container->addPage( $this->cache[$menu->id] );
    }

    /**
     * @param   StructureInterface  $menu
     */
    public function onClose( $menu )
    {
        $uri        = $menu->getUri();
        $this->last = $this->cache[$menu->id]->setUri( $uri );

        if ( $menu->hasChildren() )
        {
            $this->last->setClass( 'has-children' );
        }

        if ( $menu->isActive() )
        {
            $this->last->setActive( true );
        }

        if ( ! empty( $uri ) && $uri[0] !== '#' )
        {
            $this->last->set( 'changefreq', 'always' );
        }
    }

    /**
     * @return  Navigation\Navigation
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * @return  Navigation\Page\Uri
     */
    public function getLast()
    {
        return $this->last;
    }

}
