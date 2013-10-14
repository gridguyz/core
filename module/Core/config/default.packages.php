<?php

return array(
    'modules' => array(
        'Grid\Core' => array(
            'enabledPackages' => array(
                'system' => array(
                    'gridguyz/core'                     => 'gridguyz/core',
                    'gridguyz/multisite'                => 'gridguyz/multisite',
                ),
                'function' => array(
                    'gridguyz/openid'                   => 'gridguyz/openid',
                    'gridguyz/facebooklogin'            => 'gridguyz/facebooklogin',
                    'gridguyz/googleanalytics'          => 'gridguyz/googleanalytics',
                    'gridguyz/googlesiteverification'   => 'gridguyz/googlesiteverification',
                    'gridguyz/embed'                    => 'gridguyz/embed',
                    'gridguyz/banner'                   => 'gridguyz/banner',
                    'gridguyz/applicationlog'           => 'gridguyz/applicationlog',
                    'gridguyz/share'                    => 'gridguyz/share',
                    'gridguyz/search'                   => 'gridguyz/search',
                ),
                'application'   => array(
                    // reserved for 3rd party packages
                ),
            ),
        ),
    ),
);
