<?php

namespace Grid\Mail\Controller;

use Grid\Core\Controller\AbstractListController;

/**
 * Grid\Mail\Controller\TemplateController
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class TemplateController extends AbstractListController
{

    /**
     * @var array
     */
    protected $aclRights = array(
        '' => array(
            'mail.template' => 'view',
        ),
        'edit' => array(
            'mail.template' => 'edit',
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
                    ->get( 'Grid\Mail\Model\Template\Model' )
                    ->getPaginator();
    }

    /**
     * Edit a template
     */
    public function editAction()
    {
        $params     = $this->params();
        $request    = $this->getRequest();
        $locator    = $this->getServiceLocator();
        $model      = $locator->get( 'Grid\Mail\Model\Template\Model' );
        $form       = $locator->get( 'Form' )
                              ->create( 'Grid\Mail\Template' );
        $name       = $params->fromRoute( 'templateName' );
        $locale     = $this->getAdminLocale();
        $template   = $model->findByName( $name, $locale );
        $original   = $template->locale;
        $clone      = $original != $locale;
        $success    = null;

        if ( empty( $template ) )
        {
            $this->getResponse()
                 ->setStatusCode( 404 );

            return;
        }

        if ( $clone )
        {
            $template = $template->cloneToLocale( $locale );
        }

        /* @var $form \Zend\Form\Form */
        $form->setHydrator( $model->getMapper() )
             ->bind( $template );

        if ( $request->isPost() )
        {
            $form->setData( $request->getPost() );
            $success = $form->isValid() && $template->save();
        }

        $form->setCancel(
            $this->url()
                 ->fromRoute( 'Grid\Mail\Template\List', array(
                        'locale' => (string) $this->locale(),
                    ) )
        );

        return array(
            'clone'         => $clone,
            'locale'        => $locale,
            'original'      => $original,
            'template'      => $template,
            'form'          => $form,
            'success'       => $success,
        );
    }

}
