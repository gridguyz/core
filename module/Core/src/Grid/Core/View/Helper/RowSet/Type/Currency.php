<?php

namespace Grid\Core\View\Helper\RowSet\Type;

/**
 * Currency
 * 
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Currency extends AbstractHelper
{
    
    /**
     * Display a single value
     * 
     * @param string $value
     * @return string
     */
    public function displayValue( $value )
    {
        $values = explode( ' ', $value, 2 );
        
        if ( 1 < count( $values ) )
        {
            $value = $this->view->currencyFormat( $values[0], $values[1] );
        }
        
        return $value;
    }
    
}
