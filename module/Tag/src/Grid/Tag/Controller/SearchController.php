<?php

namespace Grid\Tag\Controller;

use Zend\View\Model\JsonModel;
use Zend\Mvc\Controller\AbstractActionController;

/**
 * Tag-seacrh controller
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class SearchController extends AbstractActionController
{

    /**
     * @return \Zend\View\Model\JsonModel
     */
    public function searchAction()
    {
        return new JsonModel(
            $this->getServiceLocator()
                 ->get( 'Grid\Tag\Model\Tag\Model' )
                 ->findOptions(
                     $this->params()
                          ->fromQuery( 'term' )
                 )
        );
    }

}
