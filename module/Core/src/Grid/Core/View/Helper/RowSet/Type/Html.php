<?php

namespace Grid\Core\View\Helper\RowSet\Type;

/**
 * Html
 * 
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Html extends Text
{
    
    /**
     * Display a single value
     * 
     * @param string $value
     * @return string
     */
    public function displayValue( $value )
    {
        return parent::displayValue( strip_tags( $value ) );
    }
    
}
