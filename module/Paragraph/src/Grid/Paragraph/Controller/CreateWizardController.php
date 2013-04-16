<?php

namespace Grid\Paragraph\Controller;

use Zend\Form\Form;
use Zend\Mvc\MvcEvent;
use Zend\Stdlib\ArrayUtils;
use Grid\Core\View\Model\WizardStep;
use Grid\Core\Controller\AbstractWizardController;
use Grid\Paragraph\View\Model\CreateWizard\StartStep;

/**
 * CreateWizardController
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class CreateWizardController extends AbstractWizardController
{

    /**
     * Execute the request
     *
     * @param  MvcEvent $event
     * @return mixed
     */
    public function onDispatch( MvcEvent $event )
    {
        $store   = $this->getStore();
        $request = $event->getRequest();
        $service = $this->getServiceLocator();
        $locale  = $service->get( 'AdminLocale' );
        $parent  = $request->getQuery( 'parentId' );

        if ( empty( $store['adminLocale'] ) )
        {
            $store['adminLocale'] = $locale->getCurrent();
        }

        if ( ! empty( $parent ) )
        {
            $store['parentId'] = $parent;
        }

        return parent::onDispatch( $event );
    }

    /**
     * Get step model
     *
     * @param string $step
     * @return \Core\View\Model\WizardStep
     */
    protected function getStep( $step )
    {
        $store      = $this->getStore();
        $formSrv    = $this->getServiceLocator()
                           ->get( 'Form' );

        if ( $step == $this->startStep )
        {
            $form  = $formSrv->get( 'Grid\Paragraph\CreateWizard\Start' );
            $model = new StartStep( array(
                'textDomain' => 'paragraph',
            ) );
        }
        else
        {
            $store['type'] = $step;
            $form   = new Form;
            $create = $formSrv->get( 'Grid\Paragraph\Meta\Create' );
            $model  = new WizardStep( array(
                'textDomain'    => 'paragraph',
            ), array(
                'finish'        => true,
                'next'          => 'finish',
            ) );

            if ( $create->has( $step ) )
            {
                foreach ( $create->get( $step ) as $element )
                {
                    $form->add( $element );
                }
            }
            else
            {
                $edit = $formSrv->get( 'Grid\Paragraph\Meta\Edit' );

                if ( $edit->has( $step ) )
                {
                    foreach ( $edit->get( $step ) as $element )
                    {
                        $form->add( $element );
                    }
                }
                else
                {
                    $model->setOption( 'skip', true );
                }
            }
        }

        return $model->setStepForm( $form );
    }

    /**
     * Cancel action
     */
    public function cancelAction()
    {
        // do nothing special
    }

    /**
     * Finish action
     */
    public function finishAction()
    {
        $store      = $this->getStore();
        $stepStores = $this->getStepStores();
        $type       = $store['type'];
        $parentId   = $store['parentId'];
        $srvLoc     = $this->getServiceLocator();
        $model      = $srvLoc->get( 'Grid\Paragraph\Model\Paragraph\Model' )
                             ->setLocale( $store['adminLocale'] );

        $paragraph = $model->create( ArrayUtils::merge(
            array( 'type' => $type ), $stepStores
        ) );

        if ( ! empty( $parentId ) &&
             $paragraph->save() &&
             $model->appendTo( $paragraph->id, $parentId ) )
        {
            return array(
                'error'     => false,
                'parentId'  => $parentId,
                'paragraph' => $paragraph,
            );
        }
        else
        {
            return array(
                'error'     => true,
            );
        }
    }

}
