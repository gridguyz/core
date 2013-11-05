<?php

namespace Grid\User\Model\User\Settings;

use Zork\Db\Sql\Sql;
use Zork\Db\Sql\Predicate\NotIn;
use Zend\Stdlib\ArrayUtils;
use Zork\Stdlib\OptionsTrait;
use Zork\Model\Exception;
use Zork\Model\DbAdapterAwareTrait;
use Zork\Model\DbAdapterAwareInterface;
use Zork\Model\MapperAwareInterface;
use Zork\Model\Mapper\ReadOnlyMapperInterface;
use Zork\Model\Mapper\ReadWriteMapperInterface;
use Zork\Model\Mapper\DbAware\DbSchemaAwareTrait;
use Zork\Model\Mapper\DbAware\DbSchemaAwareInterface;

/**
 * Core_Model_Mapper_ReadOnlyAbstract
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Mapper implements DbAdapterAwareInterface,
                        DbSchemaAwareInterface,
                        ReadOnlyMapperInterface,
                        ReadWriteMapperInterface
{

    use OptionsTrait,
        DbAdapterAwareTrait,
        DbSchemaAwareTrait;

    /**
     * @var string
     */
    const DEFAULT_TABLE         = '*';

    /**
     * Table name used in all queries
     *
     * @var string
     */
    protected static $tableName = 'user_settings';

    /**
     * @var \User\Model\User\Settings\Structure
     */
    protected $structurePrototype;

    /**
     * Get the table-name
     *
     * @return string
     */
    protected function getTableName()
    {
        if ( empty( static::$tableName ) )
        {
            throw new Zork_Model_Mapper_LogicException(
                '$tableName not implemented'
            );
        }

        return $this->getTableInSchema( static::$tableName );
    }

    /**
     * @return \User\Model\User\Settings\Structure
     */
    public function getStructurePrototype()
    {
        return $this->structurePrototype;
    }

    /**
     * @param \User\Model\User\Settings\Structure $structurePrototype
     * @return \User\Model\User\Settings\Mapper
     */
    public function setStructurePrototype( Structure $structurePrototype = null )
    {
        if ( $structurePrototype instanceof MapperAwareInterface )
        {
            $structurePrototype->setMapper( $this );
        }

        $this->structurePrototype = $structurePrototype;
        return $this;
    }

    /**
     * Constructor
     *
     * @param \User\Model\User\Settings\Structure $userSettingsStructurePrototype
     */
    public function __construct( Structure $userSettingsStructurePrototype = null )
    {
        $this->setStructurePrototype( $userSettingsStructurePrototype ?: new Structure );
    }

    /**
     * Create structure from plain data
     *
     * @param array $data
     * @return \User\Model\User\Settings\Structure
     */
    protected function createStructure( array $data )
    {
        $structure = clone $this->structurePrototype;

        if ( $structure instanceof MapperAwareInterface )
        {
            $structure->setMapper( $this );
        }

        return $structure->setOptions( $data );
    }

    /**
     * Sql-object
     *
     * @var \Zend\Db\Sql\Sql
     */
    private $sql;

    /**
     * Get a Zend\Db\Sql\Sql object
     *
     * @param null|string|TableIdentifier $table default: self::DEFAULT_TABLE
     * @return \Zend\Db\Sql\Sql
     */
    protected function sql( $table = self::DEFAULT_TABLE )
    {
        if ( self::DEFAULT_TABLE === $table )
        {
            if ( null === $this->sql )
            {
                $this->sql = new Sql(
                    $this->getDbAdapter(),
                    $this->getTableName()
                );
            }

            return $this->sql;
        }

        return new Sql(
            $this->getDbAdapter(),
            $table
        );
    }

    /**
     * Find a structure
     *
     * @param int|array $primaryKeys $userId or array( $userId, $section )
     * @param string $section [optional]
     * @return \User\Model\User\Settings\Structure
     */
    public function find( $primaryKeys )
    {
        $primaryKeys = is_array( $primaryKeys )
                     ? $primaryKeys : func_get_args();

        if ( count( $primaryKeys ) < 2 )
        {
            throw new Exception\LogicException(
                'userId & section is required to find a user-settings structure'
            );
        }

        list( $userId, $section ) = $primaryKeys;

        $data = array(
            'userId'    => (int) $userId,
            'section'   => (string) $section,
            'settings'  => array(),
        );

        $select = $this->sql()
                       ->select()
                       ->columns( array(
                           'key', 'value'
                       ) )
                       ->where( array(
                           'userId'     => $userId,
                           'section'    => $section,
                       ) );

        $result = $this->sql()
                       ->prepareStatementForSqlObject( $select )
                       ->execute();

        foreach ( $result as $row )
        {
            $data['settings'][$row['key']] = $row['value'];
        }

        return $this->createStructure( $data );
    }

    /**
     * Create structure from plain data
     *
     * @param array|\Traversable $data
     * @return \User\Model\User\Settings\Structure
     */
    public function create( $data )
    {
        $data = ArrayUtils::iteratorToArray( $data );
        return $this->createStructure( $data );
    }

    /**
     * Save a structure
     *
     * @param array|\User\Model\User\Settings\Structure $structure
     * @return int
     */
    public function save( & $structure )
    {
        if ( $structure instanceof Structure )
        {
            $userId     = $structure->userId;
            $section    = $structure->section;
            $settings   = $structure->settings;
        }
        else
        {
            $data = (array) $structure;

            if ( ArrayUtils::isHashTable( $data ) )
            {
                $userId     = $data['userId'];
                $section    = $data['section'];
                $settings   = $data['settings'];
            }
            else
            {
                list( $userId,
                      $section,
                      $settings ) = $data;
            }
        }

        $rows       = 0;
        $savedKeys  = array();
        $sql        = $this->sql();

        foreach ( $settings as $key => $value )
        {
            $update = $sql->update()
                          ->set( array(
                              'value' => $value
                          ) )
                          ->where( array(
                              'userId'  => $userId,
                              'section' => $section,
                              'key'     => $key,
                          ) );

            $affected = $this->sql()
                             ->prepareStatementForSqlObject( $update )
                             ->execute()
                             ->getAffectedRows();

            if ( $affected )
            {
                $rows += $affected;
            }
            else
            {
                $insert = $sql->insert()
                              ->values( array(
                                 'userId'  => $userId,
                                 'section' => $section,
                                 'key'     => $key,
                                 'value'   => $value,
                             ) );

                $rows += $this->sql()
                              ->prepareStatementForSqlObject( $insert )
                              ->execute()
                              ->getAffectedRows();
            }

            $savedKeys[] = $key;
        }

        $deleteWhere = array(
            'userId'    => $userId,
            'section'   => $section,
        );

        if ( ! empty( $savedKeys ) )
        {
            $deleteWhere[] = new NotIn( 'key', $savedKeys );
        }

        $delete = $this->sql()
                       ->delete()
                       ->where( $deleteWhere );

        $rows += $this->sql()
                      ->prepareStatementForSqlObject( $delete )
                      ->execute()
                      ->getAffectedRows();

        return $rows;
    }

    /**
     * Remove a structure
     *
     * @param int|string|array|\User\Model\User\Settings\Structure $structureOrPrimaryKeys
     * @return int
     */
    public function delete( $structureOrPrimaryKeys )
    {
        if ( is_array( $structureOrPrimaryKeys ) )
        {
            if ( isset( $structureOrPrimaryKeys['userId'] ) &&
                 isset( $structureOrPrimaryKeys['section'] ) &&
                 isset( $structureOrPrimaryKeys['settings'] ) )
            {
                $userId     = $structureOrPrimaryKeys['userId'];
                $section    = $structureOrPrimaryKeys['section'];
            }
            else
            {
                list( $userId, $section ) = $structureOrPrimaryKeys;
            }
        }
        else
        {
            $userId     = $structureOrPrimaryKeys->userId;
            $section    = $structureOrPrimaryKeys->section;
        }

        $delete = $this->sql()
                       ->delete()
                       ->where( array(
                           'userId'     => $userId,
                           'section'    => $section,
                       ) );

        $result = $this->sql()
                       ->prepareStatementForSqlObject( $delete )
                       ->execute();

        return $result->getAffectedRows();
    }

}
