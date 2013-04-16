<?php

namespace Grid\Core\View\Helper\RowSet\Type;

/**
 * Date
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Date extends DateTime
{

    /**
     * @var int
     */
    protected $timeFormat   = self::NONE;

    /**
     * @param null|int $dateFormat
     */
    public function __construct( $dateFormat = null )
    {
        parent::__construct( $dateFormat );
    }

}
