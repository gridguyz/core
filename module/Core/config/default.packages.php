<?php

return array(
    'modules' => array(
        'Grid\Core' => array(
            'enabledPackages'   => array(
                'system'        => array(
                    'gridguyz'  => 'gridguyz/(core|multisite)',
                ),
                'function'      => array(
                    'gridguyz'  => 'gridguyz/(?!core|multisite).*',
                ),
                'application'   => array(
                    // reserved for 3rd party packages
                ),
            ),
        ),
    ),
);
