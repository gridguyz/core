<?php

namespace Grid\Paragraph\Controller;

use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;
use Grid\Paragraph\Model\Paragraph\Structure\Content;
use Grid\Paragraph\Model\Paragraph\Structure\AbstractRoot;
use Grid\Paragraph\Model\Paragraph\Structure\LayoutAwareInterface;
use Grid\Paragraph\Model\Paragraph\Structure\PublishRestrictedInterface;

/**
 * RenderController
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class RenderController extends AbstractActionController
{

    /**
     * Render-content action
     */
    public function paragraphAction()
    {
        $params     = $this->params();
        $service    = $this->getServiceLocator();
        $model      = $service->get( 'Grid\Paragraph\Model\Paragraph\Model' );
        $list       = $model->findRenderList( $params->fromRoute( 'paragraphId' ) );

        if ( empty( $list ) )
        {
            $this->paragraphLayout();

            $this->getResponse()
                 ->setStatusCode( 404 );

            return;
        }

        $paragraph = reset( $list )[1];

        if ( $paragraph instanceof PublishRestrictedInterface &&
             ! $paragraph->isPublished() && ! $paragraph->isEditable() )
        {
            if ( $paragraph instanceof LayoutAwareInterface )
            {
                $this->paragraphLayout( $paragraph->getLayoutId() );
            }
            else
            {
                $this->paragraphLayout();
            }

            $this->getResponse()
                 ->setStatusCode( 404 );

            return;
        }

        if ( ! $paragraph->isAccessible() )
        {
            if ( $paragraph instanceof LayoutAwareInterface )
            {
                $this->paragraphLayout( $paragraph->getLayoutId() );
            }
            else
            {
                $this->paragraphLayout();
            }

            $this->getResponse()
                 ->setStatusCode( 403 );

            return;
        }

        $view = new ViewModel( array(
            'paragraphRenderList' => $list,
        ) );

        $service->setService( 'RenderedContent', $paragraph );

        if ( $paragraph instanceof LayoutAwareInterface )
        {
            $this->paragraphLayout( $paragraph->getLayoutId() );
        }
        else if ( $paragraph instanceof AbstractRoot )
        {
            $auth = $service->get( 'Zend\Authentication\AuthenticationService' );

            if ( $auth->hasIdentity() )
            {
                $view->setVariable(
                    'adminMenuSettings',
                    $service->get( 'Grid\User\Model\User\Settings\Model' )
                            ->find( $auth->getIdentity()->id, 'adminMenu' )
                );
            }
        }
        else
        {
            $view->setTerminal( true );
        }

        if ( $paragraph instanceof Content )
        {
            /* @var $logger \Zork\Log\LoggerManager */
            $logger = $this->getServiceLocator()
                           ->get( 'Zork\Log\LoggerManager' );

            if ( $logger->hasLogger( 'application' ) )
            {
                $logger->getLogger( 'application' )
                       ->info( 'content-view', array(
                           'paragraphId'    => $paragraph->id,
                           'locale'         => (string) $this->locale(),
                           'originalTitle'  => $paragraph->title,
                       ) );
            }
        }

        return $view;
    }

}
