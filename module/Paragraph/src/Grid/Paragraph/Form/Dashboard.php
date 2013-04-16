<?php

namespace Grid\Paragraph\Form;

use Zork\Form\Form;
use Zork\Form\PrepareElementsAwareInterface;

/**
 * Dashboard
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Dashboard extends Form
             implements PrepareElementsAwareInterface
{

    /**
     * Seed attributes
     *
     * @var array
     */
    protected $attributes = array(
        'method'        => 'POST',
        'data-js-type'  => 'js.form.fieldsetTabs',
    );

    /**
     * Prepare additional elements for the form
     *
     * @return void
     */
    public function prepareElements()
    {

    }

}
