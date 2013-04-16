<?php

namespace Grid\Paragraph\Form\Element;

use Zork\Form\Element;

/**
 * ColumnsPercentages form element
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class ColumnsPercentages extends Element
{

    /**
     * Seed attributes
     *
     * @var array
     */
    protected $attributes = array(
        'type'          => 'columns_percentages',
        'data-js-type'  => 'js.paragraph.columnsPercentages',
    );

}
