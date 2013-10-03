<?php

namespace Grid\Image\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zork\Image\Image;
use Zork\Http\PhpEnvironment\Response\Readfile;
use Grid\Image\View\Helper\Thumbnail;

/**
 * ThumbnailController
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class ThumbnailController extends AbstractActionController
{

    /**
     * Default method to use when not supplied
     */
    const DEFAULT_METHOD    = Thumbnail::DEFAULT_METHOD;

    /**
     * Default background-color to use when not supplied
     */
    const DEFAULT_BGCOLOR   = Thumbnail::DEFAULT_BGCOLOR;

    /**
     * Min width to accept
     */
    const MIN_WIDTH         = Thumbnail::MIN_WIDTH;

    /**
     * Min height to accept
     */
    const MIN_HEIGHT        = Thumbnail::MIN_HEIGHT;

    /**
     * Default width to use when supplied is smaller than min
     */
    const DEFAULT_WIDTH     = Thumbnail::DEFAULT_WIDTH;

    /**
     * Default height to use when supplied is smaller than min
     */
    const DEFAULT_HEIGHT    = Thumbnail::DEFAULT_HEIGHT;

    /**
     * Image for processing
     *
     * @var Zork_image
     */
    protected $image = null;

    /**
     * Path setting
     *
     * @var string
     */
    protected $path = null;

    /**
     * Method setting
     *
     * @var string
     */
    protected $method = null;

    /**
     * Crop setting
     *
     * @var bool
     */
    protected $crop = false;

    /**
     * Crop top setting
     *
     * @var int
     */
    protected $cropTop = null;

    /**
     * Crop left setting
     *
     * @var int
     */
    protected $cropLeft = null;

    /**
     * Crop width setting
     *
     * @var int
     */
    protected $cropWidth = null;

    /**
     * Crop top setting
     *
     * @var int
     */
    protected $cropHeight = null;

    /**
     * Width setting
     *
     * @var int
     */
    protected $width = self::DEFAULT_WIDTH;

    /**
     * Height setting
     * @var int
     */
    protected $height = self::DEFAULT_HEIGHT;

    /**
     * Background color setting
     *
     * @var string
     */
    protected $bgColor = self::DEFAULT_BGCOLOR;

    /**
     * Filter setting
     *
     * @var array
     */
    protected $filters = null;

    /**
     * File name setting
     *
     * @var string
     */
    protected $file = null;

    /**
     * Get file in path
     *
     * @return string
     */
    protected function filePath()
    {
        return urldecode( $this->path ) .
         '/' . urldecode( $this->file );
    }

    /**
     * Get output uri for settings
     *
     * @param int $mtime
     * @return string
     */
    protected function outputUri( $mtime )
    {
        return Thumbnail::THUMBNAIL_BASEURI
             . '/' . $this->path .
               '/' . $this->method .
               '/' . ( empty( $this->crop ) ? '' :
                         $this->cropLeft . 'x' .
                         $this->cropTop . '-' .
                         $this->cropWidth . 'x' .
                         $this->cropHeight . '-' ) .
                     $this->width .
               'x' . $this->height .
               '/' . $this->bgColor .
               '/' . ( empty( $this->filters ) ? 'none' :
                         implode( '-', (array) $this->filters ) ) .
               '/' . $mtime .
               '/' . $this->file;
    }

    /**
     * Get output path for settings
     *
     * @param int $mtime
     * @return string
     */
    protected function outputPath( $mtime )
    {
        return './public' . $this->outputUri( $mtime );
    }

    /**
     * Get mime-type of an image
     *
     * @return string
     */
    protected function getMime( $path )
    {
        static $finfo = null;

        $mime = null;

        if ( null === $mime &&
             function_exists( 'mime_content_type' ) &&
             ini_get( 'mime_magic.magicfile' ) )
        {
            $mime = mime_content_type( $path );
        }

        if ( null === $mime &&
             class_exists( 'finfo', false ) )
        {
            if ( empty( $finfo ) )
            {
                $finfo = @ finfo_open(
                    defined( 'FILEINFO_MIME_TYPE' )
                        ? FILEINFO_MIME_TYPE
                        : FILEINFO_MIME
                );
            }

            if ( ! empty( $finfo ) )
            {
                $mime = finfo_file( $finfo, $path );
            }
        }

        return $mime;
    }

    /**
     * Get image (& create if necessary)
     *
     * @return \Zork\Image\Image
     */
    protected function getImage( $path = null )
    {
        if ( is_null( $this->image ) )
        {
            if ( is_null( $path ) )
            {
                $this->image = Image::create(
                    $this->width,
                    $this->height
                );
            }
            else
            {
                $this->image = Image::open( $path );
            }
        }

        return $this->image;
    }

    /**
     * Error image
     */
    protected function error( $text = 500 )
    {
        $response = $this->getResponse();

        $this->getImage()
             ->text( $text, 5, 5, 'red' )
             ->render( $response );

        return $response;
    }

    /**
     * Default action if none provided
     *
     * @return array
     */
    public function indexAction()
    {
        return $this->error( 404 );
    }

    /**
     * Action called if matched action does not exist
     *
     * @return array
     */
    public function notFoundAction()
    {
        return $this->error( 404 );
    }

    /**
     * Thumbnail renderer action
     *
     * @return array
     */
    public function renderAction()
    {
        $redir      = false;
        $params     = $this->params();
        $pathname   = $params->fromRoute( 'pathname' );
        $matches    = array();
        $hasMatch   = preg_match( '#^(.+)/([^/]+)/([0-9x\-]+)/([^/]+)/' .
                                  '([^/]+)/([0-9]+)/([^/\?]+)(\?.*)?$#',
                                  $pathname, $matches );

        if ( ! $hasMatch )
        {
            return $this->error( 400 );
        }

        list( , $this->path,
                $this->method,
                $sizes,
                $this->bgColor,
                $filters,
                $mtime,
                $this->file ) = $matches;

        $sizes = explode( '-', $sizes, 3 );

        if ( count( $sizes ) == 3 )
        {
            $this->crop = true;

            list( $this->width,
                  $this->height ) = explode( 'x', $sizes[2], 2 );
            list( $this->cropLeft,
                  $this->cropTop ) = explode( 'x', $sizes[0], 2 );
            list( $this->cropWidth,
                  $this->cropHeight ) = explode( 'x', $sizes[1], 2 );
        }
        else
        {
            $this->crop = false;

            list( $this->width,
                  $this->height ) = explode( 'x', $sizes[0], 2 );
        }

        $mtime          = (int) $mtime;
        $this->width    = (int) $this->width;
        $this->height   = (int) $this->height;
        $inputPath      = Thumbnail::THUMBNAIL_BASEPATH
                        . '/' . $this->filePath();

        if ( 0 == $this->width &&
             self::MIN_HEIGHT <= $this->height &&
             is_file( $inputPath ) &&
             filesize( $inputPath ) > 0 )
        {
            $inputSizes = getimagesize( $inputPath );

            if ( ! empty( $inputSizes[0] ) && ! empty( $inputSizes[1] ) )
            {
                $this->width = (int) ( $inputSizes[0] * $this->height / $inputSizes[1] );
                $redir = true;
            }
        }

        if ( 0 == $this->height &&
             self::MIN_WIDTH <= $this->width &&
             is_file( $inputPath ) &&
             filesize( $inputPath ) > 0 )
        {
            $inputSizes = getimagesize( $inputPath );

            if ( ! empty( $inputSizes[0] ) && ! empty( $inputSizes[1] ) )
            {
                $this->height = (int) ( $inputSizes[1] * $this->width / $inputSizes[0] );
                $redir = true;
            }
        }

        if ( self::MIN_WIDTH > $this->width )
        {
            $this->width = self::DEFAULT_WIDTH;
            $redir = true;
        }

        if ( self::MIN_HEIGHT > $this->height )
        {
            $this->height = self::DEFAULT_HEIGHT;
            $redir = true;
        }

        if ( ! is_file( $inputPath ) )
        {
            return $this->error( 404 );
        }

        if ( 1 > filesize( $inputPath ) )
        {
            return $this->error( 415 );
        }

        if ( preg_match( '#^image/(x-|vnd.microsoft.)?icon?$#',
                         $this->getMime( $inputPath ) ) )
        {
            return $this->redirect()
                        ->toUrl( '/uploads/' . $this->filePath() );
        }

        if ( $filters == 'none' )
        {
            $this->filters = array();
        }
        else
        {
            $this->filters = explode( '-', $filters );
        }

        if ( ! Image::isResize( $this->method ) )
        {
            $this->method = self::DEFAULT_METHOD;
            $redir = true;
        }

        foreach ( $this->filters as $index => $filter )
        {
            $filter = explode( ',', $filter );

            if ( ! Image::isFilter( $filter[0] ) )
            {
                unset( $this->filters[$index] );
            }
        }

        if ( empty( $this->filters ) )
        {
            $newFilters = 'none';
        }
        else
        {
            $newFilters = implode( '-', $this->filters );
        }

        if ( $filters != $newFilters )
        {
            $redir = true;
        }

        $fmt = filemtime( $inputPath );

        if ( $fmt != $mtime )
        {
            $mtime = $fmt;
            $redir = true;
        }

        if ( $redir )
        {
            return $this->redirect()
                        ->toUrl( $this->outputUri( $mtime ) );
        }

        if ( ! $this->getImage( $inputPath ) )
        {
            return $this->error( 415 );
        }

        if ( $this->crop )
        {
            $this->getImage()
                 ->cropTo(
                     $this->cropLeft,
                     $this->cropTop,
                     $this->cropWidth,
                     $this->cropHeight
                 );
        }

        $this->getImage()
             ->resize(
                 $this->width,
                 $this->height,
                 $this->method
             );

        foreach ( $this->filters as $filter )
        {
            $filter = explode( ',', $filter );
            $name = array_shift( $filter );

            $this->getImage()
                 ->filter( $name, $filter );
        }

        $outputPath = $this->outputPath( $mtime );

        if ( ! is_dir( dirname( $outputPath ) ) )
        {
            mkdir( dirname( $outputPath ), 0777, true );
        }

        $this->getImage()
             ->render( $outputPath );

        $response = Readfile::fromFile( $outputPath, Image::typeToMimeType(
            $this->getImage()
                 ->getType()
        ) );

        $this->getEvent()
             ->setResponse( $response );

        return $response;
    }

}
