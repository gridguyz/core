<?php

namespace Grid\Customize\Controller;

use Zork\Stdlib\Message;
use Zend\Stdlib\ArrayUtils;
use Grid\Customize\Model\ImportResult;
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
        ),
    );

    /**
     * @var string[int]
     */
    protected static $importErrorMessages = array(
        ImportResult::UNKNOWN_ERROR                     => 'customize.import.failed.unknownError',
        ImportResult::FILE_NOT_EXISTS                   => 'customize.import.failed.fileNotExists',
        ImportResult::FILE_NOT_ZIP                      => 'customize.import.failed.fileNotZip',
        ImportResult::STRUCTURE_XML_NOT_FOUND           => 'customize.import.failed.structureXmlNotFound',
        ImportResult::STRUCTURE_XML_DOCTYPE_MISMATCH    => 'customize.import.failed.structureXmlDoctypeMismatch',
        ImportResult::STRUCTURE_XML_NOT_VALID           => 'customize.import.failed.structureXmlNotValid',
        ImportResult::STRUCTURE_XML_UNKNOWN_VERSION     => 'customize.import.failed.structureXmlUnknownVersion',
        ImportResult::STRUCTURE_TYPE_NOT_ALLOWED        => 'customize.import.failed.structureTypeNotAllowed',
    );

    /**
     * Gte valid return-uri
     *
     * @param   string      $returnUri
     * @param   string|null $default
     * @return  string|null
     */
    protected function getValidReturnUri( $returnUri, $default = null )
    {
        $match      = array();
        $returnUri  = ltrim( str_replace( '\\', '/', $returnUri ), "\n\r\t\v\e\f" );

        if ( ! preg_match( '#^/([^/].*)?$#', $returnUri, $match ) )
        {
            return $default;
        }

        return $returnUri;
    }

    /**
     * Import paragraph action
     */
    public function importAction()
    {
        /* @var $form \Zork\Form\Form */
        $request        = $this->getRequest();
        $serviceLocator = $this->getServiceLocator();
        $returnUri      = $request->getQuery( 'returnUri' );
        $return         = $this->getValidReturnUri( $returnUri );
        $form           = $serviceLocator->get( 'Form' )
                                         ->get( 'Grid\Customize\Import' );

        if ( $request->isPost() )
        {
            $form->setData( $data = ArrayUtils::merge(
                $request->getPost()
                        ->toArray(),
                $request->getFiles()
                        ->toArray()
            ) );

            if ( ! empty( $data['returnUri'] ) )
            {
                $return = $this->getValidReturnUri(
                    $data['returnUri'],
                    $return
                );
            }

            if ( $form->isValid() )
            {
                $data       = $form->getData();
                $file       = $data['file']['tmp_name'];
                $name       = $data['file']['name'];
                $translator = $serviceLocator->get( 'translator' );
                $result     = $serviceLocator->get( 'Grid\Customize\Model\Importer' )
                                             ->import( $file, $name );

                if ( $result->isSuccess() )
                {
                    $this->messenger()
                         ->add(
                             sprintf(
                                 $translator->translate( 'customize.import.success.link.%s' ),
                                 $this->url()
                                      ->fromRoute( 'Grid\Paragraph\Render\Paragraph', array(
                                          'locale'        => (string) $this->locale(),
                                          'paragraphId'   => $result->getCreatedParagraphId(),
                                      ) )
                             ),
                             false,
                             Message::LEVEL_INFO
                         );
                }
                else
                {
                    $code = $result->getCode();

                    if ( ! isset( static::$importErrorMessages[$code] ) )
                    {
                        $code = ImportResult::UNKNOWN_ERROR;
                    }

                    $this->messenger()
                         ->add(
                             sprintf(
                                 $translator->translate( 'customize.import.failed.message.%s.%s' ),
                                 $translator->translate( static::$importErrorMessages[$code] ),
                                 $result->getErrorMessage()
                             ),
                             false,
                             Message::LEVEL_ERROR
                         );
                }

                if ( $return )
                {
                    return $this->redirect()
                                ->toUrl( $return );
                }
                else if ( $result->isSuccess() )
                {
                    return $this->redirect()
                                ->toRoute( 'Grid\Customize\CssAdmin\List', array(
                                    'locale' => (string) $this->locale(),
                                ) );
                }
            }
        }
        else if ( $return )
        {
            $form->get( 'returnUri' )
                 ->setValue( $return );
        }

        $form->setCancel(
            $return ? $return
                    : $this->url()
                           ->fromRoute( 'Grid\Customize\CssAdmin\List', array(
                               'locale' => (string) $this->locale(),
                           ) )
        );

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
