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
            $this->getResponse()
                 ->setStatusCode( 404 );

            return;
        }

        return $this->redirect()
                    ->toUrl( $options['headLink']['favicon']['href'] );
    }

}
