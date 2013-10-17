<?php

namespace Grid\User\View\Helper;

use Locale;
use Zend\View\Helper\AbstractHelper;
use Zend\Authentication\AuthenticationService;
use Zork\Authentication\AuthenticationServiceAwareTrait;

/**
 * UserLocale
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class UserLocale extends AbstractHelper
{

    use AuthenticationServiceAwareTrait;

    /**
     * Get user-locale
     *
     * @return string
     */
    public function getLocale()
    {
        $auth = $this->getAuthenticationService();

        if ( $auth->hasIdentity()  )
        {
            $locale = $auth->getIdentity()->locale;
        }
        else
        {
            $locale = Locale::getDefault();
        }

        return $locale;
    }

    /**
     * Get user-locale
     *
     * @return string
     */
    public function __invoke()
    {
        return $this->getLocale();
    }

}
