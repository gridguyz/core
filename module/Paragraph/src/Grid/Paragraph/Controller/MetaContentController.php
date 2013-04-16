<?php

namespace Grid\Paragraph\Controller;

use Zork\Stdlib\Message;
use Grid\Core\Controller\AbstractListController;

/**
 * ContentController
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class MetaContentController extends AbstractListController
{

    /**
     * Get paginator list
     *
     * @return \Zend\Paginator\Paginator
     */
    protected function getPaginator()
    {
        return $this->getServiceLocator()
                    ->get( 'Grid\Paragraph\Model\Paragraph\Model' )
                    ->getPaginator( 'metaContent' );
    }

    /**
     * Edit a meta-content
     */
    public function editAction()
    {
        $params     = $this->params();
        $request    = $this->getRequest();
        $locator    = $this->getServiceLocator();
        $id         = $params->fromRoute( 'id' );
        $model      = $locator->get( 'Grid\Paragraph\Model\Paragraph\Model' );
        $paragraph  = $model->find( $id );
        $form       = $locator->get( 'Form' )
                              ->create( 'Grid\Paragraph\MetaContent' );

        if ( empty( $paragraph ) )
        {
            $this->getResponse()
                 ->setStatusCode( 404 );

            return;
        }

        if ( ! $paragraph->isEditable() )
        {
            $this->getResponse()
                 ->setStatusCode( 403 );

            return;
        }

        /* @var $form \Zend\Form\Form */
        $form->setHydrator( $model->getMapper() )
             ->bind( $paragraph );

        if ( $request->isPost() )
        {
            $form->setData( $request->getPost() );

            if ( $form->isValid() && $paragraph->save() )
            {
                $this->messenger()
                     ->add( 'paragraph.form.content.success',
                            'paragraph', Message::LEVEL_INFO );

                return $this->redirect()
                            ->toRoute( 'Grid\Paragraph\MetaContent\List', array(
                                'locale' => (string) $this->locale(),
                            ) );
            }
            else
            {
                $this->messenger()
                     ->add( 'paragraph.form.content.failed',
                            'paragraph', Message::LEVEL_ERROR );
            }
        }

        $form->setCancel(
            $this->url()
                 ->fromRoute( 'Grid\Paragraph\MetaContent\List', array(
                        'locale' => (string) $this->locale(),
                    ) )
        );

        return array(
            'form'      => $form,
            'paragraph' => $paragraph,
        );
    }

}
