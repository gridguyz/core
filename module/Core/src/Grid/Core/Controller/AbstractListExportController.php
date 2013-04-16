<?php

namespace Grid\Core\Controller;

use Zork\Data\Table;
use Zend\View\Model\ViewModel;
use Zork\Http\PhpEnvironment\Response\FileData;

/**
 * AbstractListController
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
abstract class AbstractListExportController extends AbstractListController
{

    /**
     * @var string|false
     * @abstract
     */
    protected $exportFileName = false;

    /**
     * @var array
     * @abstract
     */
    protected $exportFieldTypes = array();

    /**
     * @var array
     * @abstract
     */
    protected $exportFieldNames = array();

    /**
     * @return string|false
     */
    protected function getExportFileName( $type )
    {
        return $this->exportFileName
             ? $this->exportFileName . '.' . $type
             : false;
    }

    /**
     * @return array
     */
    protected function getExportFieldTypes()
    {
        return $this->exportFieldTypes;
    }

    /**
     * @return array
     */
    protected function getExportFieldNames()
    {
        $fieldNames = array();
        $translator = $this->getServiceLocator()
                           ->get( 'Zend\I18n\Translator\Translator' );

        foreach ( $this->exportFieldNames as $field => $name )
        {
            $fieldNames[$field] = $translator->translate(
                $name,
                strstr( $name, '.', true )
            );
        }

        return $fieldNames;
    }

    /**
     * Export action
     */
    public function exportAction()
    {
        $request    = $this->getRequest();
        $form       = $this->getServiceLocator()
                           ->get( 'Form' )
                           ->get( 'Grid\Core\ListExport' );

        if ( $request->isPost() )
        {
            $form->setData( $request->getPost() );

            if ( $form->isValid() )
            {
                $data       = $form->getData();
                $type       = $data['type'];
                $options    = array_merge(
                    (array) ( isset( $data[$type] ) ? $data[$type] : array() ),
                    array( 'fieldNames' => $this->getExportFieldNames() )
                );

                $iterator = $this->getPaginator()
                                 ->getAdapter()
                                 ->getItems( 0, null );

                $table = new Table(
                    $iterator,
                    $this->getExportFieldTypes()
                );

                $response = FileData::fromData(
                    $table->export( $type, $options ),
                    $this->getExportFileName( $type )
                );

                $this->getEvent()
                     ->setResponse( $response );

                return $response;
            }
        }

        $view = new ViewModel( array(
            'form' => $form->setAttribute(
                'action',
                $request->getRequestUri()
            ),
        ) );

        return $view->setTemplate( 'rowSet/export' )
                    ->setTerminal( true );
    }

}
