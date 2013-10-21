<?php

namespace Grid\User\Controller;

use Zork\Stdlib\Message;
use Zend\Mvc\Controller\AbstractActionController;
use Grid\User\Model\User\Structure as UserStructure;
use Grid\Paragraph\View\Model\MetaContent;

/**
 * ManageController
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class PasswordChangeRequestController extends AbstractActionController
{

    /**
     * Create a password-request
     */
    public function createAction()
    {
        $success = null;
        $this->paragraphLayout();

        /* @var $form \Zend\Form\Form */
        $request    = $this->getRequest();
        $data       = $request->getPost();
        $service    = $this->getServiceLocator();
        $model      = $service->get( 'Grid\User\Model\User\Model' );
        $form       = $service->get( 'Form' )
                              ->get( 'Grid\User\PasswordChangeRequest\Create' );

        if ( $request->isPost() )
        {
            $form->setData( $data );

            if ( $form->isValid() )
            {
                $data = $form->getData( 'email' );
                $user = $model->findByEmail( $data['email'] );

                if ( ! empty( $user ) &&
                     $user->state != UserStructure::STATE_BANNED )
                {
                    $change = $this->url()
                                   ->fromRoute( 'Grid\User\PasswordChangeRequest\Resolve', array(
                                       'locale' => (string) $this->locale(),
                                       'hash'   => $this->getServiceLocator()
                                                        ->get( 'Grid\User\Model\ConfirmHash' )
                                                        ->create( $user->email ),
                                   ) );

                    $this->getServiceLocator()
                         ->get( 'Grid\Mail\Model\Template\Sender' )
                         ->prepare( array(
                             'template' => 'user.forgotten-password',
                             'locale'   => $user->locale,
                         ) )
                         ->send( array(
                             'email'        => $user->email,
                             'display_name' => $user->displayName,
                             'change_url'   => $change,
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

        /* Says success even if email does not exists */
        if ( $success === true || $success === false )
        {
            $this->messenger()
                 ->add( 'user.form.passwordRequest.success',
                        'user', Message::LEVEL_INFO );
        }

        return new MetaContent( 'user.passwordChangeRequest', array(
            'form' => $form,
        ) );
    }

    /**
     * Resolve a password-request
     */
    public function resolveAction()
    {
        $success    = null;
        $failed     = null;
        $service    = $this->getServiceLocator();
        $userModel  = $service->get( 'Grid\User\Model\User\Model' );
        $confirm    = $service->get( 'Grid\User\Model\ConfirmHash' );
        $hash       = $this->params()
                           ->fromRoute( 'hash' );

        if ( $confirm->has( $hash ) &&
             ( $email = $confirm->find( $hash ) ) )
        {
            $user = $userModel->findByEmail( $email );

            if ( ! empty( $user ) &&
                 $user->state != UserStructure::STATE_BANNED )
            {
                $request    = $this->getRequest();
                $data       = $request->getPost();
                $form       = $service->get( 'Form' )
                                      ->get( 'Grid\User\PasswordChangeRequest\Resolve' );

                if ( $request->isPost() )
                {
                    $form->setData( $data );

                    if ( $form->isValid() )
                    {
                        $data = $form->getData();
                        $user->state        = UserStructure::STATE_ACTIVE;
                        $user->confirmed    = true;
                        $user->password     = $data['password'];

                        if ( $user->save() )
                        {
                            $confirm->delete( $hash );
                            $success = true;
                        }
                    }
                    else
                    {
                        $success = false;
                    }
                }
            }
            else
            {
                $failed = true;
            }
        }
        else
        {
            $failed = true;
        }

        if ( $failed === true )
        {
            $this->messenger()
                 ->add( 'user.form.passwordChange.failed',
                        'user', Message::LEVEL_ERROR );

            return $this->redirect()
                        ->toRoute( 'Grid\User\PasswordChangeRequest\Create', array(
                            'locale'    => (string) $this->locale(),
                            'returnUri' => '/',
                        ) );
        }

        if ( $success === true )
        {
            $this->messenger()
                 ->add( 'user.form.passwordChange.success',
                        'user', Message::LEVEL_INFO );

            return $this->redirect()
                        ->toRoute( 'Grid\User\Authentication\Login', array(
                            'locale'    => (string) $this->locale(),
                            'returnUri' => '/',
                        ) );
        }

        if ( $success === false )
        {
            $this->messenger()
                 ->add( 'user.form.passwordChange.resolve.failed',
                        'user', Message::LEVEL_ERROR );
        }

        $this->paragraphLayout();

        return new MetaContent( 'user.passwordChangeRequest', array(
            'success'   => $success,
            'form'      => $form,
        ) );
    }

}
