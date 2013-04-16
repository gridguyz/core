<?php

namespace Grid\Core\Form;

use Zork\Form\Form;
use Zork\Form\PrepareElementsAwareInterface;

/**
 * Settings
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Settings extends Form
            implements PrepareElementsAwareInterface
{

    /**
     * Prepare additional elements for the form
     *
     * @return void
     */
    public function prepareElements()
    {
        $this->add( array(
            'type'      => 'Zork\Form\Element\Submit',
            'name'      => 'save',
            'options'   => array(
                'text_domain' => 'settings',
            ),
            'attributes'    => array(
                'value'     => 'settings.form.all.submit',
            ),
        ) );
    }

}
