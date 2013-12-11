<?php

return array(
    'modules' => array(
        'Grid\Core' => array(
            'modifyPackages'    => true,
            'enabledPackages'   => array(
                'system' => array(
                    'gridguyz/core'                     => 'gridguyz/core',
                    'gridguyz/multisite'                => 'gridguyz/multisite',
                ),
                'function' => array(
                    'gridguyz/applicationlog'           => 'gridguyz/applicationlog',
                    'gridguyz/banner'                   => 'gridguyz/banner',
                    'gridguyz/contentlist'              => 'gridguyz/contentlist',
                    'gridguyz/openid'                   => 'gridguyz/openid',
                    'gridguyz/facebookcomment'          => 'gridguyz/facebookcomment',
                    'gridguyz/facebooklogin'            => 'gridguyz/facebooklogin',
                    'gridguyz/googleanalytics'          => 'gridguyz/googleanalytics',
                    'gridguyz/googlesiteverification'   => 'gridguyz/googlesiteverification',
                    'gridguyz/embed'                    => 'gridguyz/embed',
                    'gridguyz/share'                    => 'gridguyz/share',
                    'gridguyz/search'                   => 'gridguyz/search',
                    'gridguyz/tagcloud'                 => 'gridguyz/tagcloud',
                ),
                'application'   => array(
                    // reserved for 3rd party packages
                ),
            ),
        ),
    ),
);
