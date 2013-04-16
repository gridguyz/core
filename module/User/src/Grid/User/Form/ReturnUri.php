<?php

namespace Grid\User\Form;

use Zork\Form\Form;

/**
 * ReturnUri
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class ReturnUri extends Form
{

    /**
     * Constructor
     */
    public function __construct( $name = null, $options = array() )
    {
        parent::__construct( $name, $options );

        $this->add( array(
            'type'  => 'Zork\Form\Element\Hidden',
            'name'  => 'returnUri',
        ) );
    }

}
