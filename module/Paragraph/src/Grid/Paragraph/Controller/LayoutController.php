<?php

namespace Grid\Paragraph\Controller;

use Zork\Stdlib\Message;
use Grid\Core\Controller\AbstractListController;

/**
 * LayoutController
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class LayoutController extends AbstractListController
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
                    ->getPaginator( 'layout' );
    }

    /**
     * Edit a layout
     */
    public function editAction()
    {
        $params     = $this->params();
        $request    = $this->getRequest();
        $locator    = $this->getServiceLocator();
        $model      = $locator->get( 'Grid\Paragraph\Model\Paragraph\Model' );
        $form       = $locator->get( 'Form' )
                              ->create( 'Grid\Paragraph\Layout' );

        if ( ( $id = $params->fromRoute( 'id' ) ) )
        {
            $paragraph = $model->find( $id );

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
        }
        else
        {
            if ( ! $this->getPermissionsModel()
                        ->isAllowed( 'paragraph.layout', 'create' ) )
            {
                $this->getResponse()
                     ->setStatusCode( 403 );

                return;
            }

            $paragraph = $model->create( array(
                'type'      => 'layout',
                'created'   => time(),
            ) );
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
                     ->add( 'paragraph.form.layout.success',
                            'paragraph', Message::LEVEL_INFO );

                return $this->redirect()
                            ->toRoute( 'Grid\Paragraph\Layout\List', array(
                                'locale' => (string) $this->locale(),
                            ) );
            }
            else
            {
                $this->messenger()
                     ->add( 'paragraph.form.layout.failed',
                            'paragraph', Message::LEVEL_ERROR );
            }
        }

        $form->setCancel(
            $this->url()
                 ->fromRoute( 'Grid\Paragraph\Layout\List', array(
                        'locale' => (string) $this->locale(),
                    ) )
        );

        return array(
            'form'      => $form,
            'paragraph' => $paragraph,
        );
    }

    /**
     * Clone a layout
     */
    public function cloneAction()
    {
        if ( ! $this->getPermissionsModel()
                    ->isAllowed( 'paragraph.layout', 'create' ) )
        {
            $this->getResponse()
                 ->setStatusCode( 403 );

            return;
        }

        $params     = $this->params();
        $locator    = $this->getServiceLocator();
        $model      = $locator->get( 'Grid\Paragraph\Model\Paragraph\Model' );
        $paragraph  = $model->find( $params->fromRoute( 'id' ) );

        if ( empty( $paragraph ) || $paragraph->type !== 'layout' )
        {
            $this->getResponse()
                 ->setStatusCode( 404 );

            return;
        }

        $cloneId = $model->cloneFrom( $paragraph->id );

        if ( $cloneId )
        {
            $clone = $model->find( $cloneId );

            if ( ! empty( $clone ) )
            {
                $clone->name = rtrim( $clone->name ) . ' '
                             . $locator->get( 'Zork\I18n\Translator\Translator' )
                                       ->translate( 'default.cloned', 'default' );

                $clone->save();
            }

            $this->messenger()
                 ->add( 'paragraph.action.clone.success',
                        'paragraph', Message::LEVEL_INFO );
        }
        else
        {
            $this->messenger()
                 ->add( 'paragraph.action.clone.failed',
                        'paragraph', Message::LEVEL_ERROR );
        }

        return $this->redirect()
                    ->toRoute( 'Grid\Paragraph\Layout\List', array(
                        'locale' => (string) $this->locale(),
                    ) );
    }

    /**
     * Delete a layout
     */
    public function deleteAction()
    {
        $params     = $this->params();
        $locator    = $this->getServiceLocator();
        $model      = $locator->get( 'Grid\Paragraph\Model\Paragraph\Model' );
        $paragraph  = $model->find( $params->fromRoute( 'id' ) );

        if ( empty( $paragraph ) || $paragraph->type !== 'layout' )
        {
            $this->getResponse()
                 ->setStatusCode( 404 );

            return;
        }

        if ( ! $paragraph->isDeletable() )
        {
            $this->getResponse()
                 ->setStatusCode( 403 );

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
                    ->toRoute( 'Grid\Paragraph\Layout\List', array(
                        'locale' => (string) $this->locale(),
                    ) );
    }

}
