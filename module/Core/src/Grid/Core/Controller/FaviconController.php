<?php

namespace Grid\Core\Controller;

use Zend\Mvc\Controller\AbstractActionController;

/**
 * FaviconController
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class FaviconController extends AbstractActionController
{

    /**
     * favicon.ico
     */
    public function icoAction()
    {
        $options = $this->getServiceLocator()
                        ->get( 'Config'         )
                             [ 'view_manager'   ]
                             [ 'head_defaults'  ];

        if ( empty( $options['headLink']['favicon']['href'] ) )
        {
            $redirect = '/uploads/_central/settings/favicon.ico';
        }
        else
        {
            $redirect = $options['headLink']['favicon']['href'];
        }

        return $this->redirect()
                    ->toUrl( $redirect );
    }

}
