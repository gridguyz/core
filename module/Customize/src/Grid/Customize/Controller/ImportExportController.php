<?php

namespace Grid\Customize\Controller;

use Zork\Stdlib\Message;
use Zork\Http\PhpEnvironment\Response\Zip;
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
        'import' => array(
            'paragraph' => 'create',
        ),
    );

    /**
     * Import paragraph action
     */
    public function importAction()
    {
        $request        = $this->getRequest();
        $return         = (string) $request->getQuery( 'returnUri' );
        $serviceLocator = $this->getServiceLocator();
        $form           = $serviceLocator->get( 'Form' )
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

                $imported = $serviceLocator->get( 'Grid\Customize\Model\Importer' )
                                           ->import( $file );

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

        /* @var $zip \ZipArchive */
        $zip = $serviceLocator->get( 'Grid\Customize\Model\Exporter' )
                              ->export( $paragraph->id );

        $name = strtolower( $paragraph->name );

        if ( empty( $name ) )
        {
            $name = 'paragraph-' . $paragraph->id;
        }

        $response = Zip::fromArchive( $zip, $name . '.zip' );

        $this->getEvent()
             ->setResponse( $response );

        return $response;
    }

}
