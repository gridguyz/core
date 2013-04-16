<?php

namespace Grid\Core\Controller;

use Zork\Stdlib\Message;
use Grid\Core\Controller\AbstractListController;

/**
 * UriController
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class UriController extends AbstractListController
{

    /**
     * @var array
     */
    protected $aclRights = array(
        '' => array(
            'uri' => 'view',
        ),
        'edit' => array(
            'uri' => 'edit',
        ),
        'delete' => array(
            'uri' => 'delete',
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
                    ->get( 'Grid\Core\Model\Uri\Model' )
                    ->getPaginator();
    }

    /**
     * Edit an uri
     */
    public function editAction()
    {
        $params     = $this->params();
        $request    = $this->getRequest();
        $locator    = $this->getServiceLocator();
        $model      = $locator->get( 'Grid\Core\Model\Uri\Model' );
        $form       = $locator->get( 'Form' )
                              ->create( 'Grid\Core\Uri' );

        if ( ( $id = $params->fromRoute( 'id' ) ) )
        {
            $uri = $model->find( $id );

            if ( empty( $uri ) )
            {
                $this->getResponse()
                     ->setStatusCode( 404 );

                return;
            }
        }
        else
        {
            if ( ! $this->getServiceLocator()
                        ->get( 'Grid\User\Model\Permissions\Model' )
                        ->isAllowed( 'uri', 'create' ) )
            {
                $this->getResponse()
                     ->setStatusCode( 403 );

                return;
            }

            $uri = $model->create( array() );
        }

        /* @var $form \Zend\Form\Form */
        $form->setHydrator( $model->getMapper() )
             ->bind( $uri );

        if ( $request->isPost() )
        {
            $form->setData( $request->getPost() );

            if ( $form->isValid() && $uri->save() )
            {
                $this->messenger()
                     ->add( 'uri.form.success',
                            'uri', Message::LEVEL_INFO );

                return $this->redirect()
                            ->toRoute( 'Grid\Core\Uri\List', array(
                                'locale' => (string) $this->locale(),
                            ) );
            }
            else
            {
                $this->messenger()
                     ->add( 'uri.form.failed',
                            'uri', Message::LEVEL_ERROR );
            }
        }

        $form->setCancel(
            $this->url()
                 ->fromRoute( 'Grid\Core\Uri\List', array(
                        'locale' => (string) $this->locale(),
                    ) )
        );

        return array(
            'form'  => $form,
            'uri'   => $uri,
        );
    }

    /**
     * Delete an uri
     */
    public function deleteAction()
    {
        $params     = $this->params();
        $locator    = $this->getServiceLocator();
        $model      = $locator->get( 'Grid\Core\Model\Uri\Model' );
        $uri        = $model->find( $params->fromRoute( 'id' ) );

        if ( empty( $uri ) )
        {
            $this->getResponse()
                 ->setStatusCode( 404 );

            return;
        }

        if ( $uri->delete() )
        {
            $this->messenger()
                 ->add( 'uri.form.success',
                        'uri', Message::LEVEL_INFO );
        }
        else
        {
            $this->messenger()
                 ->add( 'uri.form.failed',
                        'uri', Message::LEVEL_ERROR );
        }

        return $this->redirect()
                    ->toRoute( 'Grid\Core\Uri\List', array(
                        'locale' => (string) $this->locale(),
                    ) );
    }

    /**
     * Set to default an uri
     */
    public function setDefaultAction()
    {
        $params     = $this->params();
        $locator    = $this->getServiceLocator();
        $model      = $locator->get( 'Grid\Core\Model\Uri\Model' );
        $uri        = $model->find( $params->fromRoute( 'id' ) );

        if ( empty( $uri ) )
        {
            $this->getResponse()
                 ->setStatusCode( 404 );

            return;
        }

        $uri->default = true;

        if ( $uri->save() )
        {
            $this->messenger()
                 ->add( 'uri.form.success',
                        'uri', Message::LEVEL_INFO );
        }
        else
        {
            $this->messenger()
                 ->add( 'uri.form.failed',
                        'uri', Message::LEVEL_ERROR );
        }

        return $this->redirect()
                    ->toRoute( 'Grid\Core\Uri\List', array(
                        'locale' => (string) $this->locale(),
                    ) );
    }

}
