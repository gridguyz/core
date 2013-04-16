<?php

namespace Grid\User\Authentication;

use Traversable;
use Zend\Stdlib\ArrayUtils;
use Zend\Authentication\Result;
use Zend\Session\ManagerInterface;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;
use Zork\EventManager\EventProviderAbstract;
use Zend\Authentication\AuthenticationService;
use Grid\User\Model\Authentication\AdapterFactory;

/**
 * Manager
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Service extends EventProviderAbstract
{

    /**
     * @var string
     */
    protected $eventIdentifier  = 'Zend\Authentication\AuthenticationService';

    /**
     * @var \User\Model\Authentication\AdapterFactory
     */
    protected $authenticationAdapterFactory;

    /**
     * @return \User\Model\Authentication\AdapterFactory
     */
    public function getAuthenticationAdapterFactory()
    {
        return $this->authenticationAdapterFactory;
    }

    /**
     * @param \User\Model\Authentication\AdapterFactory $authenticationAdapterFactory
     * @return \Zend\Authentication\AuthenticationService
     */
    public function setAuthenticationAdapterFactory( AdapterFactory $authenticationAdapterFactory )
    {
        $this->authenticationAdapterFactory = $authenticationAdapterFactory;
        return $this;
    }

    /**
     * Set the event manager instance used by this context
     *
     * @param  EventManagerInterface $events
     * @return \User\Authentication\Service
     */
    public function setEventManager( EventManagerInterface $events )
    {
        $events->setEventClass( __NAMESPACE__ . '\Event' );
        $events->attach( Event::EVENT_LOGIN,  array( $this, 'onLogin' ),  1000 );
        $events->attach( Event::EVENT_LOGOUT, array( $this, 'onLogout' ), 0    );
        return parent::setEventManager( $events );
    }

    /**
     * @param \User\Model\Authentication\AdapterFactory $authenticationAdapterFactory
     * @param \Zend\EventManager\EventManager $eventManager
     */
    public function __construct( AdapterFactory $authenticationAdapterFactory,
                                 EventManager $eventManager = null )
    {
        $this->setAuthenticationAdapterFactory( $authenticationAdapterFactory );

        if ( null !== $eventManager )
        {
             $this->setEventManager( $eventManager );
        }
    }

    /**
     * Default action on login
     *
     * @param \User\Authentication\Event $event
     * @return \Zend\Authentication\Result
     */
    public function onLogin( Event $event )
    {
        $adapter = $this->getAuthenticationAdapterFactory()
                        ->factory( $event->getParams() );
        $result  = $event->getAuthenticationService()
                         ->authenticate( $adapter );
        $event->setResult( $result );
        return $result;
    }

    /**
     * Login attempt
     *
     * @param array|\Traversable $params
     * @param \Zend\Session\ManagerInterface $sessionManager
     * @param \Zend\Authentication\AuthenticationService $auth
     * @return \Zend\Authentication\Result
     */
    public function login( $params,
                           ManagerInterface $sessionManager,
                           AuthenticationService $auth = null )
    {
        if ( null === $auth )
        {
            $auth = new AuthenticationService;
        }

        if ( $params instanceof Traversable )
        {
            $params = ArrayUtils::iteratorToArray( $params );
        }

        $event = new Event( Event::EVENT_LOGIN, $auth, $params );
        $event->setAuthenticationService( $auth )
              ->setSessionManager( $sessionManager );

        $this->getEventManager()
             ->trigger( $event );

        $result = $event->getResult();

        return new Result(
            $result->getCode(),
            $result->getIdentity(),
            ArrayUtils::merge( $result->getMessages(), array(
                Event::PARAM_RETURN_URI => $event->getReturnUri(),
            ) )
        );
    }

    /**
     * Default action on logout
     *
     * @param \User\Authentication\Event $event
     * @return void
     */
    public function onLogout( Event $event )
    {
        $event->getAuthenticationService()
              ->clearIdentity();
        $event->getSessionManager()
              ->regenerateId();
    }

    /**
     * Logout attempt
     *
     * @param array|\Traversable $params
     * @param \Zend\Session\ManagerInterface $sessionManager
     * @param \Zend\Authentication\AuthenticationService $auth
     * @return array
     */
    public function logout( $params,
                            ManagerInterface $sessionManager,
                            AuthenticationService $auth = null )
    {
        if ( null === $auth )
        {
            $auth = new AuthenticationService;
        }

        if ( $params instanceof Traversable )
        {
            $params = ArrayUtils::iteratorToArray( $params );
        }

        $event = new Event( Event::EVENT_LOGOUT, $auth, $params );
        $event->setAuthenticationService( $auth )
              ->setSessionManager( $sessionManager );

        $this->getEventManager()
             ->trigger( $event );

        return $event->getParams();
    }

}
