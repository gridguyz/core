<?php

namespace Grid\Menu\Controller;

use Zork\Stdlib\Message;
use Grid\Menu\Model\Menu\StructureInterface;
use Zork\Mvc\Controller\AbstractAdminController;

/**
 * AdminController
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class AdminController extends AbstractAdminController
{

    /**
     * @var array
     */
    protected $aclRights = array(
        'editor'    => array(
            'menu'  => 'edit',
        ),
        'create'    => array(
            'menu'  => 'create',
        ),
        'edit'      => array(
            'menu'  => 'edit',
        ),
    );

    /**
     * @var array
     */
    protected $disableLayoutActions = array(
        'create' => true,
        'edit'   => true,
    );

    /**
     * @var \Menu\Model\Menu\Model
     */
    protected $model;

    /**
     * @return \Menu\Model\Menu\Model
     */
    protected function getModel()
    {
        if ( null === $this->model )
        {
            $this->model = $this->getServiceLocator()
                                ->get( 'Grid\Menu\Model\Menu\Model' )
                                ->setLocale( $this->getAdminLocale() );
        }

        return $this->model;
    }

    /**
     * Menu-editor
     */
    public function editorAction()
    {
        /* @var $model   \Menu\Model\Menu\Model */
        /* @var $factory \Menu\Model\Menu\StructureFactory */
        $model   = $this->getModel();
        $service = $this->getServiceLocator();
        $factory = $service->get( 'Grid\Menu\Model\Menu\StructureFactory' );

        return array(
            'locale' => $this->getAdminLocale(),
            'forest' => $model->findRenderList(),
            'types'  => array_filter(
                array_keys( $factory->getRegisteredAdapters() )
            ),
        );
    }

    /**
     * Get form for menu-item structure
     *
     * @param \Menu\Model\Menu\StructureInterface $structure
     * @return \Zend\Form\Form
     */
    protected function getForm( StructureInterface $structure )
    {
        /* @var $form \Zend\Form\Form */
        /* @var $type \Zend\Form\Form */
        $service = $this->getServiceLocator()
                        ->get( 'Form' );
        $form    = $service->create( 'Grid\Menu\Meta\Base' );
        $meta    = $service->get( 'Grid\Menu\Meta\Type' );
        $type    = $structure->getType();

        if ( $meta->has( $type ) )
        {
            foreach ( $meta->get( $type ) as $element )
            {
                $form->add( clone $element );
            }
        }

        $form->add( array(
            'type'  => 'Zork\Form\Element\Submit',
            'name'  => 'save',
            'attributes' => array(
                'value'  => 'menu.form.submit',
            ),
        ) );

        $form->setHydrator( $this->getModel()->getMapper() )
             ->bind( $structure );

        return $form;
    }

    /**
     * Create menu-item
     */
    public function createAction()
    {
        $success    = null;
        $params     = $this->params();
        $request    = $this->getRequest();
        $type       = $params->fromRoute( 'type' );
        $parent     = $params->fromRoute( 'parentId', null );
        $menu       = $this->getModel()
                           ->create( array(
                                'type' => $type,
                            ) );

        $form = $this->getForm( $menu );

        if ( $request->isPost() )
        {
            $form->setData( $request->getPost() );

            if ( $form->isValid() && $menu->save() &&
                 ( empty( $parent ) || $this->getModel()
                                            ->appendTo( $menu->id, $parent ) ) )
            {
                $success = true;

                $this->messenger()
                     ->add( 'menu.form.success',
                            'menu', Message::LEVEL_INFO );
            }
            else
            {
                $success = false;

                $this->messenger()
                     ->add( 'menu.form.failed',
                            'menu', Message::LEVEL_ERROR );
            }
        }

        return array(
            'success'   => $success,
            'form'      => $form,
            'menu'      => $menu,
        );
    }

    /**
     * Edit menu-item
     */
    public function editAction()
    {
        $success    = null;
        $params     = $this->params();
        $request    = $this->getRequest();
        $menuId     = $params->fromRoute( 'menuId' );
        $menu       = $this->getModel()
                           ->find( $menuId );

        if ( empty( $menu ) )
        {
            $this->getResponse()
                 ->setStatusCode( 404 );

            return;
        }

        $form = $this->getForm( $menu );

        if ( $request->isPost() )
        {
            $form->setData( $request->getPost() );

            if ( $form->isValid() && $menu->save() )
            {
                $success = true;
                $form->bind( $menu );

                $this->messenger()
                     ->add( 'menu.form.success',
                            'menu', Message::LEVEL_INFO );
            }
            else
            {
                $success = false;

                $this->messenger()
                     ->add( 'menu.form.failed',
                            'menu', Message::LEVEL_ERROR );
            }
        }

        return array(
            'success'   => $success,
            'form'      => $form,
            'menu'      => $menu,
        );
    }

}
