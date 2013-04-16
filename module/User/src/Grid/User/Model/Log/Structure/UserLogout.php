<?php

namespace Grid\User\Model\Log\Structure;

use Zend\View\Renderer\RendererInterface;
use Grid\ApplicationLog\Model\Log\Structure\ProxyAbstract;

/**
 * UserLogout
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class UserLogout extends ProxyAbstract
{

    /**
     * Log event-type
     *
     * @var string
     */
    protected static $eventType = 'user-logout';

    /**
     * Get description for this log-event
     *
     * @return string
     */
    public function getDescription()
    {
        return '';
    }

    /**
     * Render extra data for this log-event
     *
     * @return string
     */
    public function render( RendererInterface $renderer )
    {
        return '';
    }

}
