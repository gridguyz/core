<?php

namespace Grid\User\Controller;

use Zork\Stdlib\Message;
use Zend\Authentication\Result;
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
     * Get valid return-uri
     *
     * @param   string  $returnUri
     * @return  string
     */
    protected function getValidReturnUri( $returnUri )
    {
        $match      = array();
        $returnUri  = ltrim( str_replace( '\\', '/', $returnUri ), "\n\r\t\v\e\f" );

        if ( ! preg_match( '#^/([^/].*)?$#', $returnUri, $match ) )
        {
            $returnUri = static::DEFAULT_RETURN_URI;
        }

        return $returnUri;
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
        $return = $this->getReturnUri();
        $auth   = $this->getServiceLocator()
                       ->get( 'Zend\Authentication\AuthenticationService' );

        if ( $auth->hasIdentity() )
        {
            return $this->redirect()
                        ->toUrl( $this->getValidReturnUri( $return ) );
        }

        /* @var $form \Zend\Form\Form */
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
                /* @var $sessm \Zend\Session\SessionManager */
                /* @var $logger \Zork\Log\LoggerManager */
                $data   = $form->getData();
                $sessm  = $this->getSessionManager();
                $logger = $this->getServiceLocator()
                               ->get( 'Zork\Log\LoggerManager' );
                $result = $this->getServiceLocator()
                               ->get( 'Grid\User\Authentication\Service' )
                               ->login( $data, $sessm, $auth );

                $messages = $result->getMessages();

                if ( $result->isValid() )
                {
                    $sessm->regenerateId( false );

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
                            ->toUrl( $this->getValidReturnUri( $returnUri ) );
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
        $return = $this->getReturnUri();
        $auth   = $this->getServiceLocator()
                       ->get( 'Zend\Authentication\AuthenticationService' );

        if ( $auth->hasIdentity() )
        {
            return $this->redirect()
                        ->toUrl( $this->getValidReturnUri( $return ) );
        }

        $request = $this->getRequest();
        $data    = $request->getQuery()
                           ->toArray();

        if ( $request->isPost() )
        {
            $data = array_merge( $data, $request->getPost()
                                                ->toArray() );
        }

        /* @var $sessm \Zend\Session\SessionManager */
        /* @var $logger \Zork\Log\LoggerManager */
        $sessm  = $this->getSessionManager();
        $logger = $this->getServiceLocator()
                       ->get( 'Zork\Log\LoggerManager' );
        $result = $this->getServiceLocator()
                       ->get( 'Grid\User\Authentication\Service' )
                       ->login( $data, $sessm, $auth );

        $messages   = $result->getMessages();
        $with       = empty( $messages['loginWith'] )
                    ? 'other'
                    : $messages['loginWith'];

        if ( $result->isValid() )
        {
            $sessm->regenerateId( false );

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
                    ->toUrl( $this->getValidReturnUri( $returnUri ) );
    }

    /**
     * Authentication: logout
     */
    public function logoutAction()
    {
        $return = $this->getReturnUri();
        $auth   = $this->getServiceLocator()
                       ->get( 'Zend\Authentication\AuthenticationService' );

        if ( ! $auth->hasIdentity() )
        {
            $returnUri = $this->getValidReturnUri( $return );

            if ( empty( $returnUri ) ||
                 static::DEFAULT_RETURN_URI == $returnUri )
            {
                return $this->redirect()
                            ->toRoute( 'Grid\User\Authentication\Login', array(
                                'locale' => (string) $this->locale(),
                            ) );
            }
            else
            {
                return $this->redirect()
                            ->toUrl( $returnUri );
            }
        }

        /* @var $form \Zend\Form\Form */
        $request    = $this->getRequest();
        $params     = $this->params();
        $immediate  = $params->fromRoute( 'immediate', false );
        $data       = $request->getPost();
        $form       = $this->getServiceLocator()
                           ->get( 'Form' )
                           ->create( 'Grid\User\Logout', array(
                               'returnUri' => $return,
                           ) );

        $form->setData( $data );

        if ( $immediate || ( $request->isPost() && $form->isValid() ) )
        {
            /* @var $logger \Zork\Log\LoggerManager */
            $logger = $this->getServiceLocator()
                           ->get( 'Zork\Log\LoggerManager' );

            if ( $logger->hasLogger( 'application' ) )
            {
                $logger->getLogger( 'application' )
                       ->notice( 'user-logout' );
            }

            if ( $immediate )
            {
                $data = $return ? array( 'returnUri' => $return ) : array();
            }
            else
            {
                $data = $form->getData();
            }

            /* @var $sessm \Zend\Session\SessionManager */
            $sessm  = $this->getSessionManager();
            $result = $this->getServiceLocator()
                           ->get( 'Grid\User\Authentication\Service' )
                           ->logout( $data, $sessm, $auth );

            $sessm->regenerateId( false );

            if ( empty( $result['returnUri'] ) )
            {
                $returnUri = $return;
            }
            else
            {
                $returnUri = $result['returnUri'];
            }

            return $this->redirect()
                        ->toUrl( $this->getValidReturnUri( $returnUri ) );
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
