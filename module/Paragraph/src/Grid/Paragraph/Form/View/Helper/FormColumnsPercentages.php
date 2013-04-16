<?php

namespace Grid\Paragraph\Form\View\Helper;

use Zend\Form\Exception;
use Zend\Form\ElementInterface;
use Zend\Form\View\Helper\AbstractHelper;
use Grid\Paragraph\Form\Element\ColumnsPercentages;

/**
 * Helper to generate a "columns_percentages" element
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class FormColumnsPercentages extends AbstractHelper
{

    /**
     * Attributes valid for the current tag
     *
     * Will vary based on whether a tag is being rendered
     *
     * @var array
     */
    protected $validTagAttributes = array(
        'type'      => true,
        'required'  => true,
        'disabled'  => true,
        'name'      => true,
        'value'     => true,
    );

    /**
     * Render a form radio-group element from the provided $element
     *
     * @param  ElementInterface $element
     * @throws Exception\InvalidArgumentException
     * @throws Exception\DomainException
     * @return string
     */
    public function render( ElementInterface $element )
    {
        if ( ! $element instanceof ColumnsPercentages )
        {
            throw new Exception\InvalidArgumentException( sprintf(
                '%s requires that the element is of type Grid\Paragraph\Form\Element\ColumnsPercentages',
                __METHOD__
            ) );
        }

        $name = $element->getName();
        if ( empty( $name ) && $name !== 0 )
        {
            throw new Exception\DomainException( sprintf(
                '%s requires that the element has an assigned name; none discovered',
                __METHOD__
            ) );
        }

        $values = $element->getValue();
        if ( empty( $values ) )
        {
            $values = array();
        }

        $attributes = $element->getAttributes();
        $addAttr    = array(
            'type'      => 'number',
            'required'  => empty( $attributes['required'] ) ? null : 'required',
            'disabled'  => empty( $attributes['disabled'] ) ? null : 'disabled',
        );

        unset( $attributes['type'] );
        unset( $attributes['required'] );
        unset( $attributes['disabled'] );

        return sprintf(
            '<div %s>%s</div>',
            $this->createAttributesString( $attributes ),
            $this->renderPercentages( $values, $name, $addAttr )
        );
    }

    /**
     * Render percentages
     *
     * @param array $values
     * @param string $name
     * @param array $additionalAttributes
     * @return string
     */
    public function renderPercentages( array $values, $name,
                                       array $additionalAttributes = array() )
    {
        $i           = 0;
        $percentages = array();

        foreach ( $values as $key => $value )
        {
            $attr = $additionalAttributes + array(
                'name'                          => $name . '[' . $key . ']',
                'value'                         => $value,
                'data-js-paragraph-represent'   => $key,
            );

            $percentages[] = sprintf(
                '<label>%s:&nbsp;<input %s>%%</label>',
                ++$i,
                $this->createAttributesString( $attr )
            );
        }

        return implode( "\n", $percentages );
    }

    /**
     * Invoke helper as functor
     *
     * Proxies to {@link render()}.
     *
     * @param  ElementInterface|null $element
     * @return string|FormColumnsPercentages
     */
    public function __invoke( ElementInterface $element = null )
    {
        if ( ! $element )
        {
            return $this;
        }

        return $this->render( $element );
    }

}
