<?php

namespace Grid\Core\Controller;

use Zend\Mvc\Exception;
use Zork\Mvc\Controller\AbstractAdminController;

class AdminController extends AbstractAdminController
{

    /**
     * @var \User\Model\Permissions\Model
     */
    protected $permissionModel;

    /**
     * @return \User\Model\Permissions\Model
     */
    protected function getPermissionModel()
    {
        if ( null === $this->permissionModel )
        {
            $this->permissionModel = $this->getServiceLocator()
                                          ->get( 'Grid\User\Model\Permissions\Model' );
        }

        return $this->permissionModel;
    }

    public function indexAction()
    {
        return $this->redirect()
                    ->toRoute( 'Grid\Core\Admin\Dashboard', array(
                        'locale' => (string) $this->locale(),
                    ) );
    }

    public function notAllowedAction()
    {
        return array();
    }

    /**
     * Get actions
     *
     * @param   array $spec
     * @return  array
     */
    protected function getActions( array $spec )
    {
        $permissionModel = $this->getPermissionModel();

        foreach ( $spec as $index => $description )
        {
            if ( ! empty( $description['resource'] ) )
            {
                $allow = empty( $description['privilege'] )
                    ? $permissionModel->isAllowed( $description['resource'] )
                    : $permissionModel->isAllowed( $description['resource'],
                                                   $description['privilege'] );

                if ( ! $allow )
                {
                    unset( $spec[$index] );
                }
            }
        }

        uasort( $spec, function ( $a, $b ) {
            switch ( true )
            {
                case empty( $a['order'] ) && empty( $b['order'] ):
                    return 0;

                case empty( $a['order'] ) && ! empty( $b['order'] ):
                    return 1;

                case ! empty( $a['order'] ) && empty( $b['order'] ):
                    return -1;

                case $a['order'] == $b['order']:
                    return 0;

                default:
                    return $a['order'] > $b['order'] ? 1 : -1;
            }
        } );

        return $spec;
    }

    public function dashboardAction()
    {
        $configs = $this->getServiceLocator()
                        ->get( 'Configuration' )
                             [ 'modules' ]
                             [ 'Grid\Core' ];

        $icons = $this->getActions( $configs['dashboardIcons'] );
        $boxes = $this->getActions( $configs['dashboardBoxes'] );

        $viewsOfDashboardBoxes = array();
        $plugins = $this->getPluginManager();

        foreach ( $boxes as $box )
        {
            if ( empty( $box['plugin'] ) ||
                 ! $plugins->has( $box['plugin'] ) )
            {
                throw new Exception\RuntimeException(
                    'Plugin (' . $box . ') does not exist.', 500
                );
            }

            $viewsOfDashboardBoxes[] = call_user_func_array(
                array(
                    $plugins->get(
                        $box['plugin'],
                        empty( $box['options'] )
                            ? array()
                            : (array) $box['options']
                    ),
                    empty( $box['method'] )
                        ? '__invoke'
                        : (string) $box['method']
                ),
                empty( $box['params'] )
                    ? array()
                    : (array) $box['params']
            );
        }

        return array(
            'icons'         => $icons,
            'locale'        => (string) $this->locale(),
            'boxes'         => $viewsOfDashboardBoxes,
            'adminLocale'   => $this->getAdminLocale(),
        );
    }

}
