<?php

namespace Grid\User\Controller;

use Zork\Stdlib\Message;
use Zend\Form\Form;
use Zend\Authentication\AuthenticationService;
use Zend\Mvc\Controller\AbstractActionController;

/**
 * AdminController
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class DatasheetController extends AbstractActionController
{

    /**
     * @var \User\Model\Permissions\Model
     */
    protected $permissionsModel;

    /**
     * @return \User\Model\Permissions\Model
     */
    public function getPermissionsModel()
    {
        if ( null === $this->permissionsModel )
        {
            $this->permissionsModel = $this->getServiceLocator()
                                           ->get( 'Grid\User\Model\Permissions\Model' );
        }

        return $this->permissionsModel;
    }

    /**
     * @param \Zend\Form\Form $form
     */
    protected function fixUserForm( Form & $form, $userId )
    {
        $auth       = new AuthenticationService;
        $groupId    = $form->get( 'groupId' );
        $groups     = $groupId->getValueOptions();

        if ( empty( $groups ) ||
             $auth->getIdentity()->id == $userId )
        {
            $form->remove( 'groupId' );
        }
    }

    /**
     * View a user
     */
    public function viewAction()
    {
        $params     = $this->params();
        $service    = $this->getServiceLocator();
        $displayn   = $params->fromRoute( 'displayName' );
        $model      = $service->get( 'Grid\User\Model\User\Model' );
        $user       = $model->findByDisplayName( $displayn );

        $this->paragraphLayout();

        if ( empty( $user ) )
        {
            $this->getResponse()
                 ->setStatusCode( 404 );

            return;
        }

        return array(
            'user'      => $user,
            'edit'      => $this->getPermissionsModel()
                                ->isAllowed( $user, 'edit' ),
            'password'  => $this->getPermissionsModel()
                                ->isAllowed( $user, 'password' ),
            'delete'    => $this->getPermissionsModel()
                                ->isAllowed( $user, 'delete' ),
        );
    }

    /**
     * Edit a user
     */
    public function editAction()
    {
        $params     = $this->params();
        $request    = $this->getRequest();
        $locator    = $this->getServiceLocator();
        $displayn   = $params->fromRoute( 'displayName' );
        $model      = $locator->get( 'Grid\User\Model\User\Model' );
        $form       = $locator->get( 'Form' )
                              ->create( 'Grid\User\Edit' );
        $user       = $model->findByDisplayName( $displayn );

        $this->paragraphLayout();

        if ( empty( $user ) )
        {
            $this->getResponse()
                 ->setStatusCode( 404 );

            return;
        }

        if ( ! $this->getPermissionsModel()
                    ->isAllowed( $user, 'edit' ) )
        {
            $this->getResponse()
                 ->setStatusCode( 403 );

            return;
        }

        $this->fixUserForm( $form, $user->id );

        /* @var $form \Zend\Form\Form */
        $form->setHydrator( $model->getMapper() )
             ->bind( $user );

        if ( $request->isPost() )
        {
            $form->setData( $request->getPost() );

            if ( $form->isValid() && $user->save() )
            {
                $this->messenger()
                     ->add( 'user.form.edit.success',
                            'user', Message::LEVEL_INFO );

                return $this->redirect()
                            ->toRoute( 'Grid\User\Datasheet\View', array(
                                'locale'        => (string) $this->locale(),
                                'displayName'   => $user->displayName,
                            ) );
            }
            else
            {
                $this->messenger()
                     ->add( 'user.form.edit.failed',
                            'user', Message::LEVEL_ERROR );
            }
        }

        $form->setCancel(
            $this->url()
                 ->fromRoute( 'Grid\User\Datasheet\View', array(
                        'locale'        => (string) $this->locale(),
                        'displayName'   => $user->displayName,
                    ) )
        );

        return array(
            'form'  => $form,
            'user'  => $user
        );
    }

    /**
     * Edit a user's password
     */
    public function passwordAction()
    {
        $params     = $this->params();
        $request    = $this->getRequest();
        $locator    = $this->getServiceLocator();
        $displayn   = $params->fromRoute( 'displayName' );
        $model      = $locator->get( 'Grid\User\Model\User\Model' );
        $form       = $locator->get( 'Form' )
                              ->create( 'Grid\User\Password' );
        $user       = $model->findByDisplayName( $displayn );

        $this->paragraphLayout();

        if ( empty( $user ) )
        {
            $this->getResponse()
                 ->setStatusCode( 404 );

            return;
        }

        if ( ! $this->getPermissionsModel()
                    ->isAllowed( $user, 'password' ) )
        {
            $this->getResponse()
                 ->setStatusCode( 403 );

            return;
        }

        /* @var $form \Zend\Form\Form */
        $form->setHydrator( $model->getMapper() )
             ->bind( $user );

        if ( $request->isPost() )
        {
            $form->setData( $request->getPost() );

            if ( $form->isValid() && $user->save() )
            {
                $this->messenger()
                     ->add( 'user.form.passwordChange.success',
                            'user', Message::LEVEL_INFO );

                return $this->redirect()
                            ->toRoute( 'Grid\User\Datasheet\View', array(
                                'locale'        => (string) $this->locale(),
                                'displayName'   => $user->displayName,
                            ) );
            }
            else
            {
                $this->messenger()
                     ->add( 'user.form.passwordChange.failed',
                            'user', Message::LEVEL_ERROR );
            }
        }

        $form->setCancel(
            $this->url()
                 ->fromRoute( 'Grid\User\Datasheet\View', array(
                        'locale'        => (string) $this->locale(),
                        'displayName'   => $user->displayName,
                    ) )
        );

        return array(
            'form'  => $form,
            'user'  => $user,
        );
    }

    /**
     * Delete a user
     */
    public function deleteAction()
    {
        $params     = $this->params();
        $locator    = $this->getServiceLocator();
        $displayn   = $params->fromRoute( 'displayName' );
        $model      = $locator->get( 'Grid\User\Model\User\Model' );
        $user       = $model->findByDisplayName( $displayn );

        if ( empty( $user ) )
        {
            $this->paragraphLayout();

            $this->getResponse()
                 ->setStatusCode( 404 );

            return;
        }

        $id = $user->id;

        if ( ! $this->getPermissionsModel()
                    ->isAllowed( $user, 'delete' ) )
        {
            $this->paragraphLayout();

            $this->getResponse()
                 ->setStatusCode( 403 );

            return;
        }

        if ( $user->delete() )
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

        $auth = new AuthenticationService;

        if ( $auth->hasIdentity() &&
             $auth->getIdentity()->id == $id )
        {
            return $this->redirect()
                        ->toRoute( 'Grid\User\Authentication\Logout', array(
                            'locale'    => (string) $this->locale(),
                            'immediate' => 'immediate',
                            'returnUri' => '/',
                        ) );
        }
        else
        {
            return $this->redirect()
                        ->toUri( '/' );
        }
    }

}
