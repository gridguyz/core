<?php

namespace Grid\Image\View\Helper;

use Zend\Stdlib\ArrayUtils;
use Zend\View\Helper\AbstractHelper;

/**
 * Grid\Image\View\Helper\Thumbnail
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Thumbnail extends AbstractHelper
{

    /**
     * Min width to accept
     */
    const MIN_WIDTH             = 10;

    /**
     * Min height to accept
     */
    const MIN_HEIGHT            = 10;

    /**
     * Default width to use when supplied is smaller than min
     */
    const DEFAULT_WIDTH         = 100;

    /**
     * Default height to use when supplied is smaller than min
     */
    const DEFAULT_HEIGHT        = 100;

    /**
     * Default method to use when not supplied
     */
    const DEFAULT_METHOD        = 'fit';

    /**
     * Default background-color to use when not supplied
     */
    const DEFAULT_BGCOLOR       = 'transparent';

    /**
     * @var string
     */
    const THUMBNAIL_BASEURI     = '/thumbnails';

    /**
     * @var string
     */
    const THUMBNAIL_BASEPATH    = './public/uploads';

    /**
     * @var string
     */
    const THUMBNAIL_FULLPATTERN = '#/uploads/([^/]+)/#';

    /**
     * Current schema name
     *
     * @var string
     */
    protected $schema;

    /**
     * Constructor
     *
     * @param string $schema
     */
    public function __construct( $schema )
    {
        $this->schema = (string) $schema;
    }

    /**
     * Get thumbnail uri for
     *
     * @param type $pathname
     * @param type $params
     * @return string
     */
    public function getThumbnailUri( $pathname, $params = array() )
    {
        $params = ArrayUtils::iteratorToArray( $params );
        $dir    = self::THUMBNAIL_BASEPATH;
        $uri    = self::THUMBNAIL_BASEURI;

        $matches  = array();
        $pathname = '/' . ltrim( $pathname, '/' );

        if ( preg_match( self::THUMBNAIL_FULLPATTERN, $pathname, $matches ) )
        {
            $schema   = $matches[1];
            $pathname = preg_replace( self::THUMBNAIL_FULLPATTERN,
                                      '/', $pathname );
        }
        else
        {
            $schema   = $this->schema;
        }

        if ( empty( $params['schemaIncluded'] ) )
        {
            $dir .= '/' . $schema;
            $uri .= '/' . $schema;
        }

        $add  = '/' . ltrim( dirname( $pathname ), '/' );
        $dir .= $add;
        $uri .= $add;
        $name = basename( $pathname );

        $filepath = $dir . '/' . $name;

        if ( ! is_file( $filepath ) || filesize( $filepath ) < 1 )
        {
            return null;
        }

        if ( empty( $params['mtime'] ) )
        {
            $params['mtime'] = filemtime( $filepath );
        }

        if ( empty( $params['method'] ) )
        {
            $params['method'] = self::DEFAULT_METHOD;
        }

        if ( ! empty( $params['width'] ) && $params['width'] < self::MIN_WIDTH )
        {
            $params['width'] = self::DEFAULT_WIDTH;
        }

        if ( ! empty( $params['height'] ) && $params['height'] < self::MIN_HEIGHT )
        {
            $params['height'] = self::DEFAULT_HEIGHT;
        }

        if ( empty( $params['width'] ) && empty( $params['height'] ) )
        {
            $params['width']    = self::DEFAULT_WIDTH;
            $params['height']   = self::DEFAULT_HEIGHT;
        }

        if ( empty( $params['width'] ) )
        {
            $sizes = getimagesize( $filepath );

            if ( empty( $sizes[0] ) || empty( $sizes[1] ) )
            {
                $params['width'] = self::DEFAULT_WIDTH;
            }
            else
            {
                $params['width'] = (int) ( $sizes[0] * $params['height'] / $sizes[1] );
            }
        }

        if ( empty( $params['height'] ) )
        {
            $sizes = getimagesize( $filepath );

            if ( empty( $sizes[0] ) || empty( $sizes[1] ) )
            {
                $params['height'] = self::DEFAULT_HEIGHT;
            }
            else
            {
                $params['height'] = (int) ( $sizes[1] * $params['width'] / $sizes[0] );
            }
        }

        if ( empty( $params['bgColor'] )  )
        {
            $params['bgColor'] = self::DEFAULT_BGCOLOR;
        }

        return $uri . '/' . $params['method'] .
                      '/' . ( empty( $params['crop'] ) ? '' :
                                $params['cropLeft'] . 'x' .
                                $params['cropTop'] . '-' .
                                $params['cropWidth'] . 'x' .
                                $params['cropHeight'] . '-' ) .
                            $params['width'] .
                      'x' . $params['height'] .
                      '/' . $params['bgColor'] .
                      '/' . ( empty( $params['filters'] ) ? 'none' :
                                implode( '-', (array) $params['filters'] ) ) .
                      '/' . $params['mtime'] .
                      '/' . $name;
    }

    /**
     * Invokable helper
     *
     * @param string $pathname
     * @param array|\Traversable $params
     * @return string
     */
    public function __invoke( $pathname, $params = array() )
    {
        return $this->getThumbnailUri( $pathname, $params );
    }

}
