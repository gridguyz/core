<?php

namespace Grid\User\Controller;

use Zork\Stdlib\Message;
use Zend\Authentication\Result;
use Zend\Authentication\AuthenticationService;
use Zend\Mvc\Controller\AbstractActionController;
use Zork\Session\ContainerAwareTrait as SessionContainerAwareTrait;

/**
 * Authentication
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class AuthenticationController extends AbstractActionController
{

    use SessionContainerAwareTrait;

    /**
     * @var string
     */
    const DEFAULT_RETURN_URI = '/';

    /**
     * @var string
     */
    protected $returnUri;

    /**
     * Get return-uri
     *
     * @return string
     */
    public function getReturnUri()
    {
        if ( null === $this->returnUri )
        {
            $request    = $this->getRequest();
            $session    = $this->getSessionContainer();
            $returnUri  = $request->getQuery( 'requestUri' );

            if ( empty( $returnUri ) )
            {
                $returnUri = $request->getPost( 'requestUri' );
            }

            if ( empty( $returnUri ) )
            {
                if ( isset( $session['returnUri'] ) )
                {
                    $returnUri = $session['returnUri'];
                }
                else
                {
                    $returnUri = self::DEFAULT_RETURN_URI;
                }
            }
            else
            {
                $session['returnUri'] = $returnUri;
            }

            if ( empty( $returnUri ) )
            {
                return '/';
            }

            $this->returnUri = $returnUri;
        }

        return $this->returnUri;
    }

    /**
     * Get error message key by code
     *
     * @param   int     $code
     * @return  string
     */
    protected function getErrorMessageKey( $code )
    {
        switch ( $code )
        {
            case Result::FAILURE_IDENTITY_NOT_FOUND:
                return 'user.form.login.failed.identity-not-found';

            case Result::FAILURE_IDENTITY_AMBIGUOUS:
                return 'user.form.login.failed.identity-amiguous';

            case Result::FAILURE_CREDENTIAL_INVALID:
                return 'user.form.login.failed.credential-invalid';
        }

        return 'user.form.login.failed';
    }

    /**
     * Authentication: login
     */
    public function loginAction()
    {
        $auth = new AuthenticationService;

        if ( $auth->hasIdentity() )
        {
            return $this->redirect()
                        ->toRoute( 'Grid\User\Authentication\Logout', array(
                            'locale' => (string) $this->locale(),
                        ) );
        }

        /* @var $form \Zend\Form\Form */
        $return  = $this->getReturnUri();
        $request = $this->getRequest();
        $data    = $request->getPost();
        $form    = $this->getServiceLocator()
                        ->get( 'Form' )
                        ->create( 'Grid\User\Login', array(
                            'returnUri' => $return,
                        ) );

        $form->setData( $data );

        if ( $request->isPost() )
        {
            if ( $form->isValid() )
            {
                /* @var $logger \Zork\Log\LoggerManager */
                $data   = $form->getData();
                $logger = $this->getServiceLocator()
                               ->get( 'Zork\Log\LoggerManager' );
                $result = $this->getServiceLocator()
                               ->get( 'Grid\User\Authentication\Service' )
                               ->login( $data,
                                        $this->getSessionManager(),
                                        $auth );

                $messages = $result->getMessages();

                if ( $result->isValid() )
                {
                    $this->messenger()
                         ->add( 'user.form.login.success',
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
                    foreach ( $messages as $index => $message )
                    {
                        if ( is_int( $index ) && is_string( $message ) )
                        {
                            $this->messenger()
                                 ->add( $message, false, Message::LEVEL_WARN );
                        }
                    }

                    $this->messenger()
                         ->add( $this->getErrorMessageKey( $result->getCode() ),
                                'user', Message::LEVEL_ERROR );

                    if ( $logger->hasLogger( 'application' ) )
                    {
                        $logger->getLogger( 'application' )
                               ->warn( 'user-login', array(
                                   'successful' => false,
                               ) );
                    }
                }

                if ( empty( $messages['returnUri'] ) )
                {
                    if ( empty( $data['returnUri'] ) )
                    {
                        $returnUri = $return;
                    }
                    else
                    {
                        $returnUri = $data['returnUri'];
                    }
                }
                else
                {
                    $returnUri = $messages['returnUri'];
                }

                return $this->redirect()
                            ->toUrl( $returnUri );
            }
        }

        $this->plugin( 'layout' )
             ->setMiddleLayout( 'layout/middle/center' );

        return array(
            'form'      => $form,
            'display'   => $this->getServiceLocator()
                                ->get( 'Config' )
                                     [ 'modules' ]
                                     [ 'Grid\User' ]
                                     [ 'display' ]
        );
    }

    /**
     * Authentication: login-with (additional adapters)
     */
    public function loginWithAction()
    {
        $auth = new AuthenticationService;

        if ( $auth->hasIdentity() )
        {
            return $this->redirect()
                        ->toRoute( 'Grid\User\Authentication\Logout', array(
                            'locale' => (string) $this->locale(),
                        ) );
        }

        $request = $this->getRequest();
        $data    = $request->getQuery()
                           ->toArray();

        if ( $request->isPost() )
        {
            $data = array_merge( $data, $request->getPost()
                                                ->toArray() );
        }

        /* @var $logger \Zork\Log\LoggerManager */
        $logger = $this->getServiceLocator()
                       ->get( 'Zork\Log\LoggerManager' );
        $result = $this->getServiceLocator()
                       ->get( 'Grid\User\Authentication\Service' )
                       ->login( $data,
                                $this->getSessionManager(),
                                $auth );

        $messages   = $result->getMessages();
        $with       = empty( $messages['loginWith'] )
                    ? 'other'
                    : $messages['loginWith'];

        if ( $result->isValid() )
        {
            $this->messenger()
                 ->add( 'user.form.login.success',
                        'user', Message::LEVEL_INFO );

            if ( $logger->hasLogger( 'application' ) )
            {
                $logger->getLogger( 'application' )
                       ->notice( 'user-login', array(
                           'successful' => true,
                           'loginWith'  => $with,
                       ) );
            }
        }
        else
        {
            foreach ( $messages as $index => $message )
            {
                if ( is_int( $index ) && is_string( $message ) )
                {
                    $this->messenger()
                         ->add( $message, false, Message::LEVEL_WARN );
                }
            }

            $this->messenger()
                 ->add( $this->getErrorMessageKey( $result->getCode() ),
                        'user', Message::LEVEL_ERROR );

            if ( $logger->hasLogger( 'application' ) )
            {
                $logger->getLogger( 'application' )
                       ->warn( 'user-login', array(
                           'successful' => false,
                           'loginWith'  => $with,
                       ) );
            }
        }

        if ( ! empty( $messages['registered'] ) )
        {
            $user = $result->getIdentity();

            $this->getServiceLocator()
                 ->get( 'Grid\Mail\Model\Template\Sender' )
                 ->prepare( array(
                     'template' => 'user.login-with',
                     'locale'   => $user->locale,
                 ) )
                 ->send( array(
                     'email'        => $user->email,
                     'display_name' => $user->displayName,
                     'confirm_url'  => null,
                 ), array(
                     $user->email   => $user->displayName,
                 ) );
        }

        if ( empty( $messages['returnUri'] ) )
        {
            if ( empty( $data['returnUri'] ) )
            {
                $returnUri = $this->getReturnUri();
            }
            else
            {
                $returnUri = $data['returnUri'];
            }
        }
        else
        {
            $returnUri = $messages['returnUri'];
        }

        return $this->redirect()
                    ->toUrl( $returnUri );
    }

    /**
     * Authentication: logout
     */
    public function logoutAction()
    {
        $auth = new AuthenticationService;

        if ( ! $auth->hasIdentity() )
        {
            return $this->redirect()
                        ->toRoute( 'Grid\User\Authentication\Login', array(
                            'locale' => (string) $this->locale(),
                        ) );
        }

        /* @var $form \Zend\Form\Form */
        $return  = $this->getReturnUri();
        $request = $this->getRequest();
        $params  = $this->params();
        $data    = $request->getPost();
        $form    = $this->getServiceLocator()
                        ->get( 'Form' )
                        ->create( 'Grid\User\Logout', array(
                            'returnUri' => $return,
                        ) );

        $form->setData( $data );

        if ( $params->fromRoute( 'immediate', false ) ||
             ( $request->isPost() && $form->isValid() ) )
        {
            /* @var $logger \Zork\Log\LoggerManager */
            $logger = $this->getServiceLocator()
                           ->get( 'Zork\Log\LoggerManager' );

            if ( $logger->hasLogger( 'application' ) )
            {
                $logger->getLogger( 'application' )
                       ->notice( 'user-logout' );
            }

            $data   = $form->getData();
            $result = $this->getServiceLocator()
                           ->get( 'Grid\User\Authentication\Service' )
                           ->logout( $data,
                                     $this->getSessionManager(),
                                     $auth );

            if ( empty( $result['returnUri'] ) )
            {
                $returnUri = $return;
            }
            else
            {
                $returnUri = $result['returnUri'];
            }

            return $this->redirect()
                        ->toUrl( $returnUri );
        }

        $this->plugin( 'layout' )
             ->setMiddleLayout( 'layout/middle/center' );

        return array(
            'form'      => $form,
            'display'   => $this->getServiceLocator()
                                ->get( 'Config' )
                                     [ 'modules' ]
                                     [ 'Grid\User' ]
                                     [ 'display' ]
        );
    }

}
