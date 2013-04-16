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
        $config = $this->getServiceLocator()
                       ->get( 'Configuration' )
                            [ 'view_manager' ]
                            [ 'head_defaults' ];

        if ( empty( $config['headLink']['customize']['href'] ) )
        {
            throw new \LogicException(
                'There is no custom.css setting in the "view_manager.head_defaults"'
            );
        }

        $request  = $this->getRequest();
        $fileBase = self::PUBLIC_DIR . '/';
        $filePath = trim( $config['headLink']['customize']['href'], '/' );
        $file     = $fileBase . $filePath;

        if ( ! file_exists( $file ) )
        {
            $dir = dirname( $file );

            if ( ! file_exists( $dir ) )
            {
                mkdir( $dir, 0777, true );
            }

            $iterator = new RegexIterator(
                new FileSystemIterator(
                    dirname( $file ),
                    FileSystemIterator::SKIP_DOTS |
                    FileSystemIterator::KEY_AS_FILENAME |
                    FileSystemIterator::CURRENT_AS_PATHNAME
                ),
                '#.*[/\\\\]custom\..*\.css$#'
            );

            foreach ( $iterator as $path )
            {
                @ unlink( $path );
            }

            $this->getServiceLocator()
                 ->get( 'Grid\Customize\Model\Sheet\Model' )
                 ->findComplete()
                 ->render( $file );
        }

        $path = $request->getUri()
                        ->getPath();

        if ( trim( $path, '/' ) != $filePath )
        {
            return $this->redirect()
                        ->toUrl( '/' . $filePath );
        }

        $response = Readfile::fromFile( $file, 'text/css' );

        $this->getEvent()
             ->setResponse( $response );

        return $response;
    }

    /**
     * @TODO remove
     * @return \Zend\Http\Response
     */
    public function fileToSqlAction()
    {
        $request    = $this->getRequest();
        $response   = $this->getResponse();
        $parser     = new \Customize\Model\CssParser();
        $sheet      = $parser->parse( $request->getQuery( 'file' ) );

        $response->setContent( $sheet->_toSql( $request->getQuery( 'schema' ) ) )
                 ->getHeaders()
                 ->addHeaderLine( 'Content-Type', 'text/plain; charset=utf-8' );

        return $response;
    }

    /**
     * @TODO remove
     * @return \Zend\Http\Response
     */
    public function dbToSqlAction()
    {
        $params     = $this->params();
        $request    = $this->getRequest();
        $response   = $this->getResponse();
        $sheet      = $this->getServiceLocator()
                           ->get( 'Grid\Customize\Model\Sheet\Model' )
                           ->findByRoot( $params->fromRoute( 'id' ) );

        $response->setContent( $sheet->_toSql( $request->getQuery( 'schema' ) ) )
                 ->getHeaders()
                 ->addHeaderLine( 'Content-Type', 'text/plain; charset=utf-8' );

        return $response;
    }

}
