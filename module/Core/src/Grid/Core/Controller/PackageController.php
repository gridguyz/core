<?php

namespace Grid\Core\Controller;

use Zork\Stdlib\String;
use Zork\Stdlib\Message;
use Zend\View\Model\JsonModel;
use Grid\Core\Form\TransformValues;
use Zork\Process\BackgroundProcess;
use Zork\Mvc\Controller\AbstractAdminController;

class PackageController extends AbstractAdminController
{

    /**
     * @var array
     */
    protected $aclRights = array(
        '' => array(
            'sysadmin.packages' => 'edit',
        ),
    );

    /**
     * List packages
     */
    public function listAction()
    {
        $params = $this->params();
        $filter = $params->fromPost( 'filter', $params->fromQuery( 'filter', array() ) );
        $order  = $params->fromPost( 'order',  $params->fromQuery( 'order',  true    ) );
        $page   = $params->fromPost( 'page',   $params->fromQuery( 'page',   0       ) );
        $model  = $this->getServiceLocator()
                       ->get( 'Grid\Core\Model\Package\Model' );

        return array(
            'page'          => (int)   $page,
            'filter'        => (array) $filter,
            'order'         => $order,
            'categories'    => $model->getCategories(),
            'paginator'     => $model->getPaginator( $filter, $order )
        );
    }

    /**
     * View a package
     */
    public function viewAction()
    {
        $params  = $this->params();
        $vendor  = $params->fromRoute( 'vendor' );
        $subname = $params->fromRoute( 'subname' );
        $name    = $vendor . '/' . $subname;
        $package = $this->getServiceLocator()
                        ->get( 'Grid\Core\Model\Package\Model' )
                        ->find( $name );

        if ( empty( $package ) )
        {
            $this->getResponse()
                 ->setStatusCode( 404 );

            return;
        }

        return array(
            'name'      => $name,
            'package'   => $package,
        );
    }

    /**
     * Update packages (info page)
     */
    public function updateAction()
    {
        return array();
    }

    /**
     * Update packages (info page)
     */
    public function runUpdateAction()
    {
        $process = new BackgroundProcess( array(
            'command'   => 'php',
            'arguments' => array( './bin/update.php' ),
        ) );

        return new JsonModel( array(
            'started' => $process->run(),
        ) );
    }

    /**
     * Install a package
     */
    public function installAction()
    {
        $extra   = array();
        $params  = $this->params();
        $vendor  = $params->fromRoute( 'vendor' );
        $subname = $params->fromRoute( 'subname' );
        $name    = $vendor . '/' . $subname;
        $service = $this->getServiceLocator();
        $model   = $service->get( 'Grid\Core\Model\Package\Model' );
        $forms   = $service->get( 'Form' );
        $package = $model->find( $name );

        if ( empty( $package ) )
        {
            $this->getResponse()
                 ->setStatusCode( 404 );

            return;
        }

        if ( ! $package->canInstall() )
        {
            $this->getResponse()
                 ->setStatusCode( 403 );

            return;
        }

        $formName = 'Grid\\Core\\Package\\Install\\'
            . String::camelize( $vendor, null, false ) . '\\'
            . String::camelize( $subname, null, false );

        /* @var $forms \Zork\Form\FormService */
        if ( $forms->has( $formName ) )
        {
            /* @var $form \Zork\Form\Form */
            $extraValid = false;
            $request    = $this->getRequest();
            $form       = $forms->get( $formName );

            if ( $request->isPost() )
            {
                $form->setData( $request->getPost() );

                if ( $form->isValid() )
                {
                    $extra      = $form->getData();
                    $extraValid = true;
                }
            }

            if ( ! $extraValid )
            {
                $form->add( array(
                    'type'  => 'Zork\Form\Element\Submit',
                    'name'  => 'submit',
                    'attributes'  => array(
                        'value' => 'admin.packages.action.install',
                    ),
                ) );

                return array(
                    'name'      => $name,
                    'package'   => $package,
                    'form'      => $form,
                );
            }

            if ( $form instanceof TransformValues )
            {
                $extra = $form->transformValues( $extra );
            }
        }

        if ( ! $model->install( $package, $extra ) )
        {
            $this->messenger()
                 ->add( 'admin.packages.install.failed',
                        'admin', Message::LEVEL_ERROR );

            return $this->redirect()
                        ->toRoute( 'Grid\Core\Admin\Package\View', array(
                            'locale'    => (string) $this->locale(),
                            'vendor'    => $vendor,
                            'subname'   => $subname,
                        ) );
        }

        return $this->redirect()
                    ->toRoute( 'Grid\Core\Admin\Package\Update', array(
                        'locale' => (string) $this->locale(),
                    ) );
    }

    /**
     * Install a package
     */
    public function removeAction()
    {
        $params  = $this->params();
        $vendor  = $params->fromRoute( 'vendor' );
        $subname = $params->fromRoute( 'subname' );
        $name    = $vendor . '/' . $subname;
        $model   = $this->getServiceLocator()
                        ->get( 'Grid\Core\Model\Package\Model' );
        $package = $model->find( $name );

        if ( empty( $package ) )
        {
            $this->getResponse()
                 ->setStatusCode( 404 );

            return;
        }

        if ( ! $package->canRemove() )
        {
            $this->getResponse()
                 ->setStatusCode( 403 );

            return;
        }

        if ( ! $model->remove( $package ) )
        {
            $this->messenger()
                 ->add( 'admin.packages.remove.failed',
                        'admin', Message::LEVEL_ERROR );

            return $this->redirect()
                        ->toRoute( 'Grid\Core\Admin\Package\View', array(
                            'locale'    => (string) $this->locale(),
                            'vendor'    => $vendor,
                            'subname'   => $subname,
                        ) );
        }

        return $this->redirect()
                    ->toRoute( 'Grid\Core\Admin\Package\Update', array(
                        'locale' => (string) $this->locale(),
                    ) );
    }

}
