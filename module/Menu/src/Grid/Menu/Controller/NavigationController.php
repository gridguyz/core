<?php

namespace Grid\Menu\Controller;

use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;

/**
 * NavigationController
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class NavigationController extends AbstractActionController
{

    /**
     * Render a standalone menu
     */
    public function renderAction()
    {
        $params     = $this->params();
        $navigation = $this->getServiceLocator()
                           ->get( 'Grid\Menu\Model\Menu\Model' )
                           ->findNavigation( $params->fromRoute( 'id' ) );

        $view = new ViewModel( array(
            'navigation' => $navigation,
            'class'      => $params->fromRoute( 'class' ),
        ) );

        return $view->setTerminal( true );
    }

}
