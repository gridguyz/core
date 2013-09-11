<?php

namespace Grid\Customize\Controller;

use Zork\Stdlib\Message;
use Grid\Core\Controller\AbstractListController;

/**
 * GroupAdminController
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class AdminController extends AbstractListController
{

    /**
     * Define rights required to use this controller
     *
     * @var array
     */
    protected $aclRights = array(
        '' => array(
            'customize' => 'edit'
        ),
    );

    /**
     * Get paginator list
     *
     * @return \Zend\Paginator\Paginator
     */
    protected function getPaginator()
    {
        return $this->getServiceLocator()
                    ->get( 'Grid\Customize\Model\Rule\Model' )
                    ->getPaginator();
    }

    /**
     * Edit a rule
     */
    public function editAction()
    {
        $params     = $this->params();
        $request    = $this->getRequest();
        $locator    = $this->getServiceLocator();
        $ruleId     = $params->fromRoute( 'id' );
        $model      = $locator->get( 'Grid\Customize\Model\Rule\Model' );
        $form       = $locator->get( 'Form' )
                              ->get( 'Grid\Customize\Rule' );

        if ( empty( $ruleId ) )
        {
            $rule = $model->create( array() );
        }
        else
        {
            $rule = $model->find( $ruleId );

            if ( empty( $rule ) )
            {
                $this->getResponse()
                     ->setStatusCode( 404 );

                return;
            }
        }

        /* @var $form \Zend\Form\Form */
        $form->setHydrator( $model->getMapper() )
             ->bind( $rule );

        if ( $request->isPost() )
        {
            $form->setData( $request->getPost() );

            if ( $form->isValid() && $rule->save() )
            {
                $this->messenger()
                     ->add( 'customize.form.success',
                            'customize', Message::LEVEL_INFO );

                return $this->redirect()
                            ->toRoute( 'Grid\Customize\Admin\List', array(
                                'locale' => (string) $this->locale(),
                            ) );
            }
            else
            {
                $this->messenger()
                     ->add( 'customize.form.failed',
                            'customize', Message::LEVEL_ERROR );
            }
        }

        $form->setCancel(
            $this->url()
                 ->fromRoute( 'Grid\Customize\Admin\List', array(
                        'locale' => (string) $this->locale(),
                    ) )
        );

        return array(
            'form'  => $form,
            'rule'  => $rule,
        );
    }

    /**
     * Edit extra css
     */
    public function editExtraAction()
    {
        $request    = $this->getRequest();
        $locator    = $this->getServiceLocator();
        $model      = $locator->get( 'Grid\Customize\Model\Extra\Model' );
        $form       = $locator->get( 'Form' )
                              ->get( 'Grid\Customize\Extra' );
        $extra      = $model->find();

        /* @var $form \Zend\Form\Form */
        $form->setHydrator( $model->getMapper() )
             ->bind( $extra );

        if ( $request->isPost() )
        {
            $form->setData( $request->getPost() );

            if ( $form->isValid() && $extra->save() )
            {
                $this->messenger()
                     ->add( 'customize.form.success',
                            'customize', Message::LEVEL_INFO );

                return $this->redirect()
                            ->toRoute( 'Grid\Customize\Admin\EditExtra', array(
                                'locale' => (string) $this->locale(),
                            ), array(
                                'query' => array(
                                    'refresh' => true,
                                ),
                            ) );
            }
            else
            {
                $this->messenger()
                     ->add( 'customize.form.failed',
                            'customize', Message::LEVEL_ERROR );
            }
        }

        return array(
            'form'  => $form,
            'extra' => $extra,
        );
    }

    /**
     * Delete a rule
     */
    public function deleteAction()
    {
        $params     = $this->params();
        $locator    = $this->getServiceLocator();
        $model      = $locator->get( 'Grid\Customize\Model\Rule\Model' );
        $rule       = $model->find( $params->fromRoute( 'id' ) );

        if ( empty( $rule ) )
        {
            $this->getResponse()
                 ->setStatusCode( 404 );

            return;
        }

        if ( $rule->delete() )
        {
            $this->messenger()
                 ->add( 'customize.action.delete.success',
                        'customize', Message::LEVEL_INFO );
        }
        else
        {
            $this->messenger()
                 ->add( 'customize.action.delete.failed',
                        'customize', Message::LEVEL_ERROR );
        }

        return $this->redirect()
                    ->toRoute( 'Grid\Customize\Admin\List', array(
                        'locale' => (string) $this->locale(),
                    ) );
    }

}
