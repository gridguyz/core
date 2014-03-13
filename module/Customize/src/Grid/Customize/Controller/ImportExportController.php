<?php

namespace Grid\Customize\Controller;

use Zork\Stdlib\Message;
use Zend\Stdlib\ArrayUtils;
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
            'customize' => 'view',
        ),
        'import' => array(
            'customize' => 'create',
            'paragraph' => 'create',
        ),
    );

    /**
     * Import paragraph action
     */
    public function importAction()
    {
        /* @var $form \Zork\Form\Form */
        $request        = $this->getRequest();
        $return         = (string) $request->getQuery( 'returnUri' );
        $serviceLocator = $this->getServiceLocator();
        $form           = $serviceLocator->get( 'Form' )
                                         ->get( 'Grid\Customize\Import' );

        if ( $request->isPost() )
        {
            $form->setData( ArrayUtils::merge(
                $request->getPost()
                        ->toArray(),
                $request->getFiles()
                        ->toArray()
            ) );

            if ( $form->isValid() )
            {
                $data     = $form->getData();
                $imported = $serviceLocator->get( 'Grid\Customize\Model\Importer' )
                                           ->import( $data['file']['tmp_name'] );

                if ( $imported )
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

                if ( $return )
                {
                    return $this->redirect()
                                ->toUrl( $return );
                }
                else if ( $imported )
                {
                    return $this->redirect()
                                ->toRoute( 'Grid\Paragraph\Render\Paragraph', array(
                                    'locale'        => (string) $this->locale(),
                                    'paragraphId'   => $imported,
                                ) );
                }
            }
        }

        return array(
            'form' => $form,
        );
    }

    /**
     * Export paragraph action
     */
    public function exportAction()
    {
        $params         = $this->params();
        $paragraphId    = $params->fromRoute( 'paragraphId' );
        $serviceLocator = $this->getServiceLocator();
        $paragraphModel = $serviceLocator->get( 'Grid\Paragraph\Model\Paragraph\Model' );
        $paragraph      = $paragraphModel->find( $paragraphId );

        if ( empty( $paragraph ) )
        {
            $this->getResponse()
                 ->setResultCode( 404 );

            return;
        }

        $zipFile = $serviceLocator->get( 'Grid\Customize\Model\Exporter' )
                                  ->export( $paragraph->id );

        $name = strtolower( $paragraph->name );

        if ( empty( $name ) )
        {
            $name = 'paragraph-' . $paragraph->id;
        }

        $response = Readfile::fromFile(
            $zipFile,
            'application/zip',
            $name . '.zip',
            true
        );

        $this->getEvent()
             ->setResponse( $response );

        return $response;
    }

}
