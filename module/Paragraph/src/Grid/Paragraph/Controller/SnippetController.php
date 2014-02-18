<?php

namespace Grid\Paragraph\Controller;

use Zork\Stdlib\Message;
use Zend\Stdlib\ArrayUtils;
use Grid\Core\Controller\AbstractListController;

/**
 * SnippetController
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class SnippetController extends AbstractListController
{

    /**
     * @var array
     */
    protected $aclRights = array(
        '' => array(
            'paragraph.snippet' => 'view',
        ),
        'create' => array(
            'paragraph.snippet' => 'create',
        ),
        'upload' => array(
            'paragraph.snippet' => 'create',
        ),
        'edit' => array(
            'paragraph.snippet' => 'edit',
        ),
        'delete' => array(
            'paragraph.snippet' => 'delete',
        ),
    );

    /**
     * Get paginator list
     *
     * @return \Zend\Paginator\Paginator
     */
    protected function getPaginator()
    {
        return $this->getServiceLocator()
                    ->get( 'Grid\Paragraph\Model\Snippet\Model' )
                    ->getPaginator();
    }

    /**
     * Create a snippet
     */
    public function createAction()
    {
        $request    = $this->getRequest();
        $locator    = $this->getServiceLocator();
        $model      = $locator->get( 'Grid\Paragraph\Model\Snippet\Model' );
        $snippet    = $model->create();
        $form       = $locator->get( 'Form' )
                              ->create( 'Grid\Paragraph\Snippet\Create' );

        /* @var $form \Zend\Form\Form */
        $form->setHydrator( $model->getMapper() )
             ->bind( $snippet );

        if ( $request->isPost() )
        {
            $form->setData( $request->getPost() );

            if ( $form->isValid() && $snippet->save() )
            {
                $this->messenger()
                     ->add( 'paragraph.form.snippet.success',
                            'paragraph', Message::LEVEL_INFO );

                return $this->redirect()
                            ->toRoute( 'Grid\Paragraph\Snippet\List', array(
                                'locale' => (string) $this->locale(),
                            ) );
            }
            else
            {
                $this->messenger()
                     ->add( 'paragraph.form.snippet.failed',
                            'paragraph', Message::LEVEL_ERROR );
            }
        }

        $form->setCancel(
            $this->url()
                 ->fromRoute( 'Grid\Paragraph\Snippet\List', array(
                        'locale' => (string) $this->locale(),
                    ) )
        );

        return array(
            'form'  => $form,
        );
    }

    /**
     * Upload a snippet
     */
    public function uploadAction()
    {
        $request    = $this->getRequest();
        $locator    = $this->getServiceLocator();
        $model      = $locator->get( 'Grid\Paragraph\Model\Snippet\Model' );
        $snippet    = $model->create();
        $form       = $locator->get( 'Form' )
                              ->create( 'Grid\Paragraph\Snippet\Upload' );

        /* @var $form \Zend\Form\Form */
        $form->setHydrator( $model->getMapper() )
             ->bind( $snippet );

        if ( $request->isPost() )
        {
            $form->setData( ArrayUtils::merge(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            ) );

            if ( $form->isValid() &&
                 ( $form->getData()
                        ->getOption( 'overwrite' ) ||
                   $model->isNameAvailable( $snippet->name ) ) &&
                 $snippet->save() )
            {
                $this->messenger()
                     ->add( 'paragraph.form.snippet.success',
                            'paragraph', Message::LEVEL_INFO );

                return $this->redirect()
                            ->toRoute( 'Grid\Paragraph\Snippet\List', array(
                                'locale' => (string) $this->locale(),
                            ) );
            }
            else
            {
                $this->messenger()
                     ->add( 'paragraph.form.snippet.failed',
                            'paragraph', Message::LEVEL_ERROR );
            }
        }

        $form->setCancel(
            $this->url()
                 ->fromRoute( 'Grid\Paragraph\Snippet\List', array(
                        'locale' => (string) $this->locale(),
                    ) )
        );

        return array(
            'form'  => $form,
        );
    }

    /**
     * Edit a snippet
     */
    public function editAction()
    {
        static $typeToMime = array(
            'css'   => 'text/css',
            'js'    => 'text/javascript',
        );

        /* @var $form \Zork\Form\Form */
        $params     = $this->params();
        $request    = $this->getRequest();
        $locator    = $this->getServiceLocator();
        $model      = $locator->get( 'Grid\Paragraph\Model\Snippet\Model' );
        $snippet    = $model->find( $params->fromRoute( 'name' ) );
        $form       = $locator->get( 'Form' )
                              ->create( 'Grid\Paragraph\Snippet\Edit' );

        if ( empty( $snippet ) )
        {
            $this->getResponse()
                 ->setStatusCode( 404 );

            return;
        }

        $type = $snippet->type;

        if ( ! empty( $typeToMime[$type] ) )
        {
            $form->get( 'code' )
                 ->setAttribute(
                     'data-js-codeeditor-mode',
                     $typeToMime[$type]
                 );
        }

        /* @var $form \Zend\Form\Form */
        $form->setHydrator( $model->getMapper() )
             ->bind( $snippet );

        if ( $request->isPost() )
        {
            $form->setData( $request->getPost() );

            if ( $form->isValid() && $snippet->save() )
            {
                $this->messenger()
                     ->add( 'paragraph.form.snippet.success',
                            'paragraph', Message::LEVEL_INFO );

                return $this->redirect()
                            ->toRoute( 'Grid\Paragraph\Snippet\List', array(
                                'locale' => (string) $this->locale(),
                            ) );
            }
            else
            {
                $this->messenger()
                     ->add( 'paragraph.form.snippet.failed',
                            'paragraph', Message::LEVEL_ERROR );
            }
        }

        $form->setCancel(
            $this->url()
                 ->fromRoute( 'Grid\Paragraph\Snippet\List', array(
                        'locale' => (string) $this->locale(),
                    ) )
        );

        return array(
            'form'      => $form,
            'snippet'   => $snippet,
        );
    }

    /**
     * Delete a snippet
     */
    public function deleteAction()
    {
        $params     = $this->params();
        $locator    = $this->getServiceLocator();
        $model      = $locator->get( 'Grid\Paragraph\Model\Snippet\Model' );
        $snippet    = $model->find( $params->fromRoute( 'name' ) );

        if ( empty( $snippet ) )
        {
            $this->getResponse()
                 ->setStatusCode( 404 );

            return;
        }

        if ( $snippet->delete() )
        {
            $this->messenger()
                 ->add( 'paragraph.action.delete.success',
                        'paragraph', Message::LEVEL_INFO );
        }
        else
        {
            $this->messenger()
                 ->add( 'paragraph.action.delete.failed',
                        'paragraph', Message::LEVEL_ERROR );
        }

        return $this->redirect()
                    ->toRoute( 'Grid\Paragraph\Snippet\List', array(
                        'locale' => (string) $this->locale(),
                    ) );
    }

}
