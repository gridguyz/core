<?php

namespace Grid\Core\View\Model;

use Zend\Form\Form;
use Zend\View\Model\ViewModel;

/**
 * WizardStep
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class WizardStep extends ViewModel
{

    /**
     * @const string
     */
    const DEFAULT_TEMPLATE = 'grid/core/wizard/step';

    /**
     * @param array|null $variables
     * @param array|null $options
     */
    public function __construct( $variables = null, $options = null )
    {
        parent::__construct( $variables, $options );
        $this->setTemplate( static::DEFAULT_TEMPLATE );

        if ( ! $this->getVariable( 'textDomain' ) )
        {
            $this->setVariable( 'textDomain', 'default' );
        }
    }

    /**
     * @return \Zend\Form\Form
     */
    public function getStepForm()
    {
        return $this->getVariable( 'form' );
    }

    /**
     * @param \Zend\Form\Form $form
     * @return \Core\View\Model\WizardStep
     */
    public function setStepForm( Form $form )
    {
        return $this->setVariable( 'form', $form );
    }

    /**
     * @return string
     */
    public function getTextDomain()
    {
        return $this->getVariable( 'textDomain' );
    }

    /**
     * @param string $textDomain
     * @return \Core\View\Model\WizardStep
     */
    public function setTextDomain( $textDomain )
    {
        return $this->setVariable( 'textDomain', $textDomain );
    }

    /**
     * @return string
     */
    public function getDescriptionPartial()
    {
        return $this->getVariable( 'descriptionPartial' );
    }

    /**
     * @param string $descriptionPartial
     * @return \Core\View\Model\WizardStep
     */
    public function setDescriptionPartial( $descriptionPartial )
    {
        return $this->setVariable( 'descriptionPartial', $descriptionPartial );
    }

    /**
     * Get previous step
     *
     * @return string|null
     */
    public function getPreviousStep()
    {
        return $this->getOption( 'previous' );
    }

    /**
     * Set previous step
     *
     * @param string|null $step
     * @return \Core\View\Model\WizardStep
     */
    public function setPreviousStep( $step )
    {
        return $this->setOption( 'previous', $step );
    }

    /**
     * Has previous step
     *
     * @return bool
     */
    public function hasPreviousStep()
    {
        return (bool) $this->getOption( 'previous' );
    }

    /**
     * Get next step
     *
     * @return string|null
     */
    public function getNextStep()
    {
        return $this->getOption( 'next' );
    }

    /**
     * Set next step
     *
     * @param string|null $step
     * @return \Core\View\Model\WizardStep
     */
    public function setNextStep( $step )
    {
        return $this->setOption( 'next', $step );
    }

    /**
     * Has previous step
     *
     * @return bool
     */
    public function hasNextStep()
    {
        return (bool) $this->getOption( 'next' ) || ! $this->canFinishWizard();
    }

    /**
     * Can finish wizard
     *
     * @return bool
     */
    public function canFinishWizard()
    {
        return $this->getOption( 'finish', false ) ||
               $this->getNextStep() == 'finish';
    }

    /**
     * Can cancel wizard
     *
     * @return bool
     */
    public function canCancelWizard()
    {
        return $this->getOption( 'cancel', true ) ||
               $this->getNextStep() == 'cancel';
    }

    /**
     * Can skip this step
     *
     * @return bool
     */
    public function canSkip()
    {
        return (bool) $this->getOption( 'skip', false );
    }

}
