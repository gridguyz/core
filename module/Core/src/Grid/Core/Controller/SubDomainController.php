<?php

namespace Grid\Core\Controller;

use Zork\Stdlib\Message;
use Grid\Core\Controller\AbstractListController;

/**
 * SubDomainController
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class SubDomainController extends AbstractListController
{

    /**
     * @var array
     */
    protected $aclRights = array(
        '' => array(
            'subDomain' => 'view',
        ),
        'edit' => array(
            'subDomain' => 'edit',
        ),
        'delete' => array(
            'subDomain' => 'delete',
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
                    ->get( 'Grid\Core\Model\SubDomain\Model' )
                    ->getPaginator();
    }

    /**
     * Edit a sub-domain
     */
    public function editAction()
    {
        $default    = false;
        $params     = $this->params();
        $request    = $this->getRequest();
        $locator    = $this->getServiceLocator();
        $model      = $locator->get( 'Grid\Core\Model\SubDomain\Model' );
        /* @var $form \Zend\Form\Form */
        $form       = $locator->get( 'Form' )
                              ->create( 'Grid\Core\SubDomain' );

        if ( ( $id = $params->fromRoute( 'id' ) ) )
        {
            $subDomain = $model->find( $id );

            if ( empty( $subDomain ) )
            {
                $this->getResponse()
                     ->setStatusCode( 404 );

                return;
            }

            if ( empty( $subDomain->subdomain ) )
            {
                $default = true;

                $form->get( 'subdomain' )
                     ->setRequired( false );
            }
        }
        else
        {
            if ( ! $this->getServiceLocator()
                        ->get( 'Grid\User\Model\Permissions\Model' )
                        ->isAllowed( 'subDomain', 'create' ) )
            {
                $this->getResponse()
                     ->setStatusCode( 403 );

                return;
            }

            $subDomain = $model->create( array() );
        }

        /* @var $form \Zend\Form\Form */
        $form->setHydrator( $model->getMapper() )
             ->bind( $subDomain );

        if ( $request->isPost() )
        {
            $form->setData( $request->getPost() );

            if ( $form->isValid() && $subDomain->save() )
            {
                if ( $default && ! empty( $subDomain->subdomain ) )
                {
                    $newDefault = $model->create( array(
                        'subdomain'         => '',
                        'locale'            => $subDomain->locale,
                        'defaultLayoutId'   => $subDomain->defaultLayoutId,
                        'defaultContentId'  => $subDomain->defaultContentId,
                    ) );

                    $newDefault->save();
                }

                $this->messenger()
                     ->add( 'subDomain.form.success',
                            'subDomain', Message::LEVEL_INFO );

                return $this->redirect()
                            ->toRoute( 'Grid\Core\SubDomain\List', array(
                                'locale' => (string) $this->locale(),
                            ) );
            }
            else
            {
                $this->messenger()
                     ->add( 'subDomain.form.failed',
                            'subDomain', Message::LEVEL_ERROR );
            }
        }

        $form->setCancel(
            $this->url()
                 ->fromRoute( 'Grid\Core\SubDomain\List', array(
                        'locale' => (string) $this->locale(),
                    ) )
        );

        return array(
            'form'      => $form,
            'subDomain' => $subDomain,
        );
    }

    /**
     * Delete a sub-domain
     */
    public function deleteAction()
    {
        $params     = $this->params();
        $locator    = $this->getServiceLocator();
        $model      = $locator->get( 'Grid\Core\Model\SubDomain\Model' );
        $subDomain  = $model->find( $params->fromRoute( 'id' ) );

        if ( empty( $subDomain ) )
        {
            $this->getResponse()
                 ->setStatusCode( 404 );

            return;
        }

        if ( $subDomain->delete() )
        {
            $this->messenger()
                 ->add( 'subDomain.form.success',
                        'subDomain', Message::LEVEL_INFO );
        }
        else
        {
            $this->messenger()
                 ->add( 'subDomain.form.failed',
                        'subDomain', Message::LEVEL_ERROR );
        }

        return $this->redirect()
                    ->toRoute( 'Grid\Core\SubDomain\List', array(
                        'locale' => (string) $this->locale(),
                    ) );
    }

}
