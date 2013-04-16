<?php

namespace Grid\User\Controller;

use Zork\Stdlib\Message;
use Zend\Authentication\AuthenticationService;
use Zend\Mvc\Controller\AbstractActionController;

/**
 * ManageController
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class ManageController extends AbstractActionController
{

    /**
     * Registration
     */
    public function registerAction()
    {
        $auth = new AuthenticationService();

        if ( $auth->hasIdentity() )
        {
            return $this->redirect()
                        ->toRoute( 'Grid\User\Authentication\Logout', array(
                            'locale' => (string) $this->locale()
                        ) );
        }

        $success    = null;
        $config     = $this->getServiceLocator()
                           ->get( 'Config'  )
                                [ 'modules' ]
                                [ 'Grid\User'    ];

        $this->paragraphLayout();

        if ( empty( $config['features']['registrationEnabled'] ) )
        {
            $this->getResponse()
                 ->setStatusCode( 403 );

            return;
        }

        /* @var $form \Zend\Form\Form */
        $request = $this->getRequest();
        $data    = $request->getPost();
        $model   = $this->getServiceLocator()
                        ->get( 'Grid\User\Model\User\Model' );
        $form    = $this->getServiceLocator()
                        ->get( 'Form' )
                        ->create( 'Grid\User\Register' );

        if ( $request->isPost() )
        {
            $form->setData( $data );

            if ( $form->isValid() )
            {
                $user = $model->register( $form->getData() );

                if ( ! empty( $user ) )
                {
                    $form->setData( array() );

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
                             'template' => 'user.register',
                             'locale'   => $user->locale,
                         ) )
                         ->send( array(
                             'email'        => $user->email,
                             'display_name' => $user->displayName,
                             'confirm_url'  => $confirm,
                         ), array(
                             $user->email   => $user->displayName,
                         ) );

                    $success = true;
                }
                else
                {
                    $success = false;
                }
            }
            else
            {
                $success = false;
            }
        }

        if ( $success === true )
        {
            $this->messenger()
                 ->add( 'user.action.register.success',
                        'user', Message::LEVEL_INFO );
        }

        if ( $success === false )
        {
            $this->messenger()
                 ->add( 'user.action.register.failed',
                        'user', Message::LEVEL_ERROR );
        }

        return array(
            'success'   => $success,
            'form'      => $form,
        );
    }

    /**
     * Confirmation
     */
    public function confirmAction()
    {
        $service    = $this->getServiceLocator();
        $userModel  = $service->get( 'Grid\User\Model\User\Model' );
        $confirm    = $service->get( 'Grid\User\Model\ConfirmHash' );
        $hash       = $this->params()
                           ->fromRoute( 'hash' );

        if ( $confirm->has( $hash ) &&
             ( $email = $confirm->find( $hash ) ) )
        {
            $user = $userModel->findByEmail( $email );

            if ( ! empty( $user ) )
            {
                $user->confirmed = true;

                if ( $user->save() )
                {
                    $confirm->delete( $hash );

                    $this->messenger()
                         ->add( 'user.action.confirm.success',
                                'user', Message::LEVEL_INFO );
                }
            }
        }

        return $this->redirect()
                    ->toRoute( 'Grid\User\Authentication\Login', array(
                        'locale'    => (string) $this->locale(),
                        'returnUri' => '/',
                    ) );
    }

}
