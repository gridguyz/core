<?php

namespace Grid\Menu\Controller;

use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;

/**
 * SitemapController
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class SitemapController extends AbstractActionController
{

    /**
     * Render a standalone sitemap for a menu
     */
    public function menuAction()
    {
        $params     = $this->params();
        $navigation = $this->getServiceLocator()
                           ->get( 'Grid\Menu\Model\Menu\Model' )
                           ->findNavigation( $params->fromRoute( 'id' ) );

        $view = new ViewModel( array(
            'navigation' => $navigation,
        ) );

        $this->getResponse()
             ->getHeaders()
             ->addHeaderLine( 'Content-Type', 'application/xml' );

        return $view->setTerminal( true );
    }

    /**
     * Render a whole sitemap
     */
    public function indexAction()
    {
        $navigation = $this->getServiceLocator()
                           ->get( 'Grid\Menu\Model\Menu\Model' )
                           ->findNavigation();

        $view = new ViewModel( array(
            'navigation' => $navigation,
        ) );

        $this->getResponse()
             ->getHeaders()
             ->addHeaderLine( 'Content-Type', 'application/xml' );

        return $view->setTerminal( true );
    }

}
