<?php

namespace Grid\Core\View\Helper\RowSet\Type;

/**
 * Float
 * 
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Float implements TypeInterface
{
    
    /**
     * Display a single value
     * 
     * @param float $value
     * @return string
     */
    public function displayValue( $value )
    {
        return (string) (float) $value;
    }
    
}
