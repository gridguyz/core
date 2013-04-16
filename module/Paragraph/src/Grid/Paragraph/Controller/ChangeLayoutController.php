<?php

namespace Grid\Paragraph\Controller;

use Zork\Stdlib\Message;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Exception;
use Zend\View\Model\ViewModel;
use Zork\Mvc\Controller\AbstractAdminController;
use Grid\Paragraph\Model\Paragraph\Structure\LayoutAwareInterface;

/**
 * ChangeLayoutController
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class ChangeLayoutController extends AbstractAdminController
{

    /**
     * @var array
     */
    protected $disableLayoutActions = array(
        'local'     => true,
        'import'    => true,
    );

    /**
     * @var array
     */
    protected $aclRights = array(
        '' => array(
            'paragraph.layout' => 'create'
        ),
    );

    /**
     * @var \Paragraph\Model\Paragraph\StructureInterface
     */
    protected $paragraph;

    /**
     * @var int|null
     */
    protected $paragraphId;

    /**
     * @var \Paragraph\Model\Paragraph\Model
     */
    protected $paragraphModel;

    /**
     * @return \Paragraph\Model\Paragraph\StructureInterface
     */
    public function findParagraph()
    {
        if ( ( null === $this->paragraph && null !== $this->paragraphId ) ||
             null !== $this->paragraph && $this->paragraph->id != $this->paragraphId )
        {
            $this->paragraph = $this->getParagraphModel()
                                    ->find( $this->paragraphId );

            if ( ! $this->paragraph instanceof LayoutAwareInterface )
            {
                throw new Exception\DomainException(
                    'Paragraph needs to be implement LayoutAwareInterface at ' .
                    __METHOD__
                );
            }
        }

        return $this->paragraph;
    }

    /**
     * @return \Paragraph\Model\Paragraph\Model
     */
    public function getParagraphModel()
    {
        if ( null === $this->paragraphModel )
        {
            $this->paragraphModel = $this->getServiceLocator()
                                         ->get( 'Grid\Paragraph\Model\Paragraph\Model' );
        }

        return $this->paragraphModel;
    }

    /**
     * Execute the request
     *
     * @param  MvcEvent $e
     * @return mixed
     * @throws \Zend\Mvc\Exception\DomainException
     */
    public function onDispatch( MvcEvent $event )
    {
        $this->paragraphId = (int) $this->params()
                                        ->fromRoute( 'paragraphId' );

        if ( empty( $this->paragraphId ) )
        {
            $this->paragraphId = null;
        }

        return parent::onDispatch( $event );
    }

    /**
     * @param string $action
     * @return bool|string
     */
    public function checkActionRights( $action )
    {
        $result = parent::checkActionRights( $action );

        if ( ! $result )
        {
            if ( empty( $this->paragraphId ) )
            {
                if ( ! $this->getPermissionsModel()
                            ->isAllowed( 'subdomain', 'edit' ) )
                {
                    return $this->notAllowedRedirectUrl;
                }
            }
            else
            {
                if ( ! $this->findParagraph()
                            ->isEditable() )
                {
                    return $this->notAllowedRedirectUrl;
                }
            }
        }

        return $result;
    }

    /**
     * Get actual layout ID
     *
     * @return int
     */
    protected function getLayoutId()
    {
        $paragraph = $this->findParagraph();

        if ( empty( $paragraph ) )
        {
            /* @var $structure \Core\Model\SubDomain\Structure */
            $structure = $this->getServiceLocator()
                              ->get( 'Grid\Core\Model\SubDomain\Model' )
                              ->findActual();

            return $structure->defaultLayoutId;
        }
        else
        {
            return $paragraph->layoutId;
        }
    }

    /**
     * @param  int $newLayoutId
     * @return bool
     */
    protected function saveLayout( $newLayoutId )
    {
        if ( empty( $newLayoutId ) )
        {
            return false;
        }

        $newLayoutId = (int) $newLayoutId;
        $paragraph   = $this->findParagraph();

        if ( empty( $paragraph ) )
        {
            /* @var $structure \Core\Model\SubDomain\Structure */
            $structure = $this->getServiceLocator()
                              ->get( 'Grid\Core\Model\SubDomain\Model' )
                              ->findActual();

            $structure->defaultLayoutId = $newLayoutId;
        }
        else
        {
            /* @var $structure \Paragraph\Model\Paragraph\Structure\LayoutAwareInterface */
            $structure = $paragraph->setLayoutId( $newLayoutId );
        }

        return (bool) $structure->save();
    }

    /**
     * Change to local layout
     */
    public function localAction()
    {
        $success = null;
        $request = $this->getRequest();
        $data    = $request->getPost();
        $form    = $this->getServiceLocator()
                        ->get( 'Form' )
                        ->create( 'Grid\Paragraph\ChangeLayout\Local', array(
                            'returnUri' => $request->getQuery( 'returnUri' ),
                        ) );

        $form->setAttribute(
            'action',
            $this->url()
                 ->fromRoute( 'Grid\Paragraph\ChangeLayout\Local', array(
                     'locale'       => (string) $this->locale(),
                     'paragraphId'  => $this->paragraphId,
                     'returnUri'    => $request->getQuery( 'returnUri' ),
                 ) )
        );

        if ( empty( $this->paragraphId ) )
        {
            $form->get( 'layoutId' )
                 ->setEmptyOption( null );
        }

        /* @var $form \Zend\Form\Form */
        $paragraph = $this->findParagraph();

        if ( ! empty( $paragraph ) )
        {
            $form->setHydrator( $paragraph->getMapper() )
                 ->bind( $paragraph );
        }

        if ( $request->isPost() )
        {
            $form->setData( $data );

            if ( $form->isValid() )
            {
                $data       = $form->getData();
                $success    = $this->saveLayout( $data['layoutId'] );
            }
            else
            {
                $success    = false;
            }
        }

        if ( true === $success )
        {
            $this->messenger()
                 ->add( 'paragraph.action.changeLayout.success',
                        'paragraph', Message::LEVEL_INFO );
        }

        if ( false === $success )
        {
            $this->messenger()
                 ->add( 'paragraph.action.changeLayout.failed',
                        'paragraph', Message::LEVEL_ERROR );
        }

        if ( null !== $success )
        {
            return $this->redirect()
                        ->toUrl( $form->get( 'returnUri' )
                                      ->getValue() );
        }

        $view = new ViewModel( array(
            'form' => $form,
        ) );
        return $view->setTerminal( true );
    }

    /**
     * Import layout & change to it
     */
    public function importAction()
    {
        $success = null;
        $request = $this->getRequest();
        $data    = $request->getPost();
        $form    = $this->getServiceLocator()
                        ->get( 'Form' )
                        ->create( 'Grid\Paragraph\ChangeLayout\Import', array(
                            'returnUri' => $request->getQuery( 'returnUri' ),
                        ) );

        $form->setAttribute(
            'action',
            $this->url()
                 ->fromRoute( 'Grid\Paragraph\ChangeLayout\Import', array(
                     'locale'       => (string) $this->locale(),
                     'paragraphId'  => $this->paragraphId,
                     'returnUri'    => $request->getQuery( 'returnUri' ),
                 ) )
        );

        if ( $request->isPost() )
        {
            $form->setData( $data );

            if ( $form->isValid() )
            {
                $data       = $form->getData();
                $beforeId   = $this->getLayoutId();
                $clonedId   = $this->getParagraphModel()
                                   ->cloneFrom( $data['importId'], '_central' );
                $success    = $this->saveLayout( $clonedId );

                $this->getServiceLocator()
                     ->get( 'Grid\Menu\Model\Menu\Model' )
                     ->interleaveParagraphs( $clonedId, $beforeId );
            }
            else
            {
                $success    = false;
            }
        }

        if ( true === $success )
        {
            $this->messenger()
                 ->add( 'paragraph.action.importLayout.success',
                        'paragraph', Message::LEVEL_INFO );
        }

        if ( false === $success )
        {
            $this->messenger()
                 ->add( 'paragraph.action.importLayout.failed',
                        'paragraph', Message::LEVEL_ERROR );
        }

        if ( null !== $success )
        {
            return $this->redirect()
                        ->toUrl( $form->get( 'returnUri' )
                                      ->getValue() );
        }

        $view = new ViewModel( array(
            'form' => $form,
        ) );
        return $view->setTerminal( true );
    }

}
