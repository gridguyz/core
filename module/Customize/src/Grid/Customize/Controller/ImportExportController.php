<?php

namespace Grid\Customize\Controller;

use Zork\Stdlib\Message;
use Zend\View\Model\ViewModel;
use Zork\Http\PhpEnvironment\Response\Readfile;
use Zork\Mvc\Controller\AbstractAdminController;

/**
 * ImportExportController
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class ImportExportController extends AbstractAdminController
{

    /**
     * Define rights required to use this controller
     *
     * @var array
     */
    protected $aclRights = array(
        '' => array(
            'customize' => 'edit',
        ),
    );

    /**
     * Import paragraph action
     */
    public function importAction()
    {
        $params         = $this->params();
        $request        = $this->getRequest();
        $return         = (string) $request->getQuery( 'returnUri' );
        $paragraphModel = $this->getServiceLocator()
                               ->get( 'Grid\Paragraph\Model\Paragraph\Model' );
        $layout         = $paragraphModel->find( $params->fromRoute( 'layoutId' ) );

        if ( empty( $layout ) )
        {
            $this->getResponse()
                 ->setResultCode( 404 );

            return;
        }

        $form = $this->getServiceLocator()
                     ->get( 'Form' )
                     ->get( 'Grid\Customize\Import' );

        if ( $request->isPost() )
        {
            $form->setData( $request->getPost() );

            if ( $form->isValid() )
            {
                $file = preg_replace(
                    '#^/#', './',
                    $form->getValue( 'file' )
                );

                if ( $this->getServiceLocator()
                          ->get( 'Grid\Customize\Model\Importer' )
                          ->import( $file, $layout->id ) )
                {
                    $this->messenger()
                         ->add( 'customize.form.success',
                                'customize', Message::LEVEL_INFO );
                }
                else
                {
                    $this->messenger()
                         ->add( 'customize.form.failed',
                                'customize', Message::LEVEL_ERROR );
                }

                return $this->redirect()
                            ->toUrl( $return );
            }
        }

        $view = new ViewModel( array(
            'form'      => $form->setAttribute( 'action', $request->getRequestUri() ),
            'layoutId'  => $layout->id,
            'contentId' => $params->fromRoute( 'contentId' ),
            'exportUri' => preg_replace(
                '#https?://[^/]+#', '', $return
            )
        ) );

        return $view->setTerminal( true );
    }

    /**
     * Export layout action
     */
    public function exportAction()
    {
        $params         = $this->params();
        $request        = $this->getRequest();
        $paragraphModel = $this->getServiceLocator()
                               ->get( 'Grid\Paragraph\Model\Paragraph\Model' );
        $layout         = $paragraphModel->find( $params->fromRoute( 'layoutId' ) );

        if ( empty( $layout ) )
        {
            $this->getResponse()
                 ->setResultCode( 404 );

            return;
        }

        $zip = $this->getServiceLocator()
                    ->get( 'Grid\Customize\Model\Exporter' )
                    ->export( $request->getQuery( 'exportUri' ),
                              $layout->id,
                              $params->fromRoute( 'contentId' ) );

        $response = Readfile::fromFile(
            $zip,
            'application/zip',
            'layout-' . $layout->id . '.zip',
            true
        );

        $this->getEvent()
             ->setResponse( $response );

        return $response;
    }

}
