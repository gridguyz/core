<?php

namespace Grid\Core\Controller;

use Zork\Stdlib\Message;
use Zork\Stdlib\String;
use Zork\Mvc\Controller\AbstractAdminController;

/**
 * SettingsController
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class SettingsController extends AbstractAdminController
{

    /**
     * Settings admin
     */
    public function indexAction()
    {
        $params      = $this->params();
        $request     = $this->getRequest();
        $section     = $params->fromRoute( 'section', null );
        $permissions = $this->getPermissionsModel();

        if ( empty( $section ) )
        {
            $this->getResponse()
                 ->setStatusCode( 404 );

            return;
        }

        $model    = $this->getServiceLocator()
                         ->get( 'Grid\Core\Model\Settings\Model' );
        $settings = $model->find( $section );

        if ( empty( $settings ) )
        {
            $this->getResponse()
                 ->setStatusCode( 404 );

            return;
        }

        if ( ! $permissions->isAllowed( 'settings.' . $section, 'edit' ) )
        {
            $this->getResponse()
                 ->setStatusCode( 403 );

            return;
        }

        /* @var $form \Zend\Form\Form */
        $name = ucfirst( String::camelize( $section ) );
        $form = $this->getServiceLocator()
                     ->get( 'Form' )
                     ->get( 'Grid\Core\\Settings\\' . $name );

        if ( empty( $form ) )
        {
            $this->getResponse()
                 ->setStatusCode( 404 );

            return;
        }

        $form->setHydrator( $model->getMapper() )
             ->bind( $settings );

        if ( $request->isPost() )
        {
            $form->setData( $request->getPost() );

            if ( $form->isValid() && $settings->save() )
            {
                $this->messenger()
                     ->add( 'settings.form.all.success',
                            'settings', Message::LEVEL_INFO );

                return $this->redirect()
                            ->toUrl( '?refresh' );
            }
            else
            {
                $this->messenger()
                     ->add( 'settings.form.all.failed',
                            'settings', Message::LEVEL_ERROR );
            }
        }

        return array(
            'section'       => $section,
            'name'          => $name,
            'form'          => $form,
            'textDomain'    => $model->getMapper()
                                     ->getDefinitions()
                                     ->getTextDomain( $section ),
        );
    }

}
