<?php

namespace Grid\Tag\Controller;

use Zork\Stdlib\Message;
use Grid\Core\Controller\AbstractListController;

/**
 * AdminController
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class AdminController extends AbstractListController
{

    /**
     * Get paginator list
     *
     * @return \Zend\Paginator\Paginator
     */
    protected function getPaginator()
    {
        return $this->getServiceLocator()
                    ->get( 'Grid\Tag\Model\Tag\Model' )
                    ->getPaginator();
    }

    /**
     * Edit a tag
     */
    public function editAction()
    {
        $params     = $this->params();
        $request    = $this->getRequest();
        $locator    = $this->getServiceLocator();
        $id         = $params->fromRoute( 'id' );
        $model      = $locator->get( 'Grid\Tag\Model\Tag\Model' );
        $form       = $locator->get( 'Form' )
                              ->create( 'Grid\Tag\Edit' );

        if ( empty( $id ) )
        {
            $tag = $model->create( array() );
        }
        else
        {
            $tag = $model->find( $id );

            if ( empty( $tag ) )
            {
                $this->getResponse()
                     ->setStatusCode( 404 );

                return;
            }
        }

        if ( ! $this->getPermissionsModel()
                    ->isAllowed( 'tag.entry',
                                 empty( $id ) ? 'create' : 'edit' ) )
        {
            $this->getResponse()
                 ->setStatusCode( 403 );

            return;
        }

        /* @var $form \Zend\Form\Form */
        $form->setHydrator( $model->getMapper() )
             ->bind( $tag );

        if ( $request->isPost() )
        {
            $form->setData( $request->getPost() );

            if ( $form->isValid() && $tag->save() )
            {
                $this->messenger()
                     ->add( 'tag.form.edit.success',
                            'tag', Message::LEVEL_INFO );

                return $this->redirect()
                            ->toRoute( 'Grid\Tag\Admin\List', array(
                                'locale' => (string) $this->locale(),
                            ) );
            }
            else
            {
                $this->messenger()
                     ->add( 'tag.form.edit.failed',
                            'tag', Message::LEVEL_ERROR );
            }
        }

        $form->setCancel(
            $this->url()
                 ->fromRoute( 'Grid\Tag\Admin\List', array(
                        'locale' => (string) $this->locale(),
                    ) )
        );

        return array(
            'form'  => $form,
            'tag'   => $tag,
        );
    }

    /**
     * Delete a tag
     */
    public function deleteAction()
    {
        $params     = $this->params();
        $locator    = $this->getServiceLocator();
        $model      = $locator->get( 'Grid\Tag\Model\Tag\Model' );
        $tag        = $model->find( $params->fromRoute( 'id' ) );

        if ( empty( $tag ) )
        {
            $this->getResponse()
                 ->setStatusCode( 404 );

            return;
        }

        if ( ! $this->getPermissionsModel()
                    ->isAllowed( 'tag.entry', 'delete' ) )
        {
            $this->getResponse()
                 ->setStatusCode( 403 );

            return;
        }

        if ( $tag->delete() )
        {
            $this->messenger()
                 ->add( 'tag.action.delete.success',
                        'tag', Message::LEVEL_INFO );
        }
        else
        {
            $this->messenger()
                 ->add( 'tag.action.delete.failed',
                        'tag', Message::LEVEL_ERROR );
        }

        return $this->redirect()
                    ->toRoute( 'Grid\Tag\Admin\List', array(
                        'locale' => (string) $this->locale(),
                    ) );
    }

}
