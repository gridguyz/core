<?php

namespace Grid\Core\Controller;

use Zend\Mvc\Exception;
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
            throw new Exception\RuntimeException(
                'Favicon not found', 404
            );
        }

        return $this->redirect()
                    ->toUrl( $options['headLink']['favicon']['href'] );
    }

}
