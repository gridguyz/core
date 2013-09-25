<?php

namespace Grid\Core\View\Helper\RowSet\Type;

use NumberFormatter;

/**
 * Int
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Int extends AbstractHelper
{

    /**
     * Display a single value
     *
     * @param int $value
     * @return string
     */
    public function displayValue( $value )
    {
        return $this->view->numberFormat(
            (int) $value,
            NumberFormatter::DECIMAL,
            NumberFormatter::TYPE_INT64
        );
    }

}
