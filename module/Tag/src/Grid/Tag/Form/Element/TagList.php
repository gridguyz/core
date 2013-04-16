<?php

namespace Grid\Tag\Form\Element;

use Traversable;
use Zork\Form\Element;
use Zend\Stdlib\ArrayUtils;

/**
 * TagList
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class TagList extends Element
{

    /**
     * Seed attributes
     *
     * @var array
     */
    protected $attributes = array(
        'type'          => 'tag_list',
        'data-js-type'  => 'js.form.element.tagList',
    );

    /**
     * @var array
     */
    protected $value = array();

    /**
     * Set the element value
     *
     * @param   mixed   $value
     * @return  TagList
     */
    public function setValue( $value )
    {
        if ( $value instanceof Traversable )
        {
            $value = ArrayUtils::iteratorToArray( $value );
        }

        return parent::setValue( (array) $value );
    }

}
