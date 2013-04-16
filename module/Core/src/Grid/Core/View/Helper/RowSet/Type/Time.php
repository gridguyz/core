<?php

namespace Grid\Core\View\Helper\RowSet\Type;

/**
 * Time
 * 
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Time extends DateTime
{
    
    /**
     * @var int
     */
    protected $dateFormat   = self::NONE;
    
    /**
     * @param null|int $timeFormat
     */
    public function __construct( $timeFormat = null )
    {
        parent::__construct( null, $timeFormat );
    }
    
}
