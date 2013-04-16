<?php

namespace Grid\Core\View\Helper\RowSet\Type;

/**
 * Callback
 * 
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Callback implements TypeInterface
{
    
    /**
     * @var string
     */
    protected $callback;
    
    /**
     * @return callable
     */
    public function getCallback()
    {
        return $this->callback;
    }
    
    /**
     * @param callable $callback
     * @return \Core\View\Helper\RowSet\Type\Callback
     */
    public function setCallback( callable $callback )
    {
        $this->callback = $callback;
        return $this;
    }
    
    /**
     * @param callable $callback
     */
    public function __construct( callable $callback )
    {
        $this->setCallback( $callback );
    }
    
    /**
     * Display a single value
     * 
     * @param mixed $value
     * @param mixed $values
     * @return string
     */
    public function displayValue( $value )
    {
        return call_user_func( $this->callback, func_get_arg( 1 ), $value );
    }
    
}
