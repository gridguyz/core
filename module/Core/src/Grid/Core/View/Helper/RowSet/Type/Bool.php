<?php

namespace Grid\Core\View\Helper\RowSet\Type;

/**
 * Bool
 * 
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Bool implements TypeInterface
{
    
    /**
     * @var string 
     */
    const TRUE  = '&#10004;';
    
    /**
     * @var string 
     */
    const FALSE = '&#10008;';
    
    /**
     * @var array
     */
    protected $validTrues = array(
        true,
        '1',
        'y',
        't',
        'on',
        'yes',
        'true',
    );
    
    /**
     * Display a single value
     * 
     * @param bool $value
     * @return string
     */
    public function displayValue( $value )
    {
        if ( ! is_bool( $value ) )
        {
            $value = (string) $value;
        }
        
        return ( in_array( $value, $this->validTrues, true ) )
                ? self::TRUE : self::FALSE;
    }
    
}
