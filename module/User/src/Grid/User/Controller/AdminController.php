<?php

namespace Grid\User\Controller;

use Zork\Stdlib\Message;
use Zend\Form\Form;
use Zork\Data\Table;
use Zork\Data\Transform\Translate;
use Grid\Core\Controller\AbstractListExportController;

/**
 * AdminController
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class AdminController extends AbstractListExportController
{

    /**
     * @var string
     */
    protected $exportFileName = 'users';

    /**
     * @var array
     */
    protected $exportFieldTypes = array(
        'id'            => Table::INTEGER,
        'email'         => Table::STRING,
        'displayName'   => Table::STRING,
        'locale'        => Table::STRING,
        'confirmed'     => Table::BOOLEAN,
        'state'         => Table::STRING,
        'groupName'     => Table::STRING,
    );

    /**
     * @var array
     */
    protected $exportFieldNames = array(
        'id'            => 'user.list.column.id.title',
        'email'         => 'user.list.column.email.title',
        'displayName'   => 'user.list.column.displayName.title',
        'locale'        => 'user.list.column.locale.title',
        'confirmed'     => 'user.list.column.confirmed.title',
        'state'         => 'user.list.column.state.title',
        'groupName'     => 'user.list.column.groupName.title',
    );

    /**
     * @return array
     */
    protected function getExportFieldTypes()
    {
        $translator = $this->getServiceLocator()
                           ->get( 'Zend\I18n\Translator\Translator' );

        return array_merge(
            parent::getExportFieldTypes(),
            array(
                'locale'    => new Translate( $translator, 'locale.sub.', '', 'locale' ),
                'state'     => new Translate( $translator, 'user.state.', '', 'user' ),
            )
        );
    }

    /**
     * @param \Zend\Form\Form $form
     */
    protected function fixUserForm( Form & $form, $userId = null )
    {
        $auth       = $this->getAuthenticationService();
        $groupId    = $form->get( 'groupId' );
        $groups     = $groupId->getValueOptions();

        if ( empty( $groups ) ||
             $auth->getIdentity()->id == $userId )
        {
            $form->remove( 'groupId' );
        }
    }

    /**
     * Get paginator list
     *
     * @return \Zend\Paginator\Paginator
     */
    protected function getPaginator()
    {
        return $this->getServiceLocator()
                    ->get( 'Grid\User\Model\User\Model' )
                    ->getPaginator();
    }

    /**
     * Create a user
     */
    public function createAction()
    {
        $request          = $this->getRequest();
        $locator          = $this->getServiceLocator();
        $model            = $locator->get( 'Grid\User\Model\User\Model' );
        $form             = $locator->get( 'Form' )
                                    ->create( 'Grid\User\Create' );
        $user             = $model->create( array() );
        $datasheetService = $locator->get( 'Grid\User\Datasheet\Service' );

        $allowedInGroups = $this->getPermissionsModel()
                                ->allowedUserGroups('create');

        if ( empty($allowedInGroups) )
        {
            $this->getResponse()
                 ->setStatusCode( 403 );

            return;
        }

        $this->fixUserForm( $form );

        /* @var $form \Zend\Form\Form */
        $form->setHydrator( $model->getMapper() )
             ->bind( $user );

        if ( $request->isPost() )
        {
            $form->setData( $request->getPost() );

            if ( $form->isValid() && $datasheetService->save($user) )
            {
                $this->messenger()
                     ->add( 'user.form.create.success',
                            'user', Message::LEVEL_INFO );

                $confirm = $this->url()
                                ->fromRoute( 'Grid\User\Manage\Confirm', array(
                                    'locale' => (string) $this->locale(),
                                    'hash'   => $this->getServiceLocator()
                                                     ->get( 'Grid\User\Model\ConfirmHash' )
                                                     ->create( $user->email ),
                                ) );

                $this->getServiceLocator()
                     ->get( 'Grid\Mail\Model\Template\Sender' )
                     ->prepare( array(
                         'template' => 'user.admin-create',
                         'locale'   => $user->locale,
                     ) )
                     ->send( array(
                         'email'        => $user->email,
                         'display_name' => $user->displayName,
                         'confirm_url'  => $confirm,
                     ), array(
                         $user->email   => $user->displayName,
                     ) );

                return $this->redirect()
                            ->toRoute( 'Grid\User\Admin\List', array(
                                'locale' => (string) $this->locale(),
                            ) );
            }
            else
            {
                $this->messenger()
                     ->add( 'user.form.create.failed',
                            'user', Message::LEVEL_ERROR );
            }
        }

        $form->setCancel(
            $this->url()
                 ->fromRoute( 'Grid\User\Admin\List', array(
                        'locale' => (string) $this->locale(),
                    ) )
        );

        return array(
            'form'  => $form,
            'user'  => $user,
        );
    }

    /**
     * Edit a user
     */
    public function editAction()
    {
        $params           = $this->params();
        $request          = $this->getRequest();
        $locator          = $this->getServiceLocator();
        $model            = $locator->get( 'Grid\User\Model\User\Model' );
        $form             = $locator->get( 'Form' )
                                    ->create( 'Grid\User\Edit' );
        $user             = $model->find( $params->fromRoute( 'id' ) );
        $datasheetService = $locator->get( 'Grid\User\Datasheet\Service' );

        $identity         = $locator->get('Zend\Authentication\AuthenticationService')
                                    ->getIdentity();
        
        if ( empty( $user ) )
        {
            $this->getResponse()
                 ->setStatusCode( 404 );

            return;
        }

                
        if( !$this->getPermissionsModel()->isAllowed( $user, 'edit' ) 
            || 
            ( $identity->groupId > 2 && $user->groupId < 3 )
          )
        {
            $this->getResponse()
                 ->setStatusCode( 403 );

            return;
        }

        $this->fixUserForm( $form, $user->id );

        $datasheetService->form($form,$user);

        /* @var $form \Zend\Form\Form */
        $form->setHydrator( $model->getMapper() )
             ->bind( $user );

        if ( $request->isPost() )
        {
            $form->setData( $request->getPost() );

            if ( $form->isValid() && $datasheetService->save($user) )
            {
                $this->messenger()
                     ->add( 'user.form.edit.success',
                            'user', Message::LEVEL_INFO );

                return $this->redirect()
                            ->toRoute( 'Grid\User\Admin\List', array(
                                'locale' => (string) $this->locale(),
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
                 ->fromRoute( 'Grid\User\Admin\List', array(
                        'locale' => (string) $this->locale(),
                    ) )
        );

        return array(
            'form'  => $form,
            'user'  => $user,
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
        $model      = $locator->get( 'Grid\User\Model\User\Model' );
        $form       = $locator->get( 'Form' )
                              ->create( 'Grid\User\Password' );
        $user       = $model->find( $params->fromRoute( 'id' ) );

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
                            ->toRoute( 'Grid\User\Admin\List', array(
                                'locale' => (string) $this->locale(),
                            ) );
            }
            else
            {
                $this->messenger()
                     ->add( 'user.form.passwordChange.failed',
                            'user', Message::LEVEL_ERROR );
            }
        }

        return array(
            'form'  => $form,
            'user'  => $user,
        );
    }

    /**
     * Activate a user
     */
    public function activateAction()
    {
        $params     = $this->params();
        $locator    = $this->getServiceLocator();
        $model      = $locator->get( 'Grid\User\Model\User\Model' );
        $user       = $model->find( $params->fromRoute( 'id' ) );

        if ( empty( $user ) )
        {
            $this->getResponse()
                 ->setStatusCode( 404 );

            return;
        }

        if ( ! $this->getPermissionsModel()
                    ->isAllowed( $user, 'activate' ) )
        {
            $this->getResponse()
                 ->setStatusCode( 403 );

            return;
        }

        if ( $user->activate() )
        {
            $this->messenger()
                 ->add( 'user.action.activate.success',
                        'user', Message::LEVEL_INFO );
        }
        else
        {
            $this->messenger()
                 ->add( 'user.action.activate.failed',
                        'user', Message::LEVEL_ERROR );
        }

        return $this->redirect()
                    ->toRoute( 'Grid\User\Admin\List', array(
                        'locale' => (string) $this->locale(),
                    ) );
    }

    /**
     * Grant rights for a user
     */
    public function grantAction()
    {
        $params     = $this->params();
        $request    = $this->getRequest();
        $locator    = $this->getServiceLocator();
        $userModel  = $locator->get( 'Grid\User\Model\User\Model' );
        $user       = $userModel->find( $params->fromRoute( 'id' ) );
        $identity   = $locator->get('Zend\Authentication\AuthenticationService')
                              ->getIdentity();
        
        if ( empty( $user ) )
        {
            $this->getResponse()
                 ->setStatusCode( 404 );

            return;
        }

        if( !$this->getPermissionsModel()->isAllowed( $user, 'grant' ) 
            || 
            ( $identity->groupId > 2 && $user->groupId < 3 )
          )
        {
            $this->getResponse()
                 ->setStatusCode( 403 );

            return;
        }

        $filterRights = $identity->groupId>=3
                        ?  array(new \Zend\Db\Sql\Predicate\Operator(
                                'group', 
                                \Zend\Db\Sql\Predicate\Operator::OPERATOR_NOT_EQUAL_TO,
                                'user.group',
                                \Zend\Db\Sql\Predicate\Expression::TYPE_IDENTIFIER,
                                \Zend\Db\Sql\Predicate\Expression::TYPE_VALUE
                              ))
                        : array();
        $rightModel = $locator->get( 'Grid\User\Model\User\Right\Model' );
        $rights     = $rightModel->findAllByUser( $user->id, $filterRights );

        if ( $request->isPost() )
        {
            $data = $request->getPost();

            if ( isset( $data['save'] ) && ! empty( $data['rights'] ) )
            {
                foreach ( $data['rights'] as $rightId => $grant )
                {
                    $rightModel->grantToUser( $rightId, $user->id, $grant );
                }

                $this->messenger()
                     ->add( 'user.form.grant.success',
                            'user', Message::LEVEL_INFO );

                return $this->redirect()
                            ->toRoute( 'Grid\User\Admin\List', array(
                                'locale' => (string) $this->locale(),
                            ) );
            }
        }

        return array(
            'user'      => $user,
            'rights'    => $rights,
            'inherited' => $rightModel->findAllByGroup( $user->groupId ),
        );
    }

    /**
     * Ban a user
     */
    public function banAction()
    {
        $params     = $this->params();
        $locator    = $this->getServiceLocator();
        $model      = $locator->get( 'Grid\User\Model\User\Model' );
        $user       = $model->find( $params->fromRoute( 'id' ) );
        $identity   = $locator->get('Zend\Authentication\AuthenticationService')
                              ->getIdentity();
                
        if ( empty( $user ) )
        {
            $this->getResponse()
                 ->setStatusCode( 404 );

            return;
        }

        if( !$this->getPermissionsModel()->isAllowed( $user, 'ban' ) 
            || 
            ( $identity->groupId > 2 && $user->groupId < 3 )
          )
        {
            $this->getResponse()
                 ->setStatusCode( 403 );

            return;
        }

        if ( $user->ban() )
        {
            $this->messenger()
                 ->add( 'user.action.ban.success',
                        'user', Message::LEVEL_INFO );
        }
        else
        {
            $this->messenger()
                 ->add( 'user.action.ban.failed',
                        'user', Message::LEVEL_ERROR );
        }

        return $this->redirect()
                    ->toRoute( 'Grid\User\Admin\List', array(
                        'locale' => (string) $this->locale(),
                    ) );
    }

    /**
     * Delete a user
     */
    public function deleteAction()
    {
        $params          = $this->params();
        $locator         = $this->getServiceLocator();
        $model           = $locator->get( 'Grid\User\Model\User\Model' );
        $user            = $model->find( $params->fromRoute( 'id' ) );
        $datasheetService = $locator->get( 'Grid\User\Datasheet\Service' );
        $identity         = $locator->get('Zend\Authentication\AuthenticationService')
                                    ->getIdentity();
                
        if ( empty( $user ) )
        {
            $this->getResponse()
                 ->setStatusCode( 404 );

            return;
        }

        if( !$this->getPermissionsModel()->isAllowed( $user, 'delete' ) 
            || 
            ( $identity->groupId > 2 && $user->groupId < 3 )
        )
        {
            $this->getResponse()
                 ->setStatusCode( 403 );

            return;
        }

        if( $datasheetService->delete($user) )
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
                    ->toRoute( 'Grid\User\Admin\List', array(
                        'locale' => (string) $this->locale(),
                    ) );
    }

}
