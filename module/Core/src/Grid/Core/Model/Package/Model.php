<?php

namespace Grid\Core\Model\Package;

use Zend\Json\Json;
use Zend\Stdlib\ArrayUtils;
use Zork\Model\MapperAwareTrait;
use Zork\Model\MapperAwareInterface;

/**
 * Model
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Model implements MapperAwareInterface
{

    use MapperAwareTrait;

    /**
     * @const string
     */
    const PACKAGE_FILE = './composer.json';

    /**
     * @const string
     */
    const PACKAGE_FILE_BACKUP = './composer.json.backup';

    /**
     * @const string
     */
    const EXTRA_FILE = './data/update/extra.json';

    /**
     * @var array
     */
    protected static $packageData;

    /**
     * Construct model
     *
     * @param   \Grid\Core\Model\Package\Mapper $packageMapper
     */
    public function __construct( Mapper $packageMapper )
    {
        $this->setMapper( $packageMapper );
    }

    /**
     * Find package element by name
     *
     * @param   string  $name
     * @return  \Grid\Core\Model\Package\Structure
     */
    public function find( $name )
    {
        return $this->getMapper()
                    ->find( $name );
    }

    /**
     * Find package element by name
     *
     * @param   string|null $where
     * @param   bool|null   $order
     * @return  \Grid\Core\Model\Package\Structure
     */
    public function getPaginator( $where = null, $order = null )
    {
        return $this->getMapper()
                    ->getPaginator( $where, $order );
    }

    /**
     * Get categories
     *
     * @return  array
     */
    public function getCategories()
    {
        return $this->getMapper()
                    ->getCategories();
    }

    /**
     * Get enabled pattern count
     *
     * @return  int
     */
    public function getEnabledPatternCount()
    {
        return $this->getMapper()
                    ->getEnabledPatternCount();
    }

    /**
     * Can modify packages
     *
     * @return  bool
     */
    public function canModify()
    {
        return $this->getMapper()
                    ->canModify();
    }

    /**
     * Load json data
     *
     * @param   string  $file
     * @return  mixed|null
     */
    protected static function loadJsonData( $file )
    {
        if ( ! is_file( $file ) )
        {
            return null;
        }

        return Json::decode(
            @ file_get_contents( $file ),
            Json::TYPE_ARRAY
        );
    }

    /**
     * Load package data
     *
     * @return  mixed
     */
    protected static function loadPackageData()
    {
        if ( ! is_file( static::PACKAGE_FILE ) )
        {
            return null;
        }

        if ( null === static::$packageData )
        {
            static::$packageData = static::loadJsonData( static::PACKAGE_FILE );
        }

        return static::$packageData;
    }

    /**
     * Save json data
     *
     * @param   mixed   $data
     * @return  bool|int
     */
    protected static function saveJsonData( $file, $data )
    {
        return @ file_put_contents(
            $file,
            json_encode( $data, JSON_PRETTY_PRINT )
        );
    }

    /**
     * Save package data
     *
     * @param   mixed   $data
     * @return  bool|int
     */
    protected static function savePackageData( $data )
    {
        if ( ! is_file( static::PACKAGE_FILE ) )
        {
            return false;
        }

        if ( is_file( static::PACKAGE_FILE_BACKUP ) )
        {
            @ unlink( static::PACKAGE_FILE_BACKUP );
        }

        @ copy( static::PACKAGE_FILE, static::PACKAGE_FILE_BACKUP );

        $result = static::saveJsonData( static::PACKAGE_FILE, $data );

        if ( $result )
        {
            static::$packageData = $data;
        }
        else if ( is_file( static::PACKAGE_FILE_BACKUP ) )
        {
            @ unlink( static::PACKAGE_FILE );
            @ copy( static::PACKAGE_FILE_BACKUP, static::PACKAGE_FILE );
        }

        return $result;
    }

    /**
     * Merge json data
     *
     * @param   mixed   $data
     * @return  bool|int
     */
    protected static function mergeJsonData( $file, $data )
    {
        return static::saveJsonData( $file, ArrayUtils::merge(
            (array) static::loadJsonData( $file ),
            $data
        ) );
    }

    /**
     * Install a package
     *
     * @param   \Grid\Core\Model\Package\Structure  $package
     * @param   array                               $extra
     * @return  bool|int
     */
    public function install( Structure $package, array $extra = array() )
    {
        $data = static::loadPackageData();

        if ( empty( $data['require'] ) )
        {
            return false;
        }

        if ( ! empty( $extra ) )
        {
            if ( ! static::mergeJsonData( static::EXTRA_FILE, $extra ) )
            {
                return false;
            }

            $data['extra']['patch-data-file'] = static::EXTRA_FILE;
        }

        $data['require'][$package->name] = '*';
        return static::savePackageData( $data );
    }

    /**
     * Remove a package
     *
     * @param   \Grid\Core\Model\Package\Structure  $package
     * @return  boolean
     */
    public function remove( Structure $package )
    {
        $data = static::loadPackageData();

        if ( ! isset( $data['require'][$package->name] ) )
        {
            return false;
        }

        unset( $data['require'][$package->name] );
        return static::savePackageData( $data );
    }

}
