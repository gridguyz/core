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
     * List packages
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

    /**
     * View a package
     */
    public function viewAction()
    {
        $params  = $this->params();
        $vendor  = $params->fromRoute( 'vendor' );
        $subname = $params->fromRoute( 'subname' );
        $name    = $vendor . '/' . $subname;
        $package = $this->getServiceLocator()
                        ->get( 'Grid\Core\Model\Package\Model' )
                        ->find( $name );

        if ( empty( $package ) )
        {
            $this->getResponse()
                 ->setStatusCode( 404 );

            return;
        }

        return array(
            'name'      => $name,
            'package'   => $package,
        );
    }

}
