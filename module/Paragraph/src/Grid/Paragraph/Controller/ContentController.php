<?php

namespace Grid\Paragraph\Controller;

use Zork\Stdlib\Message;
use Grid\Core\Controller\AbstractListController;

/**
 * ContentController
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class ContentController extends AbstractListController
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
                    ->getPaginator( 'content' );
    }

    /**
     * Edit a content
     */
    public function editAction()
    {
        $params     = $this->params();
        $request    = $this->getRequest();
        $locator    = $this->getServiceLocator();
        $model      = $locator->get( 'Grid\Paragraph\Model\Paragraph\Model' )
                              ->setLocale( $this->getAdminLocale() );
        $form       = $locator->get( 'Form' )
                              ->create( 'Grid\Paragraph\Content' );

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
                        ->isAllowed( 'paragraph.content', 'create' ) )
            {
                $this->getResponse()
                     ->setStatusCode( 403 );

                return;
            }

            $userId = $this->getAuthenticationService()
                           ->getIdentity()
                           ->id;

            $paragraph = $model->create( array(
                'type'          => 'content',
                'created'       => time(),
                'userId'        => $userId,
                'editUsers'     => array( $userId ),
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
                     ->add( 'paragraph.form.content.success',
                            'paragraph', Message::LEVEL_INFO );

                return $this->redirect()
                            ->toRoute( 'Grid\Paragraph\Content\List', array(
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
                 ->fromRoute( 'Grid\Paragraph\Content\List', array(
                        'locale' => (string) $this->locale(),
                    ) )
        );

        return array(
            'form'      => $form,
            'paragraph' => $paragraph,
        );
    }

    /**
     * Clone a content
     */
    public function cloneAction()
    {
        if ( ! $this->getPermissionsModel()
                    ->isAllowed( 'paragraph.content', 'create' ) )
        {
            $this->getResponse()
                 ->setStatusCode( 403 );

            return;
        }

        $params     = $this->params();
        $locator    = $this->getServiceLocator();
        $model      = $locator->get( 'Grid\Paragraph\Model\Paragraph\Model' )
                              ->setLocale( $this->getAdminLocale() );
        $paragraph  = $model->find( $params->fromRoute( 'id' ) );

        if ( empty( $paragraph ) || $paragraph->type !== 'content' )
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
                $userId = $this->getAuthenticationService()
                               ->getIdentity()
                               ->id;

                if ( empty( $clone->userId ) )
                {
                    $clone->userId = $userId;
                }

                if ( ! in_array( $userId, $clone->editUsers ) )
                {
                    $clone->editUsers = array_merge(
                        $clone->editUsers,
                        array( $userId )
                    );
                }

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
                    ->toRoute( 'Grid\Paragraph\Content\List', array(
                        'locale' => (string) $this->locale(),
                    ) );
    }

    /**
     * Delete a content
     */
    public function deleteAction()
    {
        $params     = $this->params();
        $locator    = $this->getServiceLocator();
        $model      = $locator->get( 'Grid\Paragraph\Model\Paragraph\Model' );
        $paragraph  = $model->find( $params->fromRoute( 'id' ) );

        if ( empty( $paragraph ) || $paragraph->type !== 'content' )
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
                    ->toRoute( 'Grid\Paragraph\Content\List', array(
                        'locale' => (string) $this->locale(),
                    ) );
    }

}
