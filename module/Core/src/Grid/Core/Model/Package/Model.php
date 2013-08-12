<?php

namespace Grid\Core\Model\Package;

use Zend\Json\Json;
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
            static::$packageData = Json::decode(
                @ file_get_contents( static::PACKAGE_FILE ),
                Json::TYPE_ARRAY
            );
        }

        return static::$packageData;
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

        $result = @ file_put_contents(
            static::PACKAGE_FILE,
            json_encode( $data, JSON_HEX_TAG
                              | JSON_HEX_APOS
                              | JSON_HEX_QUOT
                              | JSON_HEX_AMP
                              | JSON_PRETTY_PRINT )
        );

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
     * Install a package
     *
     * @param   \Grid\Core\Model\Package\Structure  $package
     * @return  bool|int
     */
    public function install( Structure $package )
    {
        $data = static::loadPackageData();

        if ( empty( $data['require'] ) )
        {
            return false;
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
