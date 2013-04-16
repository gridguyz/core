<?php

namespace Grid\Core\Controller;

use Zork\Stdlib\Message;
use Zork\Mvc\Controller\AbstractAdminController;

class ModuleController extends AbstractAdminController
{

    /**
     * @var array
     */
    protected $aclRights = array(
        '' => array(
            'sysadmin' => 'edit',
        ),
        'index' => array(
            'sysadmin.modules' => 'edit',
        ),
    );

    public function indexAction()
    {
        $request    = $this->getRequest();
        $locator    = $this->getServiceLocator();
        $model      = $locator->get( 'Grid\Core\Model\Module\Model' );
        $structure  = $model->find(null);

        $moduleConfigs = $this->getServiceLocator()
                      ->get( 'Configuration' )
                        ['modules']
                        ['Grid\Core']
                        ['modules'];

        /* @var $moduleForm \Zend\Form\Form */
        $moduleForm = $this->getServiceLocator()
                      ->get( 'Form' )
                      ->get( 'Grid\Core\Module' );

        $moduleForm->get( 'modules' )
                   ->setValueOptions($moduleConfigs);

//        $moduleForm->bind( $model->getData() );
        $moduleForm->setHydrator( $model->getMapper() )
                   ->bind( $structure );

        if ( $request->isPost() )
        {
            $moduleForm->setData( $request->getPost() );

            if ( $moduleForm->isValid() && $structure->save() )
            {
                $this->messenger()
                     ->add( 'default.form.module.success',
                            'default', Message::LEVEL_INFO );
            }
            else
            {
                $this->messenger()
                     ->add( 'default.form.module.failed',
                            'default', Message::LEVEL_ERROR );
            }

        }

        return array(
            'moduleForm' => $moduleForm
        );
    }

}
