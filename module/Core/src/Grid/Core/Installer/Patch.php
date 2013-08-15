<?php

namespace Grid\Core\Installer;

use Grid\Installer\Exception;
use Grid\Installer\AbstractPatch;

/**
 * Patch
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 *
 * @method \PDO getDb()
 */
class Patch extends AbstractPatch
{

    /**
     * @const int
     */
    const DEVELOPER_GROUP = 1;

    /**
     * @const int
     */
    const SITE_OWNER_GROUP = 2;

    /**
     * Uploads dirs to generate upon installation
     *
     * @var array
     */
    protected $uploadsDirs = array(
        'pages',
        'pages/images',
        'pages/documents',
        'settings',
        'customize',
        'users',
    );

    /**
     * Uploads files to copy
     *
     * @var array
     */
    protected $uploadsFiles = array(
        'customize/extra.css' => './uploads/central/customize/extra.css',
    );

    /**
     * Run after patching
     *
     * @param   string  $from
     * @param   string  $to
     * @return  void
     */
    public function afterPatch( $from, $to )
    {
        if ( $this->isZeroVersion( $from ) )
        {
            $developer = $this->selectFromTable( 'user', 'id', array(
                'groupId' => static::DEVELOPER_GROUP,
            ) );

            if ( ! $developer )
            {
                $developer = $this->insertDeveloper();
            }

            $platformOwner = $this->selectFromTable( 'user', 'id', array(
                'groupId' => static::SITE_OWNER_GROUP,
            ) );

            if ( ! $platformOwner )
            {
                $platformOwner = $this->insertPlatformOwner();
            }

            $content = $this->selectFromTable( 'paragraph', 'id', array(
                'type' => 'content',
            ) );

            if ( ! $content )
            {
                $content = $this->insertDefaultParagraph( 'content' );
            }

            $menu = $this->selectFromTable( 'menu', 'id' );

            if ( ! $menu )
            {
                $menu = $this->insertDefaultMenu( $content );
            }

            $layout = $this->selectFromTable( 'paragraph', 'id', array(
                'type' => 'layout',
            ) );

            if ( ! $layout )
            {
                $layout = $this->insertDefaultParagraph( 'layout' );
            }

            $subDomain = $this->selectFromTable( 'subdomain', 'id', array(
                'subdomain' => '',
            ) );

            if ( ! $subDomain )
            {
                $subDomain = $this->insertDefaultSubDomain( $layout, $content );
            }

            $schema = $this->getPatchData()
                           ->get( 'db', 'schema' );

            if ( is_array( $schema ) )
            {
                $schema = reset( $schema );
            }

            if ( ! empty( $schema ) )
            {
                foreach ( $this->uploadsDirs as $uploadsDir )
                {
                    @ mkdir(
                        implode( DIRECTORY_SEPARATOR, array(
                            '.',
                            'public',
                            'uploads',
                            $schema,
                            str_replace( '/', DIRECTORY_SEPARATOR, $uploadsDir )
                        ) ),
                        0777,
                        true
                    );
                }

                foreach ( $this->uploadsFiles as $uploadsFile => $copyFrom )
                {
                    @ copy(
                        str_replace( '/', DIRECTORY_SEPARATOR, $copyFrom ),
                        implode( DIRECTORY_SEPARATOR, array(
                            '.',
                            'public',
                            'uploads',
                            $schema,
                            str_replace( '/', DIRECTORY_SEPARATOR, $uploadsFile )
                        ) )
                    );
                }
            }
        }

        $this->mergePackagesConfig();
    }

