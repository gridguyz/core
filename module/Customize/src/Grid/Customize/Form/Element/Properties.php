<?php

namespace Grid\Customize\Form\Element;

use Zork\Form\Element;

/**
 * Properties
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Properties extends Element
{

    /**
     * Seed attributes
     *
     * @var array
     */
    protected $attributes = array(
        'type'          => 'customize_properties',
        'data-js-type'  => 'js.customize.properties',
    );

}
