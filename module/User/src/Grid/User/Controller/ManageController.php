<?php

namespace Grid\User\Controller;

use Zork\Stdlib\Message;
use Zend\Authentication\AuthenticationService;
use Zend\Mvc\Controller\AbstractActionController;
use Zork\Session\ContainerAwareTrait as SessionContainerAwareTrait;

/**
 * ManageController
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class ManageController extends AbstractActionController
{

    use SessionContainerAwareTrait;

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
        $service = $this->getServiceLocator()
                        ->get( 'Grid\User\Datasheet\Service' );
        $form    = $this->getServiceLocator()
                        ->get( 'Form' )
                        ->create( 'Grid\User\Register' );

        if ( $request->isPost() )
        {
            $form->setData( $data );

            if ( $form->isValid() )
            {
                $user = $service->register( $form->getData() );

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
                 ->add( 'user.form.register.success',
                        'user', Message::LEVEL_INFO );
        }

        if ( $success === false )
        {
            $this->messenger()
                 ->add( 'user.form.register.failed',
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
        $auth = new AuthenticationService;

        if ( $auth->hasIdentity() )
        {
            return $this->redirect()
                        ->toRoute( 'Grid\User\Authentication\Logout', array(
                            'locale' => (string) $this->locale(),
                        ) );
        }

        $result = $this->getServiceLocator()
                       ->get( 'Grid\User\Authentication\Service' )
                       ->login( array( 'hash' => $this->params()
                                                      ->fromRoute( 'hash' ) ),
                                $this->getSessionManager(),
                                $auth );

        /* @var $logger \Zork\Log\LoggerManager */
        $logger = $this->getServiceLocator()
                       ->get( 'Zork\Log\LoggerManager' );

        if ( $result->isValid() )
        {
            $this->messenger()
                 ->add( 'user.action.confirm.success',
                        'user', Message::LEVEL_INFO );

            if ( $logger->hasLogger( 'application' ) )
            {
                $logger->getLogger( 'application' )
                       ->notice( 'user-login', array(
                           'successful' => true,
                       ) );
            }
        }
        else
        {
            $this->messenger()
                 ->add( 'user.action.confirm.failed',
                        'user', Message::LEVEL_ERROR );

            if ( $logger->hasLogger( 'application' ) )
            {
                $logger->getLogger( 'application' )
                       ->warn( 'user-login', array(
                           'successful' => false,
                       ) );
            }
        }

        $messages  = $result->getMessages();
        $returnUri = empty( $messages['returnUri'] )
                    ? '/' : $messages['returnUri'];

        foreach ( $messages as $index => $message )
        {
            if ( is_int( $index ) && is_string( $message ) )
            {
                $this->messenger()
                     ->add( $message, false, Message::LEVEL_WARN );
            }
        }

        return $this->redirect()
                    ->toUrl( $returnUri );
    }

}
