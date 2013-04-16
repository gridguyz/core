<?php

namespace Grid\User\View\Helper;

use Locale;
use Zend\Authentication\AuthenticationService;
use Zend\View\Helper\AbstractHelper;

/**
 * UserLocale
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class UserLocale extends AbstractHelper
{

    /**
     * Get user-locale
     *
     * @return string
     */
    public function getLocale()
    {
        $auth = new AuthenticationService();

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
