<?php

namespace Grid\Core\Controller;

use Zork\Stdlib\Message;
use Zork\Mvc\Controller\AbstractAdminController;

class PackageController extends AbstractAdminController
{

    /**
     * @var array
     */
    protected $aclRights = array(
        '' => array(
            'sysadmin.packages' => 'edit',
        ),
    );

    /**
     * List packages action
     */
    public function listAction()
    {
        $params  = $this->params();
        $pattern = $params->fromPost( 'pattern', $params->fromQuery( 'pattern' ) );
        $order   = $params->fromPost( 'order',   $params->fromQuery( 'order'   ) );
        $page    = $params->fromPost( 'page',    $params->fromQuery( 'page', 0 ) );

        return array(
            'page'      => (int) $page,
            'paginator' => $this->getServiceLocator()
                                ->get( 'Grid\Core\Model\Package\Model' )
                                ->getPaginator( $pattern, $order )
        );
    }

}
