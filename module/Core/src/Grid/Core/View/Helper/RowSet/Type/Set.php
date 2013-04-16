<?php

namespace Grid\Core\View\Helper\RowSet\Type;

/**
 * Set type for rowSet helper
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Set extends Enum
{
    
    /**
     * @var string
     */
    protected $separator = ', ';
    
    /**
     * @return string
     */
    public function getSeparator()
    {
        return $this->separator;
    }
    
    /**
     * @param string $separator
     * @return \Core\View\Helper\RowSet\Type\Set
     */
    public function setSeparator( $separator )
    {
        $this->separator = (string) $separator;
        return $this;
    }
    
    /**
     * Create enumeration
     * 
     * @param array $values
     * @param string $translatePrefix
     * @param string $translatePostfix
     * @param string $translateTextDomain
     * @param string $separator
     */
    public function __construct( array $values          = array(),
                                 $translatePrefix       = null,
                                 $translatePostfix      = null,
                                 $translateTextDomain   = null,
                                 $separator             = null )
    {
        parent::__construct(
            $values,
            $translatePrefix,
            $translatePostfix,
            $translateTextDomain
        );
        
        if ( null !== $separator )
        {
            $this->setSeparator( $separator );
        }
    }
    
    /**
     * Display a single value
     * 
     * @param string|array $value
     * @return string
     */
    public function displayValue( $values )
    {
        $result = '';
        $first  = true;
        $values = (array) $values;
        
        foreach ( $values as $value )
        {
            if ( $first )
            {
                $first = false;
            }
            else
            {
                $result .= $this->separator;
            }
            
            $result .= parent::displayValue( $value );
        }
        
        return $result;
    }
    
}
