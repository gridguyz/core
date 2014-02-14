<?php

namespace Grid\Customize\Controller;

use Zork\Stdlib\Message;
use Grid\Core\Controller\AbstractListController;

/**
 * CssAdminController
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class CssAdminController extends AbstractListController
{

    /**
     * Define rights required to use this controller
     *
     * @var array
     */
    protected $aclRights = array(
        '' => array(
            'customize' => 'create'
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
                    ->get( 'Grid\Customize\Model\Sheet\Model' )
                    ->getPaginator();
    }

    /**
     * Edit a css
     */
    public function editAction()
    {
        $params     = $this->params();
        $request    = $this->getRequest();
        $locator    = $this->getServiceLocator();
        $id         = $params->fromRoute( 'id' );
        $rootId     = is_numeric( $id ) ? (int) $id : null;
        $model      = $locator->get( 'Grid\Customize\Model\Sheet\Model' );
        $form       = $locator->get( 'Form' )
                              ->get( 'Grid\Customize\Css' );
        $extra      = $model->findByRoot( $rootId );

        if ( $request->isPost() )
        {
            $form->setData( $request->getPost() );

            if ( $form->isValid() && $extra->save() )
            {
                $this->messenger()
                     ->add( 'customize.form.success',
                            'customize', Message::LEVEL_INFO );

                return $this->redirect()
                            ->toRoute( 'Grid\Customize\Admin\EditExtra', array(
                                'locale' => (string) $this->locale(),
                            ), array(
                                'query' => array(
                                    'refresh' => true,
                                ),
                            ) );
            }
            else
            {
                $this->messenger()
                     ->add( 'customize.form.failed',
                            'customize', Message::LEVEL_ERROR );
            }
        }

        return array(
            'form'  => $form,
            'extra' => $extra,
        );
    }

}
