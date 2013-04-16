<?php

namespace Grid\Customize\Form\View\Helper;

use Zend\Form\Exception;
use Zend\Form\ElementInterface;
use Grid\Customize\Form\Element\Properties;
use Zend\Form\View\Helper\AbstractHelper;

/**
 * FormCustomizeProperties
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class FormCustomizeProperties extends AbstractHelper
{

    /**
     * Attributes valid for the current tag
     *
     * Will vary based on whether a select, option, or optgroup is being rendered
     *
     * @var array
     */
    protected $validTagAttributes;

    /**
     * @var array
     */
    protected $validContainerAttributes = array(
    );

    /**
     * @var array
     */
    protected $validValueAttributes = array(
        'autofocus' => true,
        'disabled'  => true,
        'form'      => true,
        'required'  => true,
        'name'      => true,
        'label'     => true,
        'type'      => true,
        'value'     => true,
    );

    /**
     * @var array
     */
    protected $validPriorityAttributes = array(
        'autofocus' => true,
        'disabled'  => true,
        'form'      => true,
        'required'  => true,
        'checked'   => true,
        'name'      => true,
        'label'     => true,
        'type'      => true,
        'value'     => true,
    );

    /**
     * Render a form checkbox-group element from the provided $element
     *
     * @param  \Zend\Form\ElementInterface $element
     * @throws \Zend\Form\Exception\InvalidArgumentException
     * @throws \Zend\Form\Exception\DomainException
     * @return string
     */
    public function render( ElementInterface $element )
    {
        if ( ! $element instanceof Properties )
        {
            throw new Exception\InvalidArgumentException( sprintf(
                '%s requires that the element is of type Grid\Customize\Form\Element\Properties',
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

        $attributes = $element->getAttributes();
        $value      = (array) $element->getValue();
        $addAttr    = array(
            'name'      => $name,
            'required'  => empty( $attributes['required'] ) ? null : 'required',
        );

        unset( $attributes['name'] );
        unset( $attributes['required'] );
        $this->validTagAttributes = $this->validContainerAttributes;
        return sprintf(
            '<dl %s>%s</dl>',
            $this->createAttributesString( $attributes ),
            $this->renderProperties( $value, $addAttr )
        );
    }

    /**
     * Render properties
     *
     * @param  array $value
     * @param  array $addAttr
     * @return string
     */
    protected function renderProperties( array $values  = array(),
                                         array $addAttr = array() )
    {
        $markup = '';

        foreach ( $values as $key => $data )
        {
            if ( is_string( $key ) && isset( $data['value'] ) )
            {
                $markup .= $this->renderProperty(
                    $key,
                    $data['value'],
                    ! empty( $data['priority'] ),
                    $addAttr
                );
            }
        }

        return $markup;
    }

    /**
     * Render a priority
     *
     * @param  string $key
     * @param  string $value
     * @param  bool   $important
     * @param  array  $addAttr
     * @return string
     */
    protected function renderProperty( $key, $value, $important,
                                       array $addAttr = array() )
    {
        $markup = '';
        $escape = $this->getEscapeHtmlHelper();
        $eattr  = $this->getEscapeHtmlAttrHelper();
        $name   = empty( $addAttr['name'] ) ? $key : $addAttr['name'] . '[' . $key . ']';

        $this->validTagAttributes = $this->validValueAttributes;
        $markup .= sprintf(
            '<dt><label for="%s">%s</label></dt><dd><input %s />',
            $eattr( $name . '[value]' ),
            $escape( $key ),
            $this->createAttributesString( array_merge(
                $addAttr, array(
                    'type'  => 'text',
                    'name'  => $name . '[value]',
                    'value' => $value,
                )
            ) )
        );

        $importantLabel = 'important';
        $translator     = $this->getTranslator();

        if ( $translator )
        {
            $importantLabel = $translator->translate(
                'customize.form.important',
                'customize'
            );
        }

        $this->validTagAttributes = $this->validPriorityAttributes;
        $markup .= sprintf(
            '<label><input %s />%s</label></dd>',
            $this->createAttributesString( array_merge(
                $addAttr, array(
                    'type'      => 'checkbox',
                    'name'      => $name . '[priority]',
                    'value'     => 'important',
                    'checked'   => $important,
                )
            ) ),
            $escape( $importantLabel )
        );

        return $markup;
    }

    /**
     * Invoke helper as functor
     *
     * Proxies to {@link render()}.
     *
     * @param  ElementInterface|null $element
     * @return string|FormRadioGroup
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
