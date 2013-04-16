<?php

namespace Grid\Paragraph\Controller;

use Zork\Stdlib\Message;
use Zend\View\Model\ViewModel;
use Zork\Mvc\Controller\AbstractAdminController;

/**
 * ImportContentController
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class ImportContentController extends AbstractAdminController
{

    /**
     * @var array
     */
    protected $disableLayoutActions = array(
        'import'    => true,
    );

    /**
     * @var array
     */
    protected $aclRights = array(
        '' => array(
            'paragraph.content' => 'create'
        ),
    );

    /**
     * Import content & redirect to it
     */
    public function importAction()
    {
        $success = null;
        $request = $this->getRequest();
        $data    = $request->getPost();
        $form    = $this->getServiceLocator()
                        ->get( 'Form' )
                        ->get( 'Grid\Paragraph\ImportContent\Import' );

        $form->setAttribute(
            'action',
            $this->url()
                 ->fromRoute( 'Grid\Paragraph\ImportContent\Import', array(
                     'locale'       => (string) $this->locale(),
                     'redirectUri'  => $request->getQuery( 'returnUri' ),
                 ) )
        );

        if ( $request->isPost() )
        {
            $form->setData( $data );

            if ( $form->isValid() )
            {
                $data   = $form->getData();
                $model  = $this->getServiceLocator()
                               ->get( 'Grid\Paragraph\Model\Paragraph\Model' );
                $id     = $model->cloneFrom( $data['importId'], '_central' );
                unset( $data['importId'] );

                if ( empty( $id ) )
                {
                    $success = false;
                }
                else
                {
                    $content = $model->find( $id );

                    if ( empty( $content ) )
                    {
                        $success = false;
                    }
                    else
                    {
                        $userId = $this->getAuthenticationService()
                                       ->getIdentity()
                                       ->id;

                        $data['created']    = null;
                        $data['userId']     = $userId;
                        $data['editUsers']  = array( $userId );

                        $success = (bool) $content->setOptions( $data )
                                                  ->save();

                        if ( $success )
                        {
                            if ( isset( $data['title'] ) )
                            {
                                $data['title'] = trim( $data['title'] );
                            }

                            if ( ! empty( $data['title'] ) )
                            {
                                $subdomain = $this->getServiceLocator()
                                                  ->get( 'SiteInfo' )
                                                  ->getSubdomainId();

                                $postfix = '';
                                $uri = preg_replace(
                                    '/\\s+/', '-',
                                    mb_strtolower( $data['title'], 'UTF-8' )
                                );

                                $model = $this->getServiceLocator()
                                              ->get( 'Grid\Core\Model\Uri\Model' );

                                while ( $model->isSubdomainUriExists(
                                            $subdomain,
                                            $uri . $postfix
                                        ) )
                                {
                                    --$postfix;
                                }

                                $uri = $model->create( array(
                                    'subdomainId'   => $subdomain,
                                    'contentId'     => $content->id,
                                    'locale'        => $this->getAdminLocale(),
                                    'uri'           => $uri . $postfix,
                                ) );

                                if ( $uri->save() )
                                {
                                    $redirect = '/' . $uri->safeUri;
                                }
                            }
                        }
                    }
                }
            }
            else
            {
                $success = false;
            }
        }

        if ( true === $success )
        {
            $this->messenger()
                 ->add( 'paragraph.action.importContent.success',
                        'paragraph', Message::LEVEL_INFO );
        }

        if ( false === $success )
        {
            $this->messenger()
                 ->add( 'paragraph.action.importContent.failed',
                        'paragraph', Message::LEVEL_ERROR );
        }

        if ( null !== $success )
        {
            if ( empty( $redirect ) )
            {
                if ( empty( $id ) )
                {
                    $redirect = $request->getQuery( 'returnUri', '/' );
                }
                else
                {
                    $redirect = $this->url()
                                     ->fromRoute( 'Grid\Paragraph\Render\Paragraph', array(
                                         'locale'       => $this->getAdminLocale(),
                                         'paragraphId'  => $id,
                                     ) );
                }
            }

            return $this->redirect()
                        ->toUrl( $redirect );
        }

        $view = new ViewModel( array(
            'form' => $form,
        ) );
        return $view->setTerminal( true );
    }

}
