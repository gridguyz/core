<?php

namespace Grid\Core\View\Helper\RowSet\Type;

/**
 * Enum
 * 
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Enum extends Translate
{
    
    /**
     * @var array
     */
    protected $values = array();
    
    /**
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }
    
    /**
     * @param array $values
     * @return \Core\View\Helper\RowSet\Type\Enum
     */
    public function setValues( array $values )
    {
        $this->values = $values;
        return $this;
    }
    
    /**
     * Create enumeration
     * 
     * @param array $values
     * @param string $translatePrefix
     * @param string $translatePostfix
     * @param string $translateTextDomain
     */
    public function __construct( array $values          = array(),
                                 $translatePrefix       = null,
                                 $translatePostfix      = null,
                                 $translateTextDomain   = null )
    {
        parent::__construct(
            $translatePrefix,
            $translatePostfix,
            $translateTextDomain
        );
        
        $this->setValues( $values );
    }
    
    /**
     * Display a single value
     * 
     * @param string $value
     * @return string
     */
    public function displayValue( $value )
    {
        if ( isset( $this->values[$value] ) )
        {
            $value = $this->values[$value];
        }
        
        return parent::displayValue( $value );
    }
    
}
