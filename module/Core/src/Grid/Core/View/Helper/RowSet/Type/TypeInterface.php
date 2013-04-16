<?php

namespace Grid\Core\View\Helper\RowSet\Type;

/**
 * TypeInterface
 * 
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
interface TypeInterface
{
    
    /**
     * Display a single value
     * 
     * @param mixed $value
     * @return string
     */
    public function displayValue( $value );
    
}
