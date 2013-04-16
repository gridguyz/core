<?php

namespace Grid\Paragraph\Controller;

use Zork\Stdlib\Message;
use Grid\Core\Controller\AbstractListController;

/**
 * WidgetController
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class WidgetController extends AbstractListController
{

    /**
     * @var array
     */
    protected $aclRights = array(
        '' => array(
            'paragraph.widget' => 'view',
        ),
        'edit' => array(
            'paragraph.widget' => 'edit',
        ),
        'delete' => array(
            'paragraph.widget' => 'delete',
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
                    ->get( 'Grid\Paragraph\Model\Paragraph\Model' )
                    ->getPaginator( 'widget' );
    }

    /**
     * Edit a widget
     */
    public function editAction()
    {
        $params     = $this->params();
        $request    = $this->getRequest();
        $locator    = $this->getServiceLocator();
        $model      = $locator->get( 'Grid\Paragraph\Model\Paragraph\Model' );
        $paragraph  = $model->find( $params->fromRoute( 'id' ) );
        $form       = $locator->get( 'Form' )
                              ->create( 'Grid\Paragraph\Widget' );

        if ( empty( $paragraph ) )
        {
            $this->getResponse()
                 ->setStatusCode( 404 );

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
                     ->add( 'paragraph.form.widget.success',
                            'paragraph', Message::LEVEL_INFO );

                return $this->redirect()
                            ->toRoute( 'Grid\Paragraph\Widget\List', array(
                                'locale' => (string) $this->locale(),
                            ) );
            }
            else
            {
                $this->messenger()
                     ->add( 'paragraph.form.widget.failed',
                            'paragraph', Message::LEVEL_ERROR );
            }
        }

        $form->setCancel(
            $this->url()
                 ->fromRoute( 'Grid\Paragraph\Widget\List', array(
                        'locale' => (string) $this->locale(),
                    ) )
        );

        return array(
            'form'      => $form,
            'paragraph' => $paragraph,
        );
    }

    /**
     * Delete a widget
     */
    public function deleteAction()
    {
        $params     = $this->params();
        $locator    = $this->getServiceLocator();
        $model      = $locator->get( 'Grid\Paragraph\Model\Paragraph\Model' );
        $paragraph  = $model->find( $params->fromRoute( 'id' ) );

        if ( empty( $paragraph ) || $paragraph->type !== 'widget' )
        {
            $this->getResponse()
                 ->setStatusCode( 404 );

            return;
        }

        if ( $paragraph->delete() )
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
                    ->toRoute( 'Grid\Paragraph\Widget\List', array(
                        'locale' => (string) $this->locale(),
                    ) );
    }

}
