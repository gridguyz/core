<?php

namespace Grid\Core\Controller;

use Zend\View\Model\ViewModel;
use Zork\Mvc\Controller\AbstractAdminController;

/**
 * AbstractListController
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
abstract class AbstractListController extends AbstractAdminController
{

    /**
     * Get paginator list
     *
     * @return \Zend\Paginator\Paginator
     */
    abstract protected function getPaginator();

    /**
     * Index displays to list, by default
     */
    public function indexAction()
    {
        return $this->forward()
                    ->dispatch(
                        $this->params()
                             ->fromRoute( 'controller', get_called_class() ),
                        array(
                            'action' => 'list',
                            'locale' => (string) $this->locale(),
                        )
                    );
    }

    /**
     * List action
     *
     * page can come from:
     *  - route
     *  - post
     *  - get
     */
    public function listAction()
    {
        $params     = $this->params();
        $request    = $this->getRequest();
        $bodyOnly   = $request->isXmlHttpRequest();
        $page       = $params->fromRoute( 'page',
            $request->getPost( 'page',
                $request->getQuery( 'page', 1 )
            )
        );

        $view = new ViewModel( array(
            'paginator' => $this->getPaginator(),
            'page'      => ( (int) $page ) ?: 1,
            'format'    => $bodyOnly,
        ) );

        if ( $bodyOnly )
        {
            $view->setTerminal( true );
        }

        return $view;
    }

}
