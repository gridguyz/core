<?php

namespace Grid\Core\Model\Module;

use Zork\Db\Sql\Sql;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Expression;
// use Zend\Db\Sql\Select;
// use Zend\Db\Sql\Expression as SqlExpression;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Zork\Model\Mapper\ReadOnlyMapperInterface;
use Zork\Model\Mapper\ReadWriteMapperInterface;
use Zork\Model\Structure\StructureAbstract;
use Zork\Model\DbAdapterAwareInterface;
use Zork\Model\DbAdapterAwareTrait;
use Zork\Model\Mapper\DbAware\DbSchemaAwareTrait;
use Zork\Model\Mapper\DbAware\DbSchemaAwareInterface;

/**
 * Mapper
 *
 * @author Sipi
 */
class Mapper implements HydratorInterface,
                        DbAdapterAwareInterface,
                        DbSchemaAwareInterface,
                        ReadOnlyMapperInterface,
                        ReadWriteMapperInterface
{
    use DbAdapterAwareTrait,
        DbSchemaAwareTrait;

    private $sql = null;

    private $findResult = array();
    /**
     * Get a Zend\Db\Sql\Sql object
     *
     * @return \Zend\Db\Sql\Sql
     */
    protected function sql()
    {
        if ( null === $this->sql )
        {
            $this->sql = new Sql(
                $this->getDbAdapter(),
                $this->getTableInSchema( 'module' )
            );
        }

        return $this->sql;
    }


    /**
     * Construct mapper
     *
     * @param \Core\Model\Module\Mapper $moduleMapper
     */
    public function __construct( Adapter $dbAdapter )
    {
        $this->setDbAdapter( $dbAdapter );
    }

    /**
     * Find a structure
     *
     * @param int|string|array|null $filter
     * @return \Zork\Model\Structure\StructureAbstract
     */
    public function find( $filter )
    {
        if ( $filter === null )
        {
            $key = '';
        }
        else
        {
            $key = (int) $filter;
        }

        if ( isset( $this->findResult[$key] ) )
        {
            return clone $this->findResult[$key];
        }

        $select = $this->sql()
                       ->select()
                       ->columns(array(
                            'module','enabled'
                       ) );

        if ( $filter !== null )
        {
            $select->where( array(
                'enabled' => (bool) $filter
            ) );
        }

        $this->findResult[$key] = $this->create( array(
            'modules' => self::recieveModuleIndex(
                $this->sql()
                     ->prepareStatementForSqlObject( $select )
                     ->execute()
            )
        ) );

        return clone $this->findResult[$key];
    }

    protected static function recieveEnabledModules(Structure $structure )
    {
        return array_keys( array_filter( $structure->modules ) );
    }

    protected static function recieveModuleIndex( $resultSet )
    {
        $moduleIndex = array();
        foreach($resultSet as $record)
        {
            $moduleIndex[$record['module']] = $record['enabled'];
        }
        return $moduleIndex;
    }
    /**
     * Save and update the statuses of available modules.
     *
     * @return integer
     *      The count of the affected (insaerted , updated) rows.
     */
    public function save( & $structure )
    {
        $list = self::recieveEnabledModules($structure);
        $oldStructureIndex = $this->find(null)->modules;
        $adapter = $this->getDbAdapter();
        $countResult = 0;
        $connection = $adapter->getDriver()->getConnection();
        $connection->beginTransaction();
        try
        {
            $update = $this->sql()
                           ->update()
                           ->set( array(
                                'enabled' => empty( $list )
                                           ? 0
                                           : new Expression(
                                               '? IN (' . implode( ', ', array_fill( 0, count( $list ), '?' ) ) . ')',
                                               array_merge( array( 'module' ), $list ),
                                               array_merge( array( Expression::TYPE_IDENTIFIER ), array_fill( 0, count( $list ), Expression::TYPE_VALUE ) )
                                           )
                           ) );

            $result = $this->sql()
                           ->prepareStatementForSqlObject( $update )
                           ->execute();

            $countResult += $result->getAffectedRows();

            foreach ( $list as $module )
            {
                if ( ! isset( $oldStructureIndex[$module] ) )
                {
                    $insert = $this->sql()
                                   ->insert()
                                   ->columns( array( 'module', 'enabled' ) )
                                   ->values( array( 'module' => $module, 'enabled' => 1 ) );

                    $result = $this->sql()
                           ->prepareStatementForSqlObject( $insert )
                           ->execute();

                    $countResult += $result->getAffectedRows();
                }
            }


            $connection->commit();
        }
        catch ( Exception $e )
        {
            $connection->rollBack();
            throw $e;
        }

        $this->findResult = array();

        return $countResult;
    }

    /**
     * Create a structure
     *
     * @param array|\Traversable $data
     * @return \Zork\Model\Structure\StructureAbstract
     */
    public function create( $data )
    {
        $structure = new Structure( $data );
        $structure->setMapper( $this );
        return $structure;
    }

    /**
     * Remove a structure
     *
     * @param int|string|array|\Zork\Model\Structure\StructureAbstract $structureOrPrimaryKeys
     * @return int
     */
    public function delete( $structure )
    {
        $update = $this->sql()
                       ->update()
                       ->set( array(
                           'enabled' => false
                       ) );

        $result = $this->sql()
                       ->prepareStatementForSqlObject( $update )
                       ->execute();

        $this->findResult = array();
        return $result->getAffectedRows();
    }

    /**
     * Implementation of hydration .
     */


    /**
     * Extract values from a structure
     *
     * @param object $structure
     * @return array
     */
    public function extract( $structure )
    {
        if ( $structure instanceof Structure )
        {
            return array(
                'modules' => self::recieveEnabledModules( $structure )
            );
        }
        elseif ( $structure instanceof StructureAbstract )
        {
            return $structure->toArray();
        }

        return (array) $structure;
    }

    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  object $structure
     * @return object
     */
    public function hydrate( array $data, $structure )
    {
        if ( $structure instanceof Structure )
        {
            foreach($structure->modules as &$enabled)
            {
                $enabled = false;
            }
            if(isset($data['modules']))
            {
                foreach((array)$data['modules'] as $module)
                {
                    $structure->modules[$module] = true;
                }
            }
        }
        elseif ( $structure instanceof StructureAbstract )
        {
            $structure->setOptions( $data );
        }
        else
        {
            foreach ( $data as $key => $value )
            {
                $structure->$key = $value;
            }
        }

        if ( $structure instanceof MapperAwareInterface )
        {
            $structure->setMapper( $this );
        }

        return $structure;
    }

}
