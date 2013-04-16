<?php

namespace Grid\Core\View\Helper\RowSet\Type;

/**
 * Int
 * 
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Int implements TypeInterface
{
    
    /**
     * Display a single value
     * 
     * @param int $value
     * @return string
     */
    public function displayValue( $value )
    {
        return (string) (int) $value;
    }
    
}
