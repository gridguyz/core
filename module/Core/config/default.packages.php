<?php

return array(
    'modules' => array(
        'Grid\Core' => array(
            'enabledPackages'   => array(
                'function'      => array(
                    'gridguyz'  => 'gridguyz/(?!core|multisite).*',
                ),
                'application'   => array(
                    // reserved for 3rd party packages
                ),
                'system'        => array(
                    'gridguyz'  => 'gridguyz/(core|multisite)',
                ),
            ),
            'enabledPackagesOrder' => array(
                'function'      => 0,
                'application'   => 10,
                'system'        => 999,
            ),
        ),
    ),
);
