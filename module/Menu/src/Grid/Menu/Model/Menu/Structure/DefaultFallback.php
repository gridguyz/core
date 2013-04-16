<?php

namespace Grid\Menu\Model\Menu\Structure;

/**
 * Default-fallback
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class DefaultFallback extends ProxyAbstract
{

    /**
     * Return true if and only if $options accepted by this adapter
     * If returns float as likelyhood the max of these will be used as adapter
     *
     * @param array $options;
     * @return float
     */
    public static function acceptsOptions( array $options )
    {
        return 0.01;
    }

    /**
     * Get URI of this menu-item
     *
     * @return string
     */
    public function getUri()
    {
        return '#';
    }

}
