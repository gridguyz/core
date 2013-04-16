<?php

namespace Grid\Paragraph\View\Model\CreateWizard;

use Grid\Core\View\Model\WizardStep;

/**
 * StartStep
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class StartStep extends WizardStep
{

    /**
     * Get next step
     *
     * @return string|null
     */
    public function getNextStep()
    {
        $next = parent::getNextStep();

        if ( empty( $next ) )
        {
            $form = $this->getStepForm();

            if ( ! empty( $form ) )
            {
                $type = $form->get( 'type' );

                if ( ! empty( $type ) )
                {
                    $next = $type->getValue();
                }
            }
        }

        return $next;
    }

}
