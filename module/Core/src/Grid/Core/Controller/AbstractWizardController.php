<?php

namespace Grid\Core\Controller;

use Zend\Stdlib\ArrayUtils;
use Zend\View\Model\ViewModel;
use Grid\Core\View\Model\WizardStep;
use Zend\Mvc\Controller\AbstractActionController;
use Zork\Session\ContainerAwareTrait as SessionContainerAwareTrait;

/**
 * Wizard ControllerAbstarct
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
abstract class AbstractWizardController extends AbstractActionController
{

    use SessionContainerAwareTrait;

    /**
     * @const string
     */
    const STEP_DEFAULT_START    = 'start';

    /**
     * @const string
     */
    const STEP_CANCEL           = 'cancel';

    /**
     * @const string
     */
    const STEP_FINISH           = 'finish';

    /**
     * Starting step
     *
     * @var string
     */
    protected $startStep        = self::STEP_DEFAULT_START;

    /**
     * Cancel-wizard submit spec
     *
     * @var array
     */
    protected $cancelWizardSpec = array(
        'type'  => 'Zork\Form\Element\Submit',
        'name'  => 'cancel',
        'options'   => array(
            'text_domain'   => 'default',
        ),
        'attributes'    => array(
            'value'             => 'default.cancel',
            'formnovalidate'    => true,
        ),
    );

    /**
     * Previous-step submit spec
     *
     * @var array
     */
    protected $previousStepSpec = array(
        'type'  => 'Zork\Form\Element\Submit',
        'name'  => 'previous',
        'options'   => array(
            'text_domain'   => 'default',
        ),
        'attributes'    => array(
            'value'             => 'default.previous',
            'formnovalidate'    => true,
        ),
    );

    /**
     * Next-step submit spec
     *
     * @var array
     */
    protected $nextStepSpec     = array(
        'type'  => 'Zork\Form\Element\Submit',
        'name'  => 'next',
        'options'   => array(
            'text_domain'   => 'default',
        ),
        'attributes'    => array(
            'value'     => 'default.next',
        ),
    );

    /**
     * Finish-wizard submit spec
     *
     * @var array
     */
    protected $finishWizardSpec = array(
        'type'  => 'Zork\Form\Element\Submit',
        'name'  => 'finish',
        'options'   => array(
            'text_domain'   => 'default',
        ),
        'attributes'    => array(
            'value'     => 'default.finish',
        ),
    );

    /**
     * Get redirect uri according to a step
     *
     * @param   string $step
     * @return  string
     */
    protected function redirectToStep( $step )
    {
        $routeMatch = $this->getEvent()
                           ->getRouteMatch();

        return $this->redirect()
                    ->toRoute( $routeMatch->getMatchedRouteName(),
                               ArrayUtils::merge(
                                   $routeMatch->getParams(),
                                   array( 'step' => $step )
                               ) );
    }

    /**
     * Get session container
     *
     * @return  \Zend\Session\Container
     */
    protected function getStore()
    {
        $store = $this->getSessionContainer();

        if ( empty( $store['stack'] ) )
        {
            $store['stack'] = array();
        }

        if ( empty( $store['steps'] ) )
        {
            $store['steps'] = array();
        }

        return $store;
    }

    /**
     * Push step-stack
     *
     * @param   string $action
     * @return  \Grid\Core\Controller\AbstractWizardController
     */
    protected function pushStepStack( $step )
    {
        $store = $this->getStore();
        $stack = $store['stack'];
        array_push( $stack, (string) $step );
        $store['stack'] = $stack;
        return $this;
    }

    /**
     * Pop step-stack
     *
     * @return  string
     */
    protected function popStepStack()
    {
        $store  = $this->getStore();
        $stack  = $store['stack'];
        $step   = array_pop( $stack );
        $store['stack'] = $stack;
        return $step;
    }

    /**
     * Top step-stack
     *
     * @return  string
     */
    protected function topStepStack()
    {
        $store  = $this->getStore();
        $stack  = $store['stack'];

        if ( empty( $stack ) )
        {
            return null;
        }

        return end( $stack );
    }

    /**
     * Unset step-stack
     *
     * @return  \Grid\Core\Controller\AbstractWizardController
     */
    protected function unsetStepStack()
    {
        $store  = $this->getStore();
        $store['stack'] = array();
        return $this;
    }

    /**
     * Get step-store
     *
     * @param   string $action
     * @return  array
     */
    public function getStepStore( $step )
    {
        $store  = $this->getStore();
        $steps  = $store['steps'];
        $step   = (string) $step;

        if ( empty( $steps[$step] ) )
        {
            $steps[$step]   = array();
            $store['steps'] = $steps;
        }

        return $steps[$step];
    }

    /**
     * Get step-stores
     *
     * @param   bool $flat
     * @return  array
     */
    public function getStepStores( $flat = true )
    {
        $store  = $this->getStore();
        $steps  = $store['steps'];
        $result = array();

        foreach ( $store['stack'] as $step )
        {
            $stepStore = $steps[$step];

            if ( $flat )
            {
                $result = array_merge( $result, $stepStore );
            }
            else
            {
                $result[$step] = $stepStore;
            }
        }

        return $result;
    }

    /**
     * Set step-store
     *
     * @param   string  $action
     * @param   array   $data
     * @return  \Grid\Core\Controller\AbstractWizardController
     */
    public function setStepStore( $step, array $data )
    {
        $store  = $this->getStore();
        $steps  = $store['steps'];
        $step   = (string) $step;
        $steps[$step]   = $data;
        $store['steps'] = $steps;
        return $this;
    }

    /**
     * Get step model
     *
     * @param   string $step
     * @return  \Grid\Core\View\Model\WizardStep
     */
    abstract protected function getStep( $step );

    /* *
     * Called when setpping from $from to $to
     *
     * @param string $from
     * @param string $to
     * @return bool return false to prevent stepping
     * /
    protected function stepping( $from, $to )
    {
        return true;
    } */

    /**
     * Run a single step (which is not finish or cancel)
     *
     * @param   string      $step
     * @param   WizardStep  $model
     * @return  string|null the step to redirect to
     */
    protected function runStep( $step, WizardStep $model = null )
    {
        $request    = $this->getRequest();
        $isValid    = false;
        $isPost     = $request->isPost();
        $isCancel   = $isPost && $request->getPost( 'cancel' );
        $isPrevious = $isPost && $request->getPost( 'previous' );
        $isNext     = $isPost && $request->getPost( 'next' );
        $isFinish   = $isPost && $request->getPost( 'finish' );
        $jumpTo     = $request->getQuery( 'jump' );
        $values     = $this->getStepStore( $step );
        $form       = $model ? $model->getStepForm() : null;
        $redirect   = null;

        if ( ! empty( $model ) && $model->canSkip() )
        {
            $this->setStepStore( $step, array() );
            $this->pushStepStack( $step );
            $redirect = $model->getNextStep();
        }
        else if ( empty( $model ) || empty( $form ) )
        {
            $redirect = $this->startStep;
        }
        else
        {
            if ( ! empty( $values ) )
            {
                $form->setData( $values );
            }

            if ( $isPost && ( $isNext || $isFinish ) )
            {
                $form->setData( $request->getPost() );
                $isValid = $form->isValid();
            }

            if ( $isNext && $isValid && ( $redirect = $model->getNextStep() ) )
            {
                $this->setStepStore( $step, $form->getData() );
                $this->pushStepStack( $step );
            }
            else if ( $isPrevious && ( $redirect = $model->getPreviousStep() ) )
            {
                $top = $this->topStepStack();

                if ( $redirect == $top )
                {
                    $this->popStepStack();
                }
            }
            else if ( $jumpTo )
            {
                $store = $this->getStore();

                while ( ! empty( $store['stack'] ) &&
                        $jumpTo != $this->popStepStack() );

                $redirect = empty( $store['stack'] )
                    ? $this->startStep
                    : $jumpTo;
            }
            else if ( $isFinish && $isValid && $model->canFinishWizard() )
            {
                $this->setStepStore( $step, $form->getData() );
                $this->pushStepStack( $step );
                $redirect = self::STEP_FINISH;
            }
            else if ( $isCancel && $model->canCancelWizard() )
            {
                $redirect = self::STEP_CANCEL;
            }
        }

     /* if ( $redirect && $redirect != $step )
        {
            if ( ! $this->stepping( $step, $redirect ) )
            {
                $redirect = null;
            }
        } */

        return $redirect;
    }

    /**
     * Step action
     */
    public function stepAction()
    {
        $step = $this->params()
                     ->fromRoute( 'step', $this->startStep );

        if ( self::STEP_FINISH == $step || self::STEP_CANCEL == $step )
        {
            $controller = preg_replace( '/Controller$/', '', get_class( $this ) );
            $view       = new ViewModel( array(
                'content' => $this->forward()
                                  ->dispatch( $controller, array(
                                      'locale' => (string) $this->locale(),
                                      'action' => $step,
                                  ) )
            ) );

            $this->getStore()
                 ->setExpirationSeconds( 0 )
                 ->exchangeArray( array() );

            return $view->setTemplate( 'grid/core/wizard/' . $step );
        }
        else
        {
            if ( $this->startStep == $step )
            {
                $this->unsetStepStack();
            }

            $stepModel = $this->getStep( $step );

            if ( $stepModel instanceof WizardStep )
            {
                $store = $this->getStore();

                $stepModel->setVariables( array(
                    'step'  => $step,
                    'stack' => $store['stack'],
                ) );

                if ( ! $stepModel->getPreviousStep() )
                {
                    $stepModel->setPreviousStep( $this->topStepStack() );
                }
            }

            $redirect = $this->runStep( $step, $stepModel );

            if ( $redirect )
            {
                return $this->redirectToStep( $redirect );
            }

            if ( $stepModel instanceof WizardStep )
            {
                $form = $stepModel->getStepForm();

                if ( $form )
                {
                    if ( $stepModel->canCancelWizard() )
                    {
                        $form->add( $this->cancelWizardSpec );
                    }

                    if ( $stepModel->canFinishWizard() )
                    {
                        $form->add( $this->finishWizardSpec );
                    }

                    if ( $stepModel->hasNextStep() )
                    {
                        $form->add( $this->nextStepSpec );
                    }

                    if ( $stepModel->hasPreviousStep() )
                    {
                        $form->add( $this->previousStepSpec );
                    }
                }
            }

            return $stepModel;
        }
    }

    /**
     * Cancel action
     */
    abstract public function cancelAction();

    /**
     * Finish action
     */
    abstract public function finishAction();

}
