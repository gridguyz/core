<?php
/**
 * Authentication: logout
 *
 * @author Sipos Zoltán
 */
namespace Grid\User\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\View\Model\ViewModel;
use Zork\Session\ContainerAwareTrait as SessionContainerAwareTrait;

class DashboardLogoutPlugin extends AbstractPlugin
{

    use SessionContainerAwareTrait;

    public function __invoke()
    {
        $controller = $this->getController();
        $form = $this->getController()
                     ->getServiceLocator()
                     ->get( 'Form' )
                     ->create( 'Grid\User\Logout', array(
                         'returnUri' => '/'
                     ) );

        $form->setAttribute(
            'action',
            $controller->url()
                       ->fromRoute( 'Grid\User\Authentication\Logout', array(
                           'locale' => (string) $controller->locale(),
                       ) )
        );

        $view = new ViewModel( array(
            'form'      => $form,
            'display'   => array(),
        ) );

        return $view->setTemplate( 'grid/core/admin/dashboard.logout' );
    }

}
