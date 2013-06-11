<?php

namespace Grid\Core\Model\Module;

use Zork\Db\Sql\Sql;
use Zend\Db\Sql\Predicate;
use Zend\Db\Adapter\Adapter;
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

    /**
     * @var Sql
     */
    private $sql = null;

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

        $select = $this->sql()
                       ->select()
                       ->columns( array(
                           'module',
                           'enabled',
                       ) );

        if ( $filter !== null )
        {
            $select->where( array(
                'enabled' => (bool) $filter
            ) );
        }

        return $this->create( array(
            'modules' => self::recieveModuleIndex(
                $this->sql()
                     ->prepareStatementForSqlObject( $select )
                     ->execute()
            )
        ) );
    }

    /**
     * @param \Grid\Core\Model\Module\Structure $structure
     * @return array
     */
    protected static function recieveEnabledModules( Structure $structure )
    {
        return array_keys( array_filter( $structure->modules ) );
    }

    /**
     * @param array|\Traversable $resultSet
     * @return array
     */
    protected static function recieveModuleIndex( $resultSet )
    {
        $moduleIndex = array();

        foreach ( $resultSet as $record )
        {
            $module     = (string) $record['module'];
            $enabled    = $record['enabled'];
            $moduleIndex[$module] = $enabled === true
                                 || $enabled === 't'
                                 || $enabled === 'true'
                                 || $enabled === '1'
                                 || $enabled === 1;
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
        if ( $structure instanceof Structure )
        {
            $modules = $structure->modules;
        }
        else if ( is_array( $structure ) )
        {
            $modules = $structure;
        }
        else
        {
            return 0;
        }

        $return = 0;

        foreach ( $modules as $module => $enabled )
        {
            $update = $this->sql()
                           ->update()
                           ->set( array(
                               'enabled' => $enabled ? 't' : 'f',
                           ) )
                           ->where( array(
                               'module' => $module,
                           ) );

            $result = $this->sql()
                           ->prepareStatementForSqlObject( $update )
                           ->execute();

            $updated = $result->getAffectedRows();

            if ( $updated )
            {
                $return += $updated;
            }
            else
            {
                $insert = $this->sql()
                               ->insert()
                               ->values( array(
                                   'module'     => $module,
                                   'enabled'    => $enabled ? 't' : 'f',
                               ) );


                $result = $this->sql()
                               ->prepareStatementForSqlObject( $insert )
                               ->execute();

                $return += $result->getAffectedRows();
            }
        }

        return $return;
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
        if ( $structure instanceof Structure )
        {
            $modules = $structure->modules;
        }
        else if ( is_array( $structure ) )
        {
            $modules = $structure;
        }
        else
        {
            return 0;
        }

        $update = $this->sql()
                       ->delete()
                       ->where( array(
                           new Predicate\In(
                               'module',
                               array_keys( $modules )
                           ),
                       ) );

        $result = $this->sql()
                       ->prepareStatementForSqlObject( $update )
                       ->execute();

        return $result->getAffectedRows();
    }

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
            foreach ( $structure->modules as & $enabled )
            {
                $enabled = false;
            }
            if ( isset( $data['modules'] ) )
            {
                foreach ( (array) $data['modules'] as $module )
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
