<?php

namespace Grid\Core\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Authentication\AuthenticationService;
use Zork\Authentication\AuthenticationServiceAwareTrait;

/**
 * Grid\Core\View\Helper\AppService
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Authentication extends AbstractHelper
{

    use AuthenticationServiceAwareTrait;

    /**
     * Constructor
     *
     * @param   AuthenticationService   $authenticationService
     */
    public function __construct( AuthenticationService $authenticationService )
    {
        $this->setAuthenticationService( $authenticationService );
    }

    /**
     * Invokable helper
     *
     * @return  AuthenticationService
     */
    public function __invoke()
    {
        return $this->getAuthenticationService();
    }

}
