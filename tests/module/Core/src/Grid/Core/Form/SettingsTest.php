<?php

namespace Grid\Core\Form;

use PHPUnit_Framework_TestCase as TestCase;

/**
 * SettingsTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class SettingsTest extends TestCase
{

    /**
     * Test prepare elements
     */
    public function testPrepareElements()
    {
        $form = new Settings;

        $this->assertCount( 0, $form->getElements() );
        $form->prepareElements();
        $this->assertCount( 1, $form->getElements() );

        foreach ( $form->getElements() as $element )
        {
            $this->assertInstanceOf( 'Zend\Form\Element\Submit', $element );
        }
    }

}
