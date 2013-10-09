<?php

namespace Grid\Paragraph\View\Model;

use Zend\View\Model\ViewModel;

/**
 * MetaContent
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class MetaContent extends ViewModel
{

    /**
     * Meta content's name
     *
     * @var string
     */
    protected $name;

    /**
     * Constructor
     *
     * @param   string                  $name
     * @param   null|array|Traversable  $variables
     * @param   array|Traversable       $options
     */
    public function __construct( $name, $variables = null, $options = null )
    {
        $this->setName( $name );
        parent::__construct( $variables, $options );
    }

    /**
     * Get meta content's name
     *
     * @return  string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set meta content's name
     *
     * @param   string  $name
     * @return  MetaContent
     */
    public function setName( $name )
    {
        $this->name = (string) $name;
        return $this;
    }

}
