<?php

namespace Grid\User\Controller;

use Zork\Stdlib\Message;
use Grid\Core\Controller\AbstractListController;

/**
 * GroupAdminController
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class GroupAdminController extends AbstractListController
{

    /**
     * Get paginator list
     *
     * @return \Zend\Paginator\Paginator
     */
    protected function getPaginator()
    {
        return $this->getServiceLocator()
                    ->get( 'Grid\User\Model\User\Group\Model' )
                    ->getPaginator();
    }

    /**
     * Create a group
     */
    public function createAction()
    {
        $request    = $this->getRequest();
        $locator    = $this->getServiceLocator();
        $model      = $locator->get( 'Grid\User\Model\User\Group\Model' );
        $form       = $locator->get( 'Form' )
                              ->get( 'Grid\User\Group' );
        $group      = $model->create( array() );

        if ( ! $this->getPermissionsModel()
                    ->isAllowed( 'user.group', 'create' ) )
        {
            $this->getResponse()
                 ->setStatusCode( 403 );

            return;
        }

        /* @var $form \Zend\Form\Form */
        $form->setHydrator( $model->getMapper() )
             ->bind( $group );

        if ( $request->isPost() )
        {
            $form->setData( $request->getPost() );

            if ( $form->isValid() && $group->save() )
            {
                $this->messenger()
                     ->add( 'user.form.group.success',
                            'user', Message::LEVEL_INFO );

                return $this->redirect()
                            ->toRoute( 'Grid\User\GroupAdmin\List', array(
                                'locale' => (string) $this->locale(),
                            ) );
            }
            else
            {
                $this->messenger()
                     ->add( 'user.form.group.failed',
                            'user', Message::LEVEL_ERROR );
            }
        }

        $form->setCancel(
            $this->url()
                 ->fromRoute( 'Grid\User\GroupAdmin\List', array(
                        'locale' => (string) $this->locale(),
                    ) )
        );

        return array(
            'form'  => $form,
            'group' => $group,
        );
    }

    /**
     * Edit a group
     */
    public function editAction()
    {
        $params     = $this->params();
        $request    = $this->getRequest();
        $locator    = $this->getServiceLocator();
        $model      = $locator->get( 'Grid\User\Model\User\Group\Model' );
        $form       = $locator->get( 'Form' )
                              ->get( 'Grid\User\Group' );
        $group      = $model->find( $params->fromRoute( 'id' ) );

        if ( empty( $group ) )
        {
            $this->getResponse()
                 ->setStatusCode( 404 );

            return;
        }

        if ( ! $this->getPermissionsModel()
                    ->isAllowed( $group, 'edit' ) )
        {
            $this->getResponse()
                 ->setStatusCode( 403 );

            return;
        }

        /* @var $form \Zend\Form\Form */
        $form->setHydrator( $model->getMapper() )
             ->bind( $group );

        if ( $request->isPost() )
        {
            $form->setData( $request->getPost() );

            if ( $form->isValid() && $group->save() )
            {
                $this->messenger()
                     ->add( 'user.form.group.success',
                            'user', Message::LEVEL_INFO );

                return $this->redirect()
                            ->toRoute( 'Grid\User\GroupAdmin\List', array(
                                'locale' => (string) $this->locale(),
                            ) );
            }
            else
            {
                $this->messenger()
                     ->add( 'user.form.group.failed',
                            'user', Message::LEVEL_ERROR );
            }
        }

        $form->setCancel(
            $this->url()
                 ->fromRoute( 'Grid\User\GroupAdmin\List', array(
                        'locale' => (string) $this->locale(),
                    ) )
        );

        return array(
            'form'  => $form,
            'group' => $group,
        );
    }

    /**
     * Set to default a group
     */
    public function setDefaultAction()
    {
        $params     = $this->params();
        $locator    = $this->getServiceLocator();
        $model      = $locator->get( 'Grid\User\Model\User\Group\Model' );
        $group      = $model->find( $params->fromRoute( 'id' ) );

        if ( empty( $group ) )
        {
            $this->getResponse()
                 ->setStatusCode( 404 );

            return;
        }

        if ( $group->predefined )
        {
            $this->getResponse()
                 ->setStatusCode( 403 );

            return;
        }

        if ( ! $this->getPermissionsModel()
                    ->isAllowed( $group, 'edit' ) )
        {
            $this->getResponse()
                 ->setStatusCode( 403 );

            return;
        }

        if ( ! $group->default )
        {
            $group->default = true;

            if ( $group->save() )
            {
                $this->messenger()
                     ->add( 'user.form.group.success',
                            'user', Message::LEVEL_INFO );
            }
            else
            {
                $this->messenger()
                     ->add( 'user.form.group.failed',
                            'user', Message::LEVEL_ERROR );
            }
        }

        return $this->redirect()
                    ->toRoute( 'Grid\User\GroupAdmin\List', array(
                        'locale' => (string) $this->locale(),
                    ) );
    }

    /**
     * Grant rights for a group
     */
    public function grantAction()
    {
        $params     = $this->params();
        $request    = $this->getRequest();
        $locator    = $this->getServiceLocator();
        $groupModel = $locator->get( 'Grid\User\Model\User\Group\Model' );
        $group      = $groupModel->find( $params->fromRoute( 'id' ) );

        if ( empty( $group ) )
        {
            $this->getResponse()
                 ->setStatusCode( 404 );

            return;
        }

        if ( ! $this->getPermissionsModel()
                    ->isAllowed( $group, 'grant' ) )
        {
            $this->getResponse()
                 ->setStatusCode( 403 );

            return;
        }

        $rightModel = $locator->get( 'Grid\User\Model\User\Right\Model' );
        $rights     = $rightModel->findAllByGroup( $group->id );

        if ( $request->isPost() )
        {
            $data = $request->getPost();

            if ( isset( $data['save'] ) && ! empty( $data['rights'] ) )
            {
                foreach ( $data['rights'] as $rightId => $grant )
                {
                    $rightModel->grantToGroup( $rightId, $group->id, $grant );
                }

                $this->messenger()
                     ->add( 'user.form.grant.success',
                            'user', Message::LEVEL_INFO );

                return $this->redirect()
                            ->toRoute( 'Grid\User\GroupAdmin\List', array(
                                'locale' => (string) $this->locale(),
                            ) );
            }
        }

        return array(
            'group'     => $group,
            'rights'    => $rights,
        );
    }

    /**
     * Delete a group
     */
    public function deleteAction()
    {
        $params     = $this->params();
        $locator    = $this->getServiceLocator();
        $model      = $locator->get( 'Grid\User\Model\User\Group\Model' );
        $group      = $model->find( $params->fromRoute( 'id' ) );

        if ( empty( $group ) )
        {
            $this->getResponse()
                 ->setStatusCode( 404 );

            return;
        }

        if ( $group->predefined )
        {
            $this->getResponse()
                 ->setStatusCode( 403 );

            return;
        }

        if ( ! $this->getPermissionsModel()
                    ->isAllowed( $group, 'delete' ) )
        {
            $this->getResponse()
                 ->setStatusCode( 403 );

            return;
        }

        if ( $group->default )
        {
            $this->getResponse()
                 ->setStatusCode( 403 );

            return;
        }

        if ( $group->delete() )
        {
            $this->messenger()
                 ->add( 'user.action.delete.success',
                        'user', Message::LEVEL_INFO );
        }
        else
        {
            $this->messenger()
                 ->add( 'user.action.delete.failed',
                        'user', Message::LEVEL_ERROR );
        }

        return $this->redirect()
                    ->toRoute( 'Grid\User\GroupAdmin\List', array(
                        'locale' => (string) $this->locale(),
                    ) );
    }

}
