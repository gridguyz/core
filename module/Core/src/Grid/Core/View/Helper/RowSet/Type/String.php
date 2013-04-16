<?php

namespace Grid\Core\View\Helper\RowSet\Type;

/**
 * String
 * 
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class String implements TypeInterface
{
    
    /**
     * Display a single value
     * 
     * @param string $value
     * @return string
     */
    public function displayValue( $value )
    {
        return (string) $value;
    }
    
}
