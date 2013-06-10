<?php

namespace Grid\Core\View\Model;

use Zend\Form\Form;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * WizardStepTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class WizardStepTest extends TestCase
{

    /**
     * Test methods
     */
    public function testMethods()
    {
        $form = new Form;
        $step = new WizardStep;

        $step->setStepForm( $form );
        $this->assertSame( $form, $step->getStepForm() );

        $step->setTextDomain( 'exampleTextDomain' );
        $this->assertSame( 'exampleTextDomain', $step->getTextDomain() );

        $step->setDescriptionPartial( 'exampleDescriptionPartial' );
        $this->assertSame( 'exampleDescriptionPartial', $step->getDescriptionPartial() );

        $this->assertFalse( $step->canFinishWizard() );
        $this->assertTrue( $step->canCancelWizard() );
        $this->assertFalse( $step->canSkip() );
        $step->setOption( 'finish', true );
        $step->setOption( 'cancel', false );
        $step->setOption( 'skip', true );
        $this->assertTrue( $step->canFinishWizard() );
        $this->assertFalse( $step->canCancelWizard() );
        $this->assertTrue( $step->canSkip() );

        $this->assertFalse( $step->hasPreviousStep() );
        $step->setPreviousStep( 'examplePreviousStep' );
        $this->assertSame( 'examplePreviousStep', $step->getPreviousStep() );
        $this->assertTrue( $step->hasPreviousStep() );

        $this->assertFalse( $step->hasNextStep() );
        $step->setNextStep( 'exampleNextStep' );
        $this->assertSame( 'exampleNextStep', $step->getNextStep() );
        $this->assertTrue( $step->hasNextStep() );
    }

}
