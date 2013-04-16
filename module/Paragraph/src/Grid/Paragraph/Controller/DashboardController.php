<?php

namespace Grid\Paragraph\Controller;

use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;

/**
 * DashboardController
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class DashboardController extends AbstractActionController
{

    /**
     * Edit-paragraph action
     */
    public function editAction()
    {
        $params     = $this->params();
        $request    = $this->getRequest();
        $service    = $this->getServiceLocator();
        $pid        = $params->fromRoute( 'paragraphId' );
        $locale     = $service->get( 'AdminLocale' )->getCurrent();
        $model      = $service->get( 'Grid\Paragraph\Model\Dashboard\Model' );
        $permission = $service->get( 'Grid\User\Model\Permissions\Model' );
        $dashboard  = $model->setLocale( $locale )->find( $pid );
        $customize  = $permission->isAllowed( 'paragraph.customize', 'edit' );

        if ( empty( $dashboard ) )
        {
            $this->getResponse()
                 ->setStatusCode( 404 );

            return;
        }

        if ( ! $dashboard->paragraph
                         ->isEditable() )
        {
            $this->getResponse()
                 ->setStatusCode( 403 );

            return;
        }

        $form = $dashboard->getForm( $service->get( 'Form' ), $customize );
        $view = new ViewModel( array(
            'form'      => $form,
            'success'   => null,
        ) );

        if ( $request->isPost() )
        {
            $form->setData( $request->getPost() );
            $view->setVariable( 'success',
                                $form->isValid() && $dashboard->save() );
        }

        return $view->setTerminal( true );
    }

}