    /**
     * Insert developer user
     *
     * @return  int|null
     */
    protected function insertDeveloper()
    {
        $data   = $this->getPatchData();
        $create = $data->get(
            'gridguyz-core',
            'developer',
            'Do you want to create a developer user? (y/n)',
            'n',
            array( 'y', 'n', 'yes', 'no', 't', 'f', 'true', 'false', '1', '0' )
        );

        if ( in_array( strtolower( $create ),
             array( 'n', 'no', 'f', 'false', '0', '' ) ) )
        {
            return null;
        }

        $email = $data->get(
            'gridguyz-core',
            'developer-email',
            'Type the developer\'s email (must be valid email)',
            null,
            '/^[A-Z0-9\._%\+-]+@[A-Z0-9\.-]+\.[A-Z]{2,4}$/i',
            3
        );

        $displayName = $data->get(
            'gridguyz-core',
            'developer-displayName',
            'Type the developer\'s display name',
            strstr( $email, '@', true )
        );

        $password = $data->get(
            'gridguyz-core',
            'developer-password',
            'Type the developer\'s password',
            $this->createPasswordSalt( 6 ),
            true
        );

        return $this->insertIntoTable(
            'user',
            array(
                'email'         => $email,
                'displayName'   => $displayName,
                'passwordHash'  => $this->createPasswordHash( $password ),
                'groupId'       => static::DEVELOPER_GROUP,
                'state'         => 'active',
                'confirmed'     => 't',
                'locale'        => 'en',
            ),
            true
        );
    }

    /**
     * Insert platform-owner user
     *
     * @return  int
     */
    protected function insertPlatformOwner()
    {
        $data  = $this->getPatchData();
        $email = $data->get(
            'gridguyz-core',
            'platformOwner-email',
            'Type the platform owner\'s email (must be valid email)',
            null,
            '/^[A-Z0-9\._%\+-]+@[A-Z0-9\.-]+\.[A-Z]{2,4}$/i',
            3
        );

        $displayName = $data->get(
            'gridguyz-core',
            'platformOwner-displayName',
            'Type the platform owner\'s display name',
            strstr( $email, '@', true )
        );

        $password = $data->get(
            'gridguyz-core',
            'platformOwner-password',
            'Type the platform owner\'s password',
            $this->createPasswordSalt( 6 ),
            true
        );

        return $this->insertIntoTable(
            'user',
            array(
                'email'         => $email,
                'displayName'   => $displayName,
                'passwordHash'  => $this->createPasswordHash( $password ),
                'groupId'       => static::SITE_OWNER_GROUP,
                'state'         => 'active',
                'confirmed'     => 't',
                'locale'        => 'en',
            ),
            true
        );
    }

    /**
     * Create password hash
     *
     * @param   string   $password
     * @return  string
     */
    protected function createPasswordHash( $password )
    {
        if ( function_exists( 'password_hash' ) )
        {
            return password_hash( $password, PASSWORD_DEFAULT );
        }

        if ( ! defined( 'CRYPT_BLOWFISH' ) )
        {
            throw new Exception\RuntimeException( sprintf(
                '%s: CRYPT_BLOWFISH algorithm must be enabled',
                __METHOD__
            ) );
        }

        return crypt(
            $password,
            ( version_compare( PHP_VERSION, '5.3.7' ) >= 0 ? '$2y' : '$2a' ) .
            '$10$' . $this->createPasswordSalt() . '$'
        );
    }

    /**
     * Create password-salt
     *
     * @param   int     $length
     * @return  string
     */
    private function createPasswordSalt( $length = 22 )
    {
        static $chars = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

        if ( function_exists( 'openssl_random_pseudo_bytes' ) &&
             ( version_compare( PHP_VERSION, '5.3.4' ) >= 0 ||
               strtoupper( substr( PHP_OS, 0, 3 ) ) !== 'WIN' ) )
        {
            $bytes = openssl_random_pseudo_bytes( $length, $usable );

            if ( true !== $usable )
            {
                $bytes = null;
            }
        }

        if ( empty( $bytes ) &&
             function_exists( 'mcrypt_create_iv' ) &&
             ( version_compare( PHP_VERSION, '5.3.7' ) >= 0 ||
               strtoupper( substr( PHP_OS, 0, 3 ) ) !== 'WIN' ) )
        {
            $bytes = mcrypt_create_iv( $length, MCRYPT_DEV_URANDOM );

            if ( empty( $bytes ) || strlen( $bytes ) < $length )
            {
                $bytes = null;
            }
        }

        if ( empty( $bytes ) )
        {
            $bytes = '';

            for ( $i = 0; $i < $length; ++$i )
            {
                $bytes .= chr( mt_rand( 0, 255 ) );
            }
        }

        $pos  = 0;
        $salt = '';
        $clen = strlen( $chars );

        for ( $i = 0; $i < $length; ++$i )
        {
            $pos = ( $pos + ord( $bytes[$i] ) ) % $clen;
            $salt .= $chars[$pos];
        }

        return $salt;
    }

