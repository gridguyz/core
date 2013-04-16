<?php

namespace Grid\Menu\Model\Menu\Structure;

/**
 * Container
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Container extends ProxyAbstract
{

    /**
     * Menu type
     *
     * @var string
     */
    protected static $type = 'container';

    /**
     * Get URI of this menu-item
     *
     * @return string
     */
    public function getUri()
    {
        if ( $this->hasChildren() )
        {
            foreach( $this->getChildren() as $child )
            {
                $uri = $child->getUri();

                if ( '#' != $uri[0] )
                {
                    return $uri;
                }
            }
        }
        else
        {
            return '#';
        }
    }

}
