<?php

namespace Grid\Customize\Controller;

use RegexIterator;
use FileSystemIterator;
use Zork\Http\PhpEnvironment\Response\Readfile;
use Zend\Mvc\Controller\AbstractActionController;

/**
 * RenderController
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class RenderController extends AbstractActionController
{

    /**
     * @var string
     */
    const PUBLIC_DIR = './public';

    /**
     * Render-content action
     */
    public function customCssAction()
    {
        /* @var $siteInfo \Zork\Db\SiteInfo */
        /* @var $model \Grid\Customize\Model\Extra\Model */
        /* @var $structure \Grid\Customize\Model\Extra\Structure */
        $params     = $this->params();
        $request    = $this->getRequest();
        $id         = $params->fromRoute( 'id' );
        $rootId     = is_numeric( $id ) ? (int) $id : null;
        $schema     = $params->fromRoute( 'schema' );
        $locator    = $this->getServiceLocator();
        $siteInfo   = $locator->get( 'Zork\Db\SiteInfo' );

        if ( $schema != $siteInfo->getSchema() )
        {
            $this->getResponse()
                 ->setResultCode( 403 );

            return;
        }

        $model      = $locator->get( 'Grid\Customize\Model\Extra\Model' );
        $structure  = $model->findByRoot( $rootId );

        if ( empty( $structure ) )
        {
            $this->getResponse()
                 ->setResultCode( 404 );

            return;
        }

        $url        = $this->url();
        $hash       = $structure->updated->toHash();
        $cssPath    = $url->fromRoute( 'Grid\Customize\Render\CustomCss', array(
            'schema' => $schema,
            'id'     => $rootId ?: 'global',
            'hash'   => $hash,
        ) );

        $cssFile = static::PUBLIC_DIR . $cssPath;

        if ( ! is_file( $cssFile ) )
        {
            $dir = dirname( $cssFile );

            if ( ! is_dir( $dir ) )
            {
                mkdir( $dir, 0777, true );
            }

            $iterator = new RegexIterator(
                new FileSystemIterator(
                    $dir,
                    FileSystemIterator::SKIP_DOTS |
                    FileSystemIterator::KEY_AS_FILENAME |
                    FileSystemIterator::CURRENT_AS_PATHNAME
                ),
                '#.*[/\\\\]custom\.[^/\\\\]+\.css$#'
            );

            foreach ( $iterator as $unlinkPath )
            {
                @ unlink( $unlinkPath );
            }

            $this->getServiceLocator()
                 ->get( 'Grid\Customize\Model\Sheet\Model' )
                 ->findByRoot( $rootId )
                 ->render( $cssFile );
        }

        $requestPath = $request->getUri()
                               ->getPath();

        if ( ltrim( $requestPath, '/' ) != ltrim( $cssPath, '/' ) )
        {
            return $this->redirect()
                        ->toUrl( $cssPath );
        }

        $response = Readfile::fromFile( $cssFile, 'text/css' );

        $this->getEvent()
             ->setResponse( $response );

        return $response;
    }

}
