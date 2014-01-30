<?php

namespace Grid\Paragraph\Model\Dashboard;

use Grid\Customize\Model\Rule;
use Grid\Paragraph\Model\Paragraph;
use Grid\Paragraph\Form\Dashboard as DashboardForm;
use Zork\Stdlib\String;
use Zork\Form\FormService;
use Zend\Form\FieldsetInterface;
use Zend\Form\Element\Collection;
use Zork\Form\PrepareElementsAwareInterface;
use Zork\Model\Structure\MapperAwareAbstract;

/**
 * \Paragraph\Model\Dashboard\Structure
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Structure extends MapperAwareAbstract
{

    /**
     * @var \Paragraph\Model\Paragraph\StructureInterface
     */
    protected $paragraph;

    /**
     * @var \Customize\Model\Rule\Structure[]
     */
    protected $rules = array();

    /**
     * @param \Paragraph\Model\Paragraph\StructureInterface $paragraph
     * @return \Paragraph\Model\Dashboard\Structure
     */
    public function setParagraph( Paragraph\StructureInterface $paragraph )
    {
        $this->paragraph = $paragraph;
        return $this;
    }

    /**
     * @param \Customize\Model\Rule\Structure $rule
     * @param string|null $key
     * @return \Paragraph\Model\Dashboard\Structure
     */
    public function setRule( Rule\Structure $rule, $key = null )
    {
        if ( $this->paragraph && ( $rootId = $this->paragraph->getRootId() ) )
        {
            $rule->setRootParagraphId( $rootId );
        }

        if ( empty( $key ) )
        {
            $this->rules[] = $rule;
        }
        else
        {
            $this->rules[$key] = $rule;
        }

        return $this;
    }

    /**
     * @param array|\Traversable|\Customize\Model\Rule\Structure[] $rules
     * @return \Paragraph\Model\Dashboard\Structure
     */
    public function setRules( $rules )
    {
        $this->rules = array();

        foreach ( $rules as $key => $rule )
        {
            $this->setRule( $rule, $key );
        }

        return $this;
    }

    /**
     * @param string $key
     * @return \Customize\Model\Rule\Structure
     */
    public function getRuleByKey( $key )
    {
        return isset( $this->rules[$key] ) ? $this->rules[$key] : null;
    }

    /**
     * Reflect css properties
     *
     * @param   \Zend\Form\FieldsetInterface    $fieldset
     * @param   string                          $selector
     * @return  \Zend\Form\FieldsetInterface
     */
    protected function reflectCss( FieldsetInterface $fieldset, $selector )
    {
        foreach ( $fieldset->getFieldsets() as $subFieldset )
        {
            if ( ! $subFieldset instanceof Collection )
            {
                $this->reflectCss( $subFieldset, $selector );
            }
        }

        foreach ( $fieldset->getElements() as $name => $element )
        {
            $types = array_filter( preg_split( '/\s+/', trim(
                $element->getAttribute( 'data-js-type' )
            ) ) );

            $types[] = 'js.paragraph.reflectCss';

            $element->setAttributes( array(
                'data-js-type'                  => implode( ' ', $types ),
                'data-js-reflectcss-selector'   => $selector,
                'data-js-reflectcss-property'   => String::decamelize( $name ),
            ) );
        }

        return $fieldset;
    }

    /**
     * @param   \Zork\Form\FormService  $formService
     * @param   bool                    $customize
     * @return  \Zend\Form\Form
     */
    public function getForm( FormService $formService, $customize = true )
    {
        if ( null === $this->getMapper() )
        {
            return null;
        }

        $form           = new DashboardForm();
        $type           = $this->paragraph->type;
        $customization  = $this->getMapper()->getCustomization();
        $selectors      = $customization->getSelectorsByParagraph( $this->paragraph );
        $metaEdit       = $formService->get( 'Grid\Paragraph\Meta\Edit' );
        $metaCustomize  = $formService->get( 'Grid\Paragraph\Meta\Customize' );

        if ( $metaEdit->has( $type ) )
        {
            $name       = 'paragraph-' . $type;
            $fieldset   = clone $metaEdit->get( $type );
            $form->add( $fieldset->setName( $name ) );
        }

        if ( $customize )
        {
            foreach ( $customization->getFormsByType( $type ) as $key => $fsname )
            {
                if ( $metaCustomize->has( $fsname ) )
                {
                    $name       = 'customize-' . $key;
                    $fieldset   = clone $metaCustomize->get( $fsname );
                    $fieldset->setName( $name )
                             ->setLabel( $fieldset->getLabel() . '.' . $key );

                    if ( isset( $selectors[$key] ) )
                    {
                        $this->reflectCss( $fieldset, (string) $selectors[$key] );
                    }

                    $form->add( $fieldset );
                }
            }
        }

        if ( $form instanceof PrepareElementsAwareInterface )
        {
            $form->prepareElements();
        }

        $form->setHydrator( $this->getMapper() )
             ->bind( $this );

        return $form;
    }

}