    /**
     * Insert default paragraph: content / layout
     *
     * @return  int
     */
    protected function insertDefaultParagraph( $type )
    {
        $data = $this->getPatchData();
        $key  = $type . 'Id';

        if ( $data->has( 'gridguyz-core', $key ) )
        {
            $id = $data->get( 'gridguyz-core', $key );
        }
        else
        {
            $choices = array();
            $first   = null;
            $rows    = $this->selectRowsFromTable(
                array( '_central', 'paragraph' ),
                array( 'id', 'name' ),
                array( 'type' => $type ),
                array( 'id'   => 'ASC' )
            );

            foreach ( $rows as $row )
            {
                if ( empty( $first ) )
                {
                    $first = $row->id;
                }

                $choices[$row->id] = $row->name;
            }

            $data->printChoices( "Available {$type}s:", $choices );

            $id = $data->get(
                'gridguyz-core',
                $key,
                "Type the default $type's id",
                $first,
                array_keys( $choices )
            );
        }

        $query = $this->query(
            'SELECT "paragraph_clone"( :schema, :id ) AS "result"',
            array(
                'schema'    => '_central',
                'id'        => $id,
            )
        );

        while ( $row = $query->fetchObject() )
        {
            return $row->result;
        }

        return null;
    }

    /**
     * Insert default menu
     *
     * @param   int $content
     * @return  int
     */
    protected function insertDefaultMenu( $content )
    {
        $root = $this->insertIntoTable(
            'menu',
            array(
                'type'  => 'container',
                'left'  => 1,
                'right' => 4,
            ),
            true
        );

        $this->insertIntoTable(
            'menu_label',
            array(
                'menuId'    => $root,
                'locale'    => 'en',
                'label'     => 'Default menu',
            )
        );

        $menuContent = $this->insertIntoTable(
            'menu',
            array(
                'type'  => 'content',
                'left'  => 2,
                'right' => 3,
            ),
            true
        );

        $this->insertIntoTable(
            'menu_label',
            array(
                'menuId'    => $menuContent,
                'locale'    => 'en',
                'label'     => 'Home',
            )
        );

        $this->insertIntoTable(
            'menu_property',
            array(
                'menuId'    => $menuContent,
                'name'      => 'contentId',
                'value'     => $content,
            )
        );

        return $root;
    }

    /**
     * Insert default sub-domain
     *
     * @param   int $layout
     * @param   int $content
     * @return  int
     */
    protected function insertDefaultSubDomain( $layout, $content )
    {
        return $this->insertIntoTable(
            'subdomain',
            array(
                'subdomain'         => '',
                'locale'            => 'en',
                'defaultLayoutId'   => $layout,
                'defaultContentId'  => $content,
            )
        );
    }

    /**
     * Merge packages config
     *
     * @return  void
     */
    protected function mergePackagesConfig()
    {
        $installer  = $this->getInstaller();
        $config     = 'packages.local';
        $file       = $installer->getConfigFile( $config );

        if ( is_file( $file . '.dist' ) )
        {
            if ( ! is_file( $file ) )
            {
                @ copy( $file . '.dist', $file );
            }
            else
            {
                $data = $installer->getConfigData( $config );
                @ unlink( $file );
                @ copy( $file . '.dist', $file );
                $installer->clearConfigDataCache( $config );

                $data = $installer::merge(
                    $installer->getConfigData( $config ),
                    $data
                );

                if ( ! empty( $data['modules']['Grid\Core']['enabledPackages'] ) )
                {
                    $data['modules']['Grid\Core']['enabledPackages'] = (array) $data['modules']['Grid\Core']['enabledPackages'];

                    foreach ( $data['modules']['Grid\Core']['enabledPackages'] as $key => $packages )
                    {
                        if ( ! is_array( $packages ) )
                        {
                            $packages = (array) $packages;
                        }

                        $data['modules']['Grid\Core']['enabledPackages'][$key] = array_unique( $packages );
                    }
                }

                $installer->setConfigData( $config, $data );
            }
        }
    }
}
