<?php

namespace Grid\Core\Model;

use FilesystemIterator;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Zend\Authentication\AuthenticationService;
use Zork\Rpc\CallableTrait;
use Zork\Rpc\CallableInterface;
use Zork\Iterator\SortIterator;
use Zork\Db\SiteInfo;
use Zork\Db\SiteInfoAwareTrait;
use Zork\Db\SiteInfoAwareInterface;
use Grid\User\Model\Permissions\Model as PermissionsModel;

/**
 * Grid\Core\Model\FileSystem
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class FileSystem implements CallableInterface,
                            SiteInfoAwareInterface
{

    use CallableTrait,
        SiteInfoAwareTrait;

    /**
     * @var string
     */
    const UPLOADS_URL           = 'uploads/';

    /**
     * @var string
     */
    const UPLOADS_PATH          = './public';

    /**
     * @var string
     */
    const UPLOADS_TEMP          = '/tmp';

    /**
     * @var string
     */
    const UPLOADS_ROOT          = '/pages';

    /**
     * Base url
     *
     * @var string
     */
    private $baseUrl;

    /**
     * Base dir
     *
     * @var string
     */
    private $baseDir;

    /**
     * Stored identity
     *
     * @var \User\Model\User\Structure
     */
    private $identity;

    /**
     * Stored permissions
     *
     * @var \User\Model\Permissions\Model
     */
    private $permissions;

    /**
     * Rights cache
     *
     * @var array
     */
    private $rightsCache       = array();


    /**
     * @return \User\Model\Permissions\Model
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * @param \User\Model\Permissions\Model $siteInfo
     * @return \Core\Model\FileSystem
     */
    public function setPermissions( PermissionsModel $permissionsModel )
    {
        $this->permissions = $permissionsModel;
        return $this;
    }

    /**
     * Constructor
     *
     * @param   SiteInfo                $siteInfo
     * @param   AuthenticationService   $auth
     * @param   PermissionsModel        $permissionsModel
     */
    public function __construct( SiteInfo               $siteInfo,
                                 AuthenticationService  $auth,
                                 PermissionsModel       $permissionsModel )
    {
        $this->setSiteInfo( $siteInfo )
             ->setPermissions( $permissionsModel );

        $schema = $siteInfo->getSchema();

        $this->baseUrl      = self::UPLOADS_URL . $schema . self::UPLOADS_ROOT;
        $this->baseDir      = realpath( self::UPLOADS_PATH );

        if ( $auth->hasIdentity() )
        {
            $this->identity = $auth->getIdentity();
        }
    }

    /**
     * Get real path
     *
     * @param string $path
     * @return string
     */
    protected function realPath( $path )
    {
        $path = trim( str_replace( '\\', '/', $path ), '/' );
        $path = str_replace( '/./', '/', $path );
        $path = preg_replace( '#[^/]+/\\.\\./#', '/', $path );
        $path = preg_replace( '#/?\\.\\.?/#', '', $path );
        $path = preg_replace( '#/+#', '/', $path );
        $path = trim( $path, '/' );

        if ( $path == '.' || $path == '..' )
        {
            $path = '';
        }

        return $path;
    }

    /**
     * Get valid filename
     *
     * @param string $name
     * @return string
     */
    public function validName( $name )
    {
        return strtr( preg_replace( '#^\.+#', '', $name ), array(
            '"'     => '\'',
            '<'     => '_',
            '>'     => '_',
            '*'     => '_',
            '?'     => '_',
            ':'     => '-',
            '|'     => '-',
            '\\'    => '-',
            '/'     => '-',
        ) );
    }

    /**
     * Get valid path
     *
     * @param string $path
     * @return string
     */
    public function validPath( $path )
    {
        return rtrim( preg_replace(
            array( '#^\.+#', '#/\.+#' ),
            array( '', '/' ),
            strtr( $path, array(
                '"'     => '\'',
                '<'     => '_',
                '>'     => '_',
                '*'     => '_',
                '?'     => '_',
                ':'     => '-',
                '|'     => '-',
                '\\'    => '/',
            ) )
        ), '/' );
    }

    /**
     * Get secure file
     *
     * @param string $path
     * @return string
     */
    public function secureFile( $path )
    {
        if ( preg_match( '/\.php$/i', $path ) )
        {
            return $path . 's';
        }

        return $path;
    }

    /**
     * Get mime-info
     *
     * @param string $full
     * @return object
     */
    protected function mime( $full )
    {
        static $finfo = null;

        $mime = null;

        if ( null === $mime &&
             function_exists( 'mime_content_type' ) &&
             ini_get( 'mime_magic.magicfile' ) )
        {
            $mime = mime_content_type( $full );
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
                $mime = finfo_file( $finfo, $full );
            }
        }

        return $mime;
    }

    /**
     * Get rights
     *
     * @param type $path
     * @return object
     */
    protected function rights( $path )
    {
        if ( isset( $this->rightsCache[$path] ) )
        {
            return $this->rightsCache[$path];
        }

        $name = basename( $path );

        if ( isset( $name[0] ) && $name[0] == '.' )
        {
            return $this->rightsCache[$path] = (object) array(
                'read'   => false,
                'write'  => false,
                'delete' => false,
            );
        }

        if ( empty( $path ) )
        {
            $res = 'uploads.*';
        }
        else
        {
            $res = 'uploads.' . trim( strtr( $path, array(
                '.' => '*',
                '/' => '.',
            ) ), '.' );
        }

        return $this->rightsCache[$path] = (object) array(
            'read'   => $this->permissions->isAllowed( $res, 'read' ),
            'write'  => $this->permissions->isAllowed( $res, 'write' ),
            'delete' => $this->permissions->isAllowed( $res, 'delete' ),
        );
    }

    /**
     * Get path-info
     *
     * @param string $path
     * @param string $base
     * @param string $full
     * @param string $type
     * @param bool $recursive
     * @return object
     */
    protected function info( $path, $base, $full, $type, $recursive = true )
    {
        $name = basename( $path );

        if ( isset( $name[0] ) && $name[0] == '.' )
        {
            return null;
        }

        $result = (object) array(
            'name' => $name,
            'path' => $path,
            'uri'  => '/' . $base,
            'type' => $type,
            'size' => filesize( $full ),
            'time' => filemtime( $full ),
        );

        $result->rights = $this->rights( $path );

        if ( ! $result->rights->read )
        {
            return null;
        }

        switch ( $type )
        {
            case 'dir':

                if ( $recursive )
                {
                    $result->entries = array();

                    $iterator = new FilesystemIterator( $full,
                        FilesystemIterator::KEY_AS_FILENAME     |
                        FilesystemIterator::CURRENT_AS_FILEINFO |
                        FilesystemIterator::SKIP_DOTS
                    );

                    $iterator = new SortIterator(
                        $iterator,
                        function ( $a, $b )
                        {
                            $aIsDir = $a->isDir();
                            $bIsDir = $b->isDir();
                            $aFname = $a->getFilename();
                            $bFname = $b->getFilename();

                            switch ( true )
                            {
                                case ! $aIsDir && $bIsDir:
                                    return 1;

                                case $aIsDir && ! $bIsDir:
                                    return -1;

                                default:
                                    return strnatcasecmp( $aFname, $bFname );
                            }
                        }
                    );

                    foreach ( $iterator as $name => $info )
                    {
                        $child = null;

                        if ( $name[0] != '.' )
                        {
                            if ( $info->isDir() )
                            {
                                $child = $this->info(
                                    ( empty( $path ) ? ''
                                        : $path . '/' ) . $name,
                                    $base . '/' . $name,
                                    $full . '/' . $name,
                                    'dir', false
                                );
                            }
                            else if ( $info->isFile() )
                            {
                                $child = $this->info(
                                    ( empty( $path ) ? ''
                                        : $path . '/' ) . $name,
                                    $base . '/' . $name,
                                    $full . '/' . $name,
                                    'file', false
                                );
                            }
                        }

                        if ( null !== $child )
                        {
                            $result->entries[] = $child;
                        }
                    }
                }

                break;

            case 'file':

                $result->mime = $this->mime( $full );

                break;
        }

        return $result;
    }

    /**
     * Get path-info
     *
     * @param string $path
     * @return object <pre>
     * string   $return->name
     * string   $return->path
     * string   $return->uri
     * string   $return->type           'dir'|'file'
     * string   $return->size           in bytes
     * int      $return->time           filemtime()
     * object   $return->rights
     * bool     $return->rights->read
     * bool     $return->rights->write
     * bool     $return->rights->delete
     * string   $return->rights->owner
     * string   $return->mime           only where type = 'file'
     * array    $return->entries        only where type = 'dir'
     * </pre>
     */
    public function pathInfo( $path )
    {
        if ( null === $this->identity )
        {
            return null;
        }

        $path = $this->realPath( $path );
        $base = $this->baseUrl . ( empty( $path ) ? '' : '/' . $path );
        $full = realpath( $this->baseDir . '/' . $base );

        switch ( true )
        {
            case is_file( $full ):
                return $this->info( $path, $base, $full, 'file' );
                break;

            case is_dir( $full ):
                return $this->info( $path, $base, $full, 'dir' );
                break;
        }

        return null;
    }

    /**
     * Create a dir
     *
     * @param string $path
     * @return bool|string failed / new
     */
    public function createDir( $path )
    {
        if ( empty( $path ) )
        {
            return false;
        }

        $path = $this->validPath( $path );
        $base = $this->baseUrl . '/' . $path;
        $full = $this->baseDir . '/' . $base;

        if ( ! is_dir( $full ) && ! is_file( $full ) )
        {
            $rights = $this->rights( $path );

            if ( $rights->write && @ mkdir( $full, 0777, true ) )
            {
                return $path;
            }
        }

        return false;
    }

    /**
     * Copy entire dir
     *
     * @param string $path
     * @param string $to
     * @return bool
     */
    protected function _copy( $path, $to )
    {
        if ( is_file( $to ) )
        {
            return false;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator( $path,
                RecursiveDirectoryIterator::CURRENT_AS_FILEINFO |
                RecursiveDirectoryIterator::SKIP_DOTS
            ),
            RecursiveIteratorIterator::SELF_FIRST
        );

        $result = is_dir( $to ) ? true : @ mkdir( $to );

        foreach ( $iterator as $info )
        {
            $toPath = $to . '/' . $iterator->getSubPathName();

            switch ( true )
            {
                case $info->isFile():
                    $result = $result && @ copy( $info->getPathname(), $toPath );
                    break;

                case $info->isDir():
                    $result = $result && @ mkdir( $toPath, 0777 );
                    break;
            }
        }

        return $result;
    }

    /**
     * Copy path (dir or file)
     *
     * @param string|array $path
     * @param string|array $to
     * @return bool|string failed / to
     */
    public function copy( $path, $to )
    {
        if ( null === $this->identity )
        {
            return false;
        }

        if ( is_array( $path ) || is_object( $path ) ||
             is_array( $to )   || is_object( $to ) )
        {
            if ( ( ! is_array( $path ) && ! is_object( $path ) ) ||
                 ( ! is_array( $to )   && ! is_object( $to ) ) )
            {
                return false;
            }

            $result = array();

            foreach ( $path as $key => $p )
            {
                foreach ( $to as $toKey => $t )
                {
                    if ( $key == $toKey )
                    {
                        $result[$key] = $this->copy( $p, $t );
                    }
                }
            }

            return $result;
        }

        $to     = $this->validPath( $this->realPath( $to ) );
        $path   = $this->realPath( $path );
        $base   = $this->baseUrl . ( empty( $path ) ? '' : '/' . $path );
        $full   = realpath( $this->baseDir . '/' . $base );
        $isFile = is_file( $full );
        $isDir  = is_dir( $full );

        if ( $isFile )
        {
            $to = $this->secureFile( $to );
        }

        $toBase = $this->baseUrl . '/' . $to;
        $toFull = $this->baseDir . '/' . $toBase;

        if ( ! empty( $to ) && ! is_dir( dirname( $toFull ) ) )
        {
            $ba = '/' . basename( $to );
            $to = $this->createDir( dirname( $to ) );

            if ( ! $to )
            {
                return false;
            }

            $to     = $to . $ba;
            $toBase = $this->baseUrl . '/' . $to;
            $toFull = $this->baseDir . '/' . $toBase;
        }

        if ( ( $isFile || $isDir ) && is_dir( dirname( $toFull ) ) )
        {
            if ( $this->rights( $path )->read &&
                 $this->rights( $to )->write )
            {
                switch ( true )
                {
                    case $isDir:
                        $result = $this->_copy( $full, $toFull );
                        break;

                    case $isFile:
                        $result = @ copy( $full, $toFull );
                        break;
                }

                if ( $result )
                {
                    return $to;
                }
            }
        }

        return false;
    }

    /**
     * Rename path (dir or file)
     *
     * @param string|array $path
     * @param string|array $to
     * @return bool|string failed / to
     */
    public function rename( $path, $to )
    {
        if ( null === $this->identity )
        {
            return false;
        }

        if ( is_array( $path ) || is_object( $path ) ||
             is_array( $to )   || is_object( $to ) )
        {
            if ( ( ! is_array( $path ) && ! is_object( $path ) ) ||
                 ( ! is_array( $to )   && ! is_object( $to ) ) )
            {
                return false;
            }

            $result = array();

            foreach ( $path as $key => $p )
            {
                foreach ( $to as $toKey => $t )
                {
                    if ( $key == $toKey )
                    {
                        $result[$key] = $this->rename( $p, $t );
                    }
                }
            }

            return $result;
        }

        $to     = $this->validPath( $this->realPath( $to ) );
        $path   = $this->realPath( $path );
        $base   = $this->baseUrl . ( empty( $path ) ? '' : '/' . $path );
        $full   = realpath( $this->baseDir . '/' . $base );

        if ( is_file( $full ) )
        {
            $to = $this->secureFile( $to );
        }

        $toBase = $this->baseUrl . '/' . $to;
        $toFull = $this->baseDir . '/' . $toBase;

        if ( ! empty( $to ) && ! is_dir( dirname( $toFull ) ) )
        {
            $ba = '/' . basename( $to );
            $to = $this->createDir( dirname( $to ) );

            if ( ! $to )
            {
                return false;
            }

            $to     = $to . $ba;
            $toBase = $this->baseUrl . '/' . $to;
            $toFull = $this->baseDir . '/' . $toBase;
        }

        if ( ( is_file( $full ) || is_dir( $full ) ) &&
               is_dir( dirname( $toFull ) ) )
        {
            $rights = $this->rights( $path );

            if ( $rights->read &&
                 $rights->delete &&
                 $this->rights( $to )->write )
            {
                if ( @ rename( $full, $toFull ) )
                {
                    return $to;
                }
            }
        }

        return false;
    }

    /**
     * Handle uploaded file
     *
     * @param string|array $temp
     * @param string|array $to
     * @return bool|string failed / to
     */
    public function uploaded( $temp, $to )
    {
        if ( null === $this->identity )
        {
            return false;
        }

        if ( is_array( $temp ) || is_object( $temp ) ||
             is_array( $to )   || is_object( $to ) )
        {
            if ( ( ! is_array( $temp ) && ! is_object( $temp ) ) ||
                 ( ! is_array( $to )   && ! is_object( $to ) ) )
            {
                return false;
            }

            $result = array();

            foreach ( $temp as $key => $p )
            {
                foreach ( $to as $toKey => $t )
                {
                    if ( $key == $toKey )
                    {
                        $result[$key] = $this->uploaded( $p, $t );
                    }
                }
            }

            return $result;
        }

        $to     = $this->secureFile( $this->validPath( $this->realPath( $to ) ) );
        $base   = $this->baseDir . self::UPLOADS_TEMP;
        $full   = realpath( $base . '/' . $temp );
        $toBase = $this->baseUrl . '/' . $to;
        $toFull = $this->baseDir . '/' . $toBase;

        if ( ! empty( $to ) && ! is_dir( dirname( $toFull ) ) )
        {
            $ba = '/' . basename( $to );
            $to = $this->createDir( dirname( $to ) );

            if ( ! $to )
            {
                return false;
            }

            $to     = $to . $ba;
            $toBase = $this->baseUrl . '/' . $to;
            $toFull = $this->baseDir . '/' . $toBase;
        }

        if ( is_file( $full ) && is_dir( dirname( $toFull ) ) )
        {
            if ( $this->rights( $to )->write )
            {
                if ( @ rename( $full, $toFull ) )
                {
                    return $to;
                }
            }
        }

        return false;
    }

    /**
     * Make dir
     *
     * @param string|array $path
     * @return bool|string failed / new
     */
    public function makeDir( $path )
    {
        if ( null === $this->identity )
        {
            return false;
        }

        if ( is_array( $path ) || is_object( $path ) )
        {
            $result = array();

            foreach ( $path as $key => $p )
            {
                $result[$key] = $this->makeDir( $p );
            }

            return $result;
        }

        $path = $this->realPath( $path );
        return $this->createDir( $path );
    }

    /**
     * Remove directory recursively
     *
     * @param string $dir
     * @return bool
     */
    protected function _rmdir( $dir )
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator( $dir,
                RecursiveDirectoryIterator::CURRENT_AS_FILEINFO |
                RecursiveDirectoryIterator::SKIP_DOTS
            ),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        $result = true;

        foreach ( $iterator as $info )
        {
            $path = $info->getPathname();

            switch ( true )
            {
                case $info->isFile():
                case $info->isLink():
                    $result = $result && @ unlink( $path );
                    break;

                case $info->isDir():
                    $result = $result && @ rmdir( $path );
                    break;
            }

        }

        return $result && @ rmdir( $dir );
    }

    /**
     * Remove file / dir
     *
     * @param string|array $file
     * @return bool
     */
    public function remove( $path )
    {
        if ( null === $this->identity )
        {
            return false;
        }

        if ( is_array( $path ) || is_object( $path ) )
        {
            $result = array();

            foreach ( $path as $key => $p )
            {
                $result[$key] = $this->remove( $p );
            }

            return $result;
        }

        $path = $this->realPath( $path );
        $base = $this->baseUrl . ( empty( $path ) ? '' : '/' . $path );
        $full = realpath( $this->baseDir . '/' . $base );

        if ( empty( $path ) ) // prevent delete root
        {
            return false;
        }

        $isDir  = is_dir( $full );
        $isFile = is_file( $full );

        if ( $isDir || $isFile )
        {
            $rights = $this->rights( $path );

            if ( $rights->delete )
            {
                switch ( true )
                {
                    case $isDir:
                        return $this->_rmdir( $full );

                    case $isFile:
                        return @ unlink( $full );
                }
            }
        }

        return false;
    }

}
