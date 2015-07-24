<?php

namespace Grid\Paragraph\Controller;

use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;
use Grid\Paragraph\Model\Paragraph\Structure\AbstractRoot;
use Grid\Paragraph\Model\Paragraph\Structure\ContentDependentAwareInterface;
use Grid\Paragraph\Model\Paragraph\Structure\PublishRestrictedInterface;

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
        $cid        = $params->fromQuery( 'contentId' );
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

        $paragraph = $dashboard->paragraph;

        if ( ! $paragraph->isEditable() )
        {
            $this->getResponse()
                 ->setStatusCode( 403 );

            return;
        }

        if ( $cid && $paragraph instanceof ContentDependentAwareInterface )
        {
            $content = $service->get( 'Grid\Paragraph\Model\Paragraph\Model' )
                               ->setLocale( $locale )
                               ->find( $cid );

            if ( $content instanceof AbstractRoot )
            {
                $paragraph->setDependentContent( $content );
            }
        }

        $form = $dashboard->getForm( $service->get( 'Form' ), $customize );
        $view = new ViewModel( array(
            'form'      => $form,
            'success'   => null,
        ) );

        if ( $request->isPost() )
        {
            $post = $request->getPost();
            if( $cid && $paragraph instanceof PublishRestrictedInterface )
            {
                if( empty($post['paragraph-content']['accessUsers']) )
                {
                    $post->set('paragraph-content', array_merge( $post['paragraph-content'],array('accessUsers' => array()) ));
                }
                if( empty($post['paragraph-content']['accessGroups']) )
                {
                    $post->set('paragraph-content', array_merge( $post['paragraph-content'],array('accessGroups' => array()) ));
                }
            }
            //var_dump($post['paragraph-content']);die();
            //if( !isset($pos) )
            $form->setData( $post );
            $view->setVariable( 'success',
                                $form->isValid() && $dashboard->save() );
        }

        return $view->setTerminal( true );
    }

}
