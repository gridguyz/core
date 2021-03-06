<?php

namespace Grid\Customize\Controller;

use Zork\Stdlib\Message;
use Zork\Stdlib\DateTime;
use Grid\Core\Controller\AbstractListController;

/**
 * CssAdminController
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class CssAdminController extends AbstractListController
{

    /**
     * @const string
     */
    const PREVIEW_FILE = '/tmp/customize-preview-%s';

    /**
     * @const string
     */
    const PREVIEW_EXTENSION = '.css';

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
        /* @var $form \Zork\Form\Form */
        /* @var $model \Grid\Customize\Model\Sheet\Model */
        $params     = $this->params();
        $request    = $this->getRequest();
        $locator    = $this->getServiceLocator();
        $id         = $params->fromRoute( 'id' );
        $rootId     = is_numeric( $id ) ? (int) $id : null;
        $model      = $locator->get( 'Grid\Customize\Model\Sheet\Model' );
        $form       = $locator->get( 'Form' )
                              ->get( 'Grid\Customize\Css' );
        $sheet      = $model->find( $rootId );

        $form->setHydrator( $model->getMapper() )
             ->bind( $sheet );

        if ( $request->isPost() )
        {
            $form->setData( $request->getPost() );

            if ( $form->isValid() && $sheet->save() )
            {
                $this->messenger()
                     ->add( 'customize.form.success',
                            'customize', Message::LEVEL_INFO );

                $cssPreview = $locator->get( 'Grid\Customize\Service\CssPreview' );

                if ( $cssPreview->hasPreviewById( $rootId ) )
                {
                    @ unlink( 'public' . $cssPreview->getPreviewById( $rootId ) );
                    $cssPreview->unsetPreviewById( $rootId );
                }

                return $this->redirect()
                            ->toRoute( 'Grid\Customize\CssAdmin\List', array(
                                'locale' => (string) $this->locale(),
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
            'sheet' => $sheet,
        );
    }

    /**
     * Preview a css
     */
    public function previewAction()
    {
        /* @var $form \Zork\Form\Form */
        /* @var $model \Grid\Customize\Model\Sheet\Model */
        $params     = $this->params();
        $request    = $this->getRequest();
        $locator    = $this->getServiceLocator();
        $id         = $params->fromRoute( 'id' );
        $rootId     = is_numeric( $id ) ? (int) $id : null;
        $model      = $locator->get( 'Grid\Customize\Model\Sheet\Model' );
        $form       = $locator->get( 'Form' )
                              ->get( 'Grid\Customize\Css' );
        $sheet      = $model->createEmpty( $rootId );

        $form->setHydrator( $model->getMapper() )
             ->bind( $sheet );

        if ( $request->isPost() )
        {
            $form->setData( $request->getPost() );

            if ( $form->isValid() )
            {
                $this->messenger()
                     ->add( 'customize.preview.success',
                            'customize', Message::LEVEL_INFO );

                $id     = $rootId === null ? 'global' : $rootId;
                $prefix = 'public';
                $file   = sprintf( static::PREVIEW_FILE, $id ) . '.';

                do
                {
                    $suffix = new DateTime;
                }
                while ( file_exists( $prefix . $file . $suffix->toHash() .
                                     static::PREVIEW_EXTENSION ) );

                $url    = $file . $suffix->toHash() . static::PREVIEW_EXTENSION;
                $path   = $prefix . $url;
                $sheet->render( $path );

                $locator->get( 'Grid\Customize\Service\CssPreview' )
                        ->setPreviewById( $rootId, $url );

                if ( null === $rootId )
                {
                    return $this->redirect()
                                ->toUrl( '/' );
                }
                else
                {
                    return $this->redirect()
                                ->toRoute( 'Grid\Paragraph\Render\Paragraph', array(
                                    'locale'        => (string) $this->locale(),
                                    'paragraphId'   => $rootId,
                                ) );
                }
            }
            else
            {
                $this->messenger()
                     ->add( 'customize.preview.failed',
                            'customize', Message::LEVEL_ERROR );

                return $this->redirect()
                            ->toRoute( 'Grid\Customize\CssAdmin\List', array(
                                'locale' => (string) $this->locale(),
                            ) );
            }
        }
    }

    /**
     * Cancel an edit of a css
     */
    public function cancelAction()
    {
        /* @var $cssPreview \Grid\Customize\Service\CssPreview */
        $params     = $this->params();
        $locator    = $this->getServiceLocator();
        $cssPreview = $locator->get( 'Grid\Customize\Service\CssPreview' );
        $id         = $params->fromRoute( 'id' );
        $rootId     = is_numeric( $id ) ? (int) $id : null;

        if ( $cssPreview->hasPreviewById( $rootId ) )
        {
            @ unlink( 'public' . $cssPreview->getPreviewById( $rootId ) );
            $cssPreview->unsetPreviewById( $rootId );
        }

        return $this->redirect()
                    ->toRoute( 'Grid\Customize\CssAdmin\List', array(
                        'locale' => (string) $this->locale(),
                    ) );
    }

    /**
     * Reset all previews
     */
    public function resetPreviewsAction()
    {
        /* @var $cssPreview \Grid\Customize\Service\CssPreview */
        $cssPreview = $this->getServiceLocator()
                           ->get( 'Grid\Customize\Service\CssPreview' );

        foreach ( $cssPreview->getPreviews() as $url )
        {
            @ unlink( 'public' . $url );
        }

        $cssPreview->unsetPreviews();

        $this->messenger()
             ->add( 'customize.preview.reset',
                    'customize', Message::LEVEL_INFO );

        return $this->redirect()
                    ->toUrl( '/' );
    }

}
