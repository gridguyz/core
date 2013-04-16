<?php

namespace Grid\Tag\Form\View\Helper;

use Zend\Form\Exception;
use Zend\Form\ElementInterface;
use Zend\Form\View\Helper\FormInput;
use Grid\Tag\Form\Element\TagList as TagListElement;

/**
 * FormTagList
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class FormTagList extends FormInput
{

    /**
     * Render a form <input> element from the provided $element
     *
     * @param   ElementInterface $element
     * @throws  Exception\InvalidArgumentException
     * @throws  Exception\DomainException
     * @return  string
     */
    public function render( ElementInterface $element )
    {
        if ( ! $element instanceof TagListElement )
        {
            throw new Exception\InvalidArgumentException( sprintf(
                '%s requires that the element is of type Grid\Tag\Form\Element\TagList',
                __METHOD__
            ) );
        }

        $rendered       = '';
        $type           = $this->getInputType();
        $name           = static::getName( $element );
        $closingBracket = $this->getInlineClosingBracket();

        foreach ( $element->getValue() as $value )
        {
            $rendered .= sprintf(
                '<input %s%s',
                $this->createAttributesString( array(
                    'type'  => $type,
                    'name'  => $name,
                    'value' => $value,
                ) ),
                $closingBracket
            );
        }

        $attributes = $element->getAttributes();

        if ( empty( $attributes['class'] ) )
        {
            $attributes['class'] = '';
        }
        else
        {
            $attributes['class'] .= ' ';
        }

        $attributes['class'] .= 'tag_list';
        $attributes['data-name'] = $name;
        unset( $attributes['type'] );
        unset( $attributes['name'] );
        unset( $attributes['value'] );

        return sprintf(
            '<div %s>%s</div>',
            $this->createAttributesString( $attributes ),
            $rendered
        );
    }

    /**
     * Return input type
     *
     * @return string
     */
    protected function getInputType()
    {
        return 'hidden';
    }

    /**
     * Get element name
     *
     * @param  ElementInterface $element
     * @throws Exception\DomainException
     * @return string
     */
    protected static function getName( ElementInterface $element )
    {
        $name = $element->getName();

        if ( $name === null || $name === '' )
        {
            throw new Exception\DomainException( sprintf(
                '%s requires that the element has an assigned name; none discovered',
                __METHOD__
            ) );
        }

        return $name . '[]';
    }

}
