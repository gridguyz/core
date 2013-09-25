<?php

namespace Grid\Core\View\Helper\RowSet\Type;

use NumberFormatter;

/**
 * Float
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Float implements TypeInterface
{

    /**
     * Display a single value
     *
     * @param float $value
     * @return string
     */
    public function displayValue( $value )
    {
        return $this->view->numberFormat(
            (float) $value,
            NumberFormatter::DECIMAL,
            NumberFormatter::TYPE_DOUBLE
        );
    }

}
