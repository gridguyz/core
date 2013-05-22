<?php

namespace Grid\Paragraph\Model\Dashboard;

use Traversable;
use Zend\Stdlib\ArrayUtils;
use Grid\Paragraph\Model\Paragraph;

/**
 * Definitions
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Customization
{

    /**
     * @var array
     */
    protected $selectors    = array();

    /**
     * @var array
     */
    protected $forms        = array();

    /**
     * @param array|\Traversable $selectors
     * @param array|\Traversable $forms
     */
    public function __construct( $selectors, $forms )
    {
        if ( $selectors instanceof Traversable )
        {
            $selectors = ArrayUtils::iteratorToArray( $selectors );
        }

        if ( $forms instanceof Traversable )
        {
            $forms = ArrayUtils::iteratorToArray( $forms );
        }

        $this->selectors    = (array) $selectors;
        $this->forms        = (array) $forms;
    }

    /**
     * @param string $type
     * @return array
     */
    public function getFormsByType( $type )
    {
        return empty( $this->forms[$type] )
            ? array()
            : (array) $this->forms[$type];
    }

    /**
     * @param \Paragraph\Model\Dashboard\Paragraph\StructureInterface $paragraph
     * @return array
     */
    public function getSelectorsByParagraph( Paragraph\StructureInterface $paragraph )
    {
        $result = array();
        $id     = $paragraph->id;
        $type   = $paragraph->type;

        if ( ! empty( $id ) &&
             ! empty( $type ) &&
             ! empty( $this->forms[$type] ) )
        {
            $replace = array(
                '%id%'      => $id,
                '%type%'    => $type,
            );

            foreach ( $this->forms[$type] as $key => $_ )
            {
                if ( ! empty( $this->selectors[$key] ) )
                {
                    $result[$key] = strtr( $this->selectors[$key], $replace );
                }
            }
        }

        return $result;
    }

}
