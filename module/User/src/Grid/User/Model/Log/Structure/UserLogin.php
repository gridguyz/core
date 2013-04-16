<?php

namespace Grid\User\Model\Log\Structure;

use Zend\View\Renderer\RendererInterface;
use Grid\ApplicationLog\Model\Log\Structure\ProxyAbstract;

/**
 * UserLogin
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class UserLogin extends ProxyAbstract
{

    /**
     * Log event-type
     *
     * @var string
     */
    protected static $eventType = 'user-login';

    /**
     * Successful
     *
     * @var bool
     */
    protected $successful;

    /**
     * Login-with
     *
     * @var string|null
     */
    protected $loginWith;

    /**
     * Get description for this log-event
     *
     * @return string
     */
    public function getDescription()
    {
        return ( $this->successful ? 'success' : 'failed' ) . (
            empty( $this->loginWith ) ? '' : ', ' . $this->loginWith
        );
    }

    /**
     * Render extra data for this log-event
     *
     * @return string
     */
    public function render( RendererInterface $renderer )
    {
        return $renderer->translate(
            'default.' . ( $this->successful ? 'success' : 'failed' ),
            'default'
        ) . ( empty( $this->loginWith ) ? '' : ', ' . sprintf(
            $renderer->translate( 'user.action.loginWith.%s', 'user' ),
            $renderer->translate( 'user.loginWith.' . $this->loginWith, 'user' )
        ) );
    }

}
