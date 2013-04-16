<?php

namespace Grid\User\Authentication;

use Zend\Authentication\Result;
use Zend\Authentication\AuthenticationService;
use Zend\Session\ManagerInterface;
use Zend\EventManager\Event as ZendEvent;

/**
 * Event
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 * @method \User\Authentication\Service getTarget()
 */
class Event extends ZendEvent
{

    /**
     * @const string
     */
    const EVENT_LOGIN           = 'login';

    /**
     * @const string
     */
    const EVENT_LOGOUT          = 'logout';

    /**
     * @const string
     */
    const PARAM_RETURN_URI      = 'returnUri';

    /**
     * @var \Zend\Authentication\Result
     */
    protected $result;

    /**
     * @var \Zend\Session\ManagerInterface
     */
    protected $sessionManager;

    /**
     * @var \Zend\Authentication\AuthenticationService
     */
    protected $authenticationService;

    /**
     * @return \Zend\Authentication\Result
     */
    public function getResult()
    {
        if ( null === $this->result )
        {
            $this->result = new Result( Result::FAILURE_UNCATEGORIZED, null );
        }

        return $this->result;
    }

    /**
     * @param \Zend\Authentication\Result $result
     * @return \User\Authentication\Event
     */
    public function setResult( Result $result )
    {
        $this->result = $result;
        return $this;
    }

    /**
     * @return \Zend\Authentication\AuthenticationService
     */
    public function getAuthenticationService()
    {
        if ( null === $this->authenticationService )
        {
            $this->authenticationService = new AuthenticationService;
        }

        return $this->authenticationService;
    }

    /**
     * @param \Zend\Authentication\AuthenticationService $service
     * @return \User\Authentication\Event
     */
    public function setAuthenticationService( AuthenticationService $service )
    {
        $this->authenticationService = $service;
        return $this;
    }

    /**
     * @return \User\Model\User\Structure
     */
    public function getIdentity()
    {
        return isset( $this->result )
            ? $this->getResult()->getIdentity()
            : $this->getAuthenticationService()->getIdentity();
    }

    /**
     * @return \Zend\Session\ManagerInterface
     */
    public function getSessionManager()
    {
        return $this->sessionManager;
    }

    /**
     * @param \Zend\Session\ManagerInterface $sessionManager
     * @return \User\Authentication\Event
     */
    public function setSessionManager( ManagerInterface $sessionManager )
    {
        $this->sessionManager = $sessionManager;
        return $this;
    }

    /**
     * @return string
     */
    public function getReturnUri()
    {
        $return = $this->getParam( static::PARAM_RETURN_URI );

        if ( empty( $return ) )
        {
            $result = $this->getResult();
            $msgs   = $result->getMessages();

            if ( empty( $msgs[static::PARAM_RETURN_URI] ) )
            {
                $return = '/';
            }
            else
            {
                $return = (string) $msgs[static::PARAM_RETURN_URI];
            }
        }

        return $return;
    }

    /**
     * @param string\null $value
     * @return \User\Event\Authentication\Event
     */
    public function setReturnUri( $value )
    {
        return $this->setParam(
            static::PARAM_RETURN_URI,
            ( (string) $value ) ?: null
        );
    }

}
