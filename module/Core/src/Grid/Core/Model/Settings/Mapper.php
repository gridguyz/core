<?php

namespace Grid\Core\Model\Settings;

use Zork\Db\Sql\Sql;
use Zork\Db\SiteInfo;
use Zend\Stdlib\ArrayUtils;
use Zork\Db\SiteInfoAwareTrait;
use Zork\Db\SiteInfoAwareInterface;
use Zend\Db\Sql\Predicate\In;
use Zork\Db\Sql\Predicate\SimilarTo;
use Zend\Db\Sql\Predicate\PredicateSet;
use Zork\Model\MapperAwareInterface;
use Zork\Model\DbAdapterAwareTrait;
use Zork\Model\DbAdapterAwareInterface;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Zork\Model\Mapper\ReadWriteMapperInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zork\Model\Mapper\DbAware\DbSchemaAwareTrait;
use Zork\ServiceManager\ServiceLocatorAwareTrait;
use Zork\Model\Mapper\DbAware\DbSchemaAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * Mapper
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Mapper implements HydratorInterface,
                        SiteInfoAwareInterface,
                        DbSchemaAwareInterface,
                        DbAdapterAwareInterface,
                        ReadWriteMapperInterface,
                        ServiceLocatorAwareInterface
{

    use SiteInfoAwareTrait,
        DbSchemaAwareTrait,
        DbAdapterAwareTrait,
        ServiceLocatorAwareTrait;

    /**
     * @var \Zend\Db\Sql\Sql
     */
    protected $sql;

    /**
     * @var \Core\Model\Settings\Definitions
     */
    protected $definitions;

    /**
     * Structure prototype for the mapper
     *
     * @var \Core\Model\Settings\StructureFactory
     */
    protected $structureFactory;

    /**
     * @return \Core\Model\Settings\Definitions
     */
    public function getDefinitions()
    {
        return $this->definitions;
    }

    /**
     * @param \Core\Model\Settings\Definitions $definitions
     * @return \Core\Model\Settings\Mapper
     */
    public function setDefinitions( Definitions $definitions )
    {
        $this->definitions = $definitions;
        return $this;
    }

    /**
     * Get structure factory
     *
     * @return \Core\Model\Settings\Structure\Factory
     */
    public function getStructureFactory()
    {
        return $this->structureFactory;
    }

    /**
     * Set structure factory
     *
     * @param \Core\Model\Settings\Structure\Factory $structureFactory
     * @return \Core\Model\Settings\Mapper
     */
    public function setStructureFactory( $structureFactory )
    {
        $this->structureFactory = $structureFactory;
        return $this;
    }

    /**
     * Contructor
     *
     * @param \Core\Model\Settings\Definitions $definitions
     * @param \Zork\Db\SiteInfo $siteInfo
     * @param \Core\Model\Settings\StructureFactory $settingsStructureFactory
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     */
    public function __construct( Definitions                $definitions,
                                 SiteInfo                   $siteInfo,
                                 StructureFactory           $settingsStructureFactory,
                                 ServiceLocatorInterface    $serviceLocator )
    {
        $this->setDefinitions( $definitions )
             ->setSiteInfo( $siteInfo )
             ->setStructureFactory( $settingsStructureFactory )
             ->setServiceLocator( $serviceLocator );
    }

    /**
     * Extract values from an object
     *
     * @param  object $object
     * @return array
     */
    public function extract( $object )
    {
        $values = array();

        foreach ( $object->settings as $key => $value )
        {
            switch ( true )
            {
                case $value instanceof StructureAbstract:
                    $values[$key] = $this->extract( $value );
                    break;

                case method_exists( $object, $method = 'extract' . ucfirst( $key ) ):
                    $values[$key] = $object->$method( $value );
                    break;

                default:
                    $values[$key] = $value;
            }
        }

        return $values;
    }

    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  object $object
     * @return object
     */
    public function hydrate( array $data, $object )
    {
        $object->settings = $data;
        return $object;
    }

    /**
     * Create structure from plain data
     *
     * @param array $data
     * @return \Core\Model\Settings\StructureAbstract
     */
    protected function createStructure( array $data )
    {
        $structure = $this->getStructureFactory()
                          ->factory( $data );

        if ( $structure instanceof MapperAwareInterface )
        {
            $structure->setMapper( $this );
        }

        if ( $structure instanceof SiteInfoAwareInterface )
        {
            $structure->setSiteInfo( $this->getSiteInfo() );
        }

        if ( $structure instanceof ServiceLocatorAwareInterface )
        {
            $structure->setServiceLocator( $this->getServiceLocator() );
        }

        return $structure;
    }

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
                $this->getTableInSchema( 'settings' )
            );
        }

        return $this->sql;
    }

    /**
     * Create a structure
     *
     * @param array|\Traversable $data
     * @return \Core\Model\Settings\StructureAbstract
     */
    public function create( $data )
    {
        $data = ArrayUtils::iteratorToArray( $data );
        return $this->createStructure( $data );
    }

    /**
     * @param array $keyValues
     * @return array
     */
    protected function keysWhere( $keyValues )
    {
        $keys   = array();
        $hashes = array();

        foreach ( $keyValues as $key )
        {
            $keys[]   = $key;
            $hashes[] = addcslashes( $key, '_%|*+?{}()[]' ) . '.%';
        }

        return array(
            new PredicateSet(
                array(
                    new In( 'key', $keys ),
                    new SimilarTo( 'key', '(' . implode( '|', $hashes ) . ')' )
                ),
                PredicateSet::OP_OR
            ),
        );
    }

    /**
     * Find a structure
     *
     * @param string $section
     * @return \Core\Model\Settings\StructureAbstract
     */
    public function find( $section )
    {
        $def        = $this->getDefinitions();
        $keyNames   = $def->getKeyNames( $section );
        $fieldsets  = $def->getFieldsets( $section );
        $settings   = array();

        if ( empty( $keyNames ) && empty( $fieldsets ) )
        {
            return $this->createStructure( array(
                'section' => $section,
            ) );
        }

        if ( ! empty( $keyNames ) )
        {
            $select = $this->sql()
                           ->select()
                           ->where( $this->keysWhere(
                                array_keys( $keyNames )
                            ) );

            /* @var $result \Zend\Db\Adapter\Driver\ResultInterface */
            $result = $this->sql()
                           ->prepareStatementForSqlObject( $select )
                           ->execute();

            foreach ( $result as $row )
            {
                $key = $row['key'];

                if ( isset( $keyNames[$key] ) )
                {
                    $settings[$keyNames[$key]] = $row['value'];
                }
                else
                {
                    $parts = explode( '.', $key );
                    $index = array_pop( $parts );
                    $key   = implode( '.', $parts );

                    if ( isset( $keyNames[$key] ) )
                    {
                        if ( empty( $settings[$keyNames[$key]] ) )
                        {
                            $settings[$keyNames[$key]] = array();
                        }

                        $settings[$keyNames[$key]][$index] = $row['value'];
                    }
                }
            }
        }

        if ( ! empty( $fieldsets ) )
        {
            foreach ( $fieldsets as $name => $fieldset )
            {
                $fieldset = (string) $fieldset;
                $settings[$name] = $this->find( $fieldset );
            }
        }

        return $this->createStructure( array(
            'section'   => $section,
            'settings'  => $settings,
        ) );
    }

    /**
     * Save a structure
     *
     * @param array|\Core\Model\Settings\StructureAbstract $structure
     * @return int
     */
    public function save( & $structure )
    {
        if ( is_object( $structure ) )
        {
            $section    = $structure->section;
            $settings   = (array) $structure->settings;
        }
        else if ( is_array( $structure ) )
        {
            $section    = $structure['section'];
            $settings   = (array) $structure['settings'];
        }
        else
        {
            return 0;
        }

        $rows   = 0;
        $set    = array();
        $rem    = array();
        $def    = $this->getDefinitions();
        $elems  = $def->getElements( $section );
        $fields = $def->getFieldsets( $section );

        if ( ! empty( $fields ) && ! empty( $settings ) )
        {
            foreach ( $fields as $name => $fieldset )
            {
                if ( isset( $settings[$name] ) )
                {
                    if ( $settings[$name] instanceof StructureAbstract )
                    {
                        $fieldsetSettings = $settings[$name]->settings;
                    }
                    else if ( is_array( $settings[$name] ) )
                    {
                        if ( array_key_exists( 'settings', $settings[$name] ) )
                        {
                            $fieldsetSettings = $settings[$name]['settings'];
                        }
                        else
                        {
                            $fieldsetSettings = $settings[$name];
                        }
                    }
                    else
                    {
                        continue;
                    }

                    $subSection = array(
                        'section'   => $fieldset,
                        'settings'  => $fieldsetSettings,
                    );

                    $rows += $this->save( $subSection );
                }
            }
        }

        if ( ! empty( $elems ) && ! empty( $settings ) )
        {
            foreach ( $elems as $name => $spec )
            {
                if ( isset( $settings[$name] ) )
                {
                    if ( is_scalar( $settings[$name] ) )
                    {
                        $set[$spec['key']] = array(
                            'value' => $settings[$name],
                            'type'  => empty( $spec['type'] )
                                    ? 'ini' : $spec['type'],
                        );
                    }
                    else
                    {
                        foreach ( $settings[$name] as $index => $value )
                        {
                            $set[$spec['key'] . '.' . $index] = array(
                                'value' => $value,
                                'type'  => empty( $spec['type'] )
                                        ? 'ini' : $spec['type'],
                            );
                        }
                    }
                }
                else
                {
                    $rem[] = $spec['key'];
                }
            }
        }

        $connection = $this->getDbAdapter()
                           ->getDriver()
                           ->getConnection();

        $connection->beginTransaction();

        try
        {
            if ( ! empty( $rem ) )
            {
                $delete = $this->sql()
                               ->delete()
                               ->where( $this->keysWhere( $rem ) );

                $result = $this->sql()
                               ->prepareStatementForSqlObject( $delete )
                               ->execute();

                $rows += $result->getAffectedRows();
            }

            foreach ( $set as $key => $spec )
            {
                $type  = $spec['type'];
                $value = $spec['value'];

                $update = $this->sql()
                               ->update()
                               ->set( array(
                                   'value'  => $value,
                               ) )
                               ->where( array(
                                   'key'    => $key,
                                   'type'   => $type,
                               ) );

                $result = $this->sql()
                               ->prepareStatementForSqlObject( $update )
                               ->execute();

                $updateRows = $result->getAffectedRows();

                if ( $updateRows )
                {
                    $rows += $updateRows;
                }
                else
                {
                    $insert = $this->sql()
                                   ->insert()
                                   ->values( array(
                                       'key'    => $key,
                                       'type'   => $type,
                                       'value'  => $value,
                                   ) );

                    $result = $this->sql()
                                   ->prepareStatementForSqlObject( $insert )
                                   ->execute();

                    $rows += $result->getAffectedRows();
                }
            }

            $connection->commit();
        }
        catch ( \Exception $ex )
        {
            $connection->rollback();
            throw $ex;
        }

        return $rows;
    }

    /**
     * Remove a structure
     *
     * @param string|array|\Core\Model\Settings\StructureAbstract $structureOrPrimaryKeys
     * @return int
     */
    public function delete( $structureOrPrimaryKeys )
    {
        if ( is_object( $structureOrPrimaryKeys ) )
        {
            $section = $structureOrPrimaryKeys->section;
        }
        else if ( is_array( $structureOrPrimaryKeys ) )
        {
            $section = $structureOrPrimaryKeys['section'];
        }
        else
        {
            $section = (string) $structureOrPrimaryKeys;
        }

        $rows = 0;
        $def  = $this->getDefinitions();
        $keys = $def->getKeys( $section );

        if ( ! empty( $keys ) )
        {
            $delete = $this->sql()
                           ->delete()
                           ->where( $this->keysWhere( $keys ) );

            $result = $this->sql()
                           ->prepareStatementForSqlObject( $delete )
                           ->execute();

            $rows += $result->getAffectedRows();
        }

        $fieldsets = $def->getFieldsets( $section );

        if ( ! empty( $fieldsets ) )
        {
            foreach ( $fieldsets as $fieldset )
            {
                $rows += $this->delete( $fieldset );
            }
        }

        return $rows;
    }

}
