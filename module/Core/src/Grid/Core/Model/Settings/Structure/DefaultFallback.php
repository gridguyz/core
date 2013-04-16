<?php

namespace Grid\Core\Model\Settings\Structure;

use Grid\Core\Model\Settings\StructureAbstract;


/**
 * DefaultFallback
 * 
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class DefaultFallback extends StructureAbstract
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
    
}
