<?php

namespace Grid\Menu\Model\Menu;

use Zend\Db\Sql;
use Zend\Stdlib\ArrayUtils;
use Zork\Db\Sql\Predicate\NotIn;
use Zork\Model\LocaleAwareTrait;
use Zork\Model\LocaleAwareInterface;
use Zork\Model\Mapper\DbAware\ReadWriteMapperAbstract;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * Mapper
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Mapper extends ReadWriteMapperAbstract
          implements LocaleAwareInterface,
                     ServiceLocatorAwareInterface
{

    use LocaleAwareTrait;

    /**
     * @const string
     */
    const MOVE_AFTER            = 'after';

    /**
     * @const string
     */
    const MOVE_APPEND           = 'append';

    /**
     * @const string
     */
    const MOVE_BEFORE           = 'before';

    /**
     * @const string
     */
    const MOVE_PREPEND          = 'prepend';

    /**
     * Table name used in all queries
     *
     * @var string
     */
    protected static $tableName = 'menu';

    /**
     * Table name used additinally in select queries
     *
     * @var string
     */
    protected static $labelTableName = 'menu_label';

    /**
     * Table name used additinally in select queries
     *
     * @var string
     */
    protected static $propertyTableName = 'menu_property';

    /**
     * Default column-conversion functions as types;
     * used in selected(), deselect()
     *
     * @var array
     */
    protected static $columns = array(
        'id'            => self::INT,
        'type'          => self::STR,
        'left'          => self::INT,
        'right'         => self::INT,
        'target'        => self::STR,
    );

    /**
     * Service-locator
     *
     * @var \Zend\ServiceManager\ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * Structure factory for the mapper
     *
     * @var \Menu\Model\Menu\StructureFactory
     */
    protected $structureFactory;

    /**
     * Get service-locator
     *
     * @return \Zend\ServiceManager\ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * Set service-locator
     *
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return \Menu\Model\Menu\Structure\ProxyBase
     */
    public function setServiceLocator( ServiceLocatorInterface $serviceLocator )
    {
        $this->serviceLocator = $serviceLocator;
        return $this;
    }

    /**
     * Get structure factory
     *
     * @return \Menu\Model\Menu\StructureFactory
     */
    public function getStructureFactory()
    {
        return $this->structureFactory;
    }

    /**
     * Set structure factory
     *
     * @param \Menu\Model\Menu\StructureFactory $structurePrototype
     * @return \Menu\Model\Menu\Mapper
     */
    public function setStructureFactory( $structureFactory )
    {
        $this->structureFactory = $structureFactory;
        return $this;
    }

    /**
     * Contructor
     *
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @param \Menu\Model\Menu\StructureFactory $paragraphStructureFactory
     * @param \Menu\Model\Menu\Structure\ProxyBase $paragraphStructurePrototype
     * @param string $locale
     */
    public function __construct( ServiceLocatorInterface $serviceLocator,
                                 StructureFactory $menuStructureFactory,
                                 Structure\ProxyBase $menuStructurePrototype = null,
                                 $locale = null )
    {
        parent::__construct( $menuStructurePrototype ?: new Structure\ProxyBase );

        $this->setServiceLocator( $serviceLocator )
             ->setStructureFactory( $menuStructureFactory )
             ->setLocale( $locale );
    }

    /**
     * Create structure from plain data
     *
     * @param array $data
     * @return \Menu\Model\Menu\StructureInterface
     */
    protected function createStructure( array $data )
    {
        if ( isset( $data['proxyData'] ) )
        {
            $proxyData = $data['proxyData'] ?: array();
            unset( $data['proxyData'] );
        }
        else
        {
            $proxyData = $data;
        }

        $proxyData['proxyBase'] = parent::createStructure( $data );
        $proxyData['type']      = $proxyData['proxyBase']->type;

        return $this->structureFactory
                    ->factory( $proxyData );
    }

    /**
     * Get select() default columns
     *
     * @return array
     */
    protected function getSelectColumns( $columns = null )
    {
        if ( null === $columns )
        {
            $label = true;
        }
        elseif ( ( $index = array_search( 'label', $columns ) ) )
        {
            $label = true;
            unset( $columns[$index] );
        }
        else
        {
            $label = false;
        }

        if ( null === $columns )
        {
            $proxyData = true;
        }
        elseif ( ( $index = array_search( 'proxyData', $columns ) ) )
        {
            $proxyData = true;
            unset( $columns[$index] );
        }
        else
        {
            $proxyData = false;
        }

        $columns = parent::getSelectColumns( $columns );

        if ( $label )
        {
            $platform = $this->getDbAdapter()
                             ->getPlatform();

            $columns['label'] = new Sql\Expression( '(' .
                $this->sql( $this->getTableInSchema(
                        static::$labelTableName
                     ) )
                     ->select()
                     ->columns( array( 'label' ) )
                     ->where( array(
                         new Sql\Predicate\Expression(
                             $platform->quoteIdentifierChain( array(
                                 static::$labelTableName, 'menuId'
                             ) ) .
                             ' = ' .
                             $platform->quoteIdentifierChain( array(
                                 static::$tableName, 'id'
                             ) )
                         )
                     ) )
                     ->order( array(
                         new Sql\Expression(
                             'CASE ? ' .
                                 'WHEN ? THEN 1 ' .
                                 'WHEN ? THEN 2 ' .
                                 'WHEN ? THEN 3 ' .
                                 'WHEN ? THEN 4 ' .
                                 'ELSE 5 ' .
                             'END ASC',
                             array(
                                 'locale',
                                 $this->getLocale(),
                                 $this->getPrimaryLanguage(),
                                 $this->getDefaultLocale(),
                                 'en',
                             ),
                             array(
                                 Sql\Expression::TYPE_IDENTIFIER,
                                 Sql\Expression::TYPE_VALUE,
                                 Sql\Expression::TYPE_VALUE,
                                 Sql\Expression::TYPE_VALUE,
                                 Sql\Expression::TYPE_VALUE,
                             )
                         ),
                     ) )
                     ->limit( 1 )
                     ->getSqlString( $platform ) .
            ')' );
        }

        if ( $proxyData )
        {
            $platform = $this->getDbAdapter()
                             ->getPlatform();

            $columns['proxyData'] = new Sql\Expression( '(' .
                $this->sql( $this->getTableInSchema(
                        static::$propertyTableName
                     ) )
                     ->select()
                     ->columns( array(
                         new Sql\Expression( 'TEXT( ARRAY_TO_JSON(
                             ARRAY_AGG( ? ORDER BY ? ASC )
                         ) )', array(
                             static::$propertyTableName,
                             'name',
                         ), array(
                             Sql\Expression::TYPE_IDENTIFIER,
                             Sql\Expression::TYPE_IDENTIFIER,
                         ) )
                     ) )
                     ->where( array(
                         new Sql\Predicate\Expression(
                             $platform->quoteIdentifierChain( array(
                                 static::$propertyTableName, 'menuId'
                             ) ) .
                             ' = ' .
                             $platform->quoteIdentifierChain( array(
                                 static::$tableName, 'id'
                             ) )
                         )
                     ) )
                     ->getSqlString( $platform ) .
            ')' );
        }

        return $columns;
    }

    /**
     * Parse proxy-data
     *
     * Like:
     * <pre>
     * &lt;struct&gt;
     * [{"name":"{key}","value":"{value}"}]
     * &nbsp;...
     * &lt;/struct&gt;
     * </pre>
     *
     * @param string $data
     * @return array
     */
    protected function parseProxyData( & $data )
    {
        if ( empty( $data ) )
        {
            return array();
        }

        $result = array();
        foreach ( json_decode( $data, true ) as $field )
        {
            if ( empty( $field['name'] ) )
            {
                continue;
            }

            $name   = (string) $field['name'];
            $parts  = explode( '.', $name, 2 );
            $value  = isset( $field['value'] ) ? $field['value'] : null;

            if ( count( $parts ) > 1 )
            {
                list( $name, $sub ) = $parts;

                if ( isset( $result[$name] ) )
                {
                    if ( ! is_array( $result[$name] ) )
                    {
                        $result[$name] = (array) $result[$name];
                    }
                }
                else
                {
                    $result[$name] = array();
                }

                $result[$name][$sub] = $value;
            }
            else
            {
                $result[$name] = $value;
            }
        }

        foreach ( $result as & $value )
        {
            if ( is_array( $value ) )
            {
                uksort( $value, 'strnatcmp' );
            }
        }

        return $result;
    }

    /**
     * Transforms the selected data into the structure object
     *
     * @param array $data
     * @return \Zork\Model\Structure\StructureAbstract
     */
    public function selected( array $data )
    {
        if ( isset( $data['proxyData'] ) && is_string( $data['proxyData'] ) )
        {
            $data['proxyData'] = $this->parseProxyData( $data['proxyData'] );
        }

        return parent::selected( $data );
    }

    /**
     * Find first (usually default)
     *
     * @return \Menu\Model\Menu\StructureInterface
     */
    public function findFirst()
    {
        return $this->findOne( array(), array(
            'left' => 'ASC',
        ) );
    }

    /**
     * Find render-list
     *
     * @param int|null $id
     * @return \Menu\Model\Menu\StructureInterface[]
     */
    public function findRenderList( $id = null )
    {
        $platform = $this->getDbAdapter()
                         ->getPlatform();

        $columns = $this->getSelectColumns();
        $columns['_depth'] = new Sql\Expression( '(' .
            $this->sql( null )
                 ->select( array( 'parent' => static::$tableName ) )
                 ->columns( array(
                     new Sql\Expression( 'COUNT(*)' )
                 ) )
                 ->where( array(
                     new Sql\Predicate\Expression(
                         $platform->quoteIdentifierChain( array( static::$tableName, 'left' ) ) .
                         ' BETWEEN ' . $platform->quoteIdentifierChain( array( 'parent', 'left' ) ) .
                             ' AND ' . $platform->quoteIdentifierChain( array( 'parent', 'right' ) )
                     ),
                 ) )
                 ->getSqlString( $platform ) .
        ')' );

        $select = $this->select( $columns )
                       ->order( 'left' );

        if ( null !== $id )
        {
            $select->join( array( 'parent' => self::$tableName ),
                           '( ' . self::$tableName . '.left BETWEEN parent.left AND parent.right ) ',
                           array() )
                   ->where( array( 'parent.id' => $id ) );
        }

        /* @var $result \Zend\Db\Adapter\Driver\ResultInterface */

        $return = array();
        $result = $this->sql()
                       ->prepareStatementForSqlObject( $select )
                       ->execute();

        foreach ( $result as $row )
        {
            $depth = (int) $row['_depth'];
            unset( $row['_depth'] );
            $return[] = array( $depth, $this->selected( $row ) );
        }

        return $return;
    }

    /**
     * Save a single property
     *
     * @param int $id
     * @param string $name
     * @param mixed $value
     * @return int
     */
    private function saveSingleProperty( $id, $name, $value )
    {
        $sql = $this->sql( $this->getTableInSchema(
            static::$propertyTableName
        ) );

        $update = $sql->update()
                      ->set( array(
                          'value'   => $value,
                      ) )
                      ->where( array(
                          'menuId'  => $id,
                          'name'    => $name,
                      ) );

        $affected = $sql->prepareStatementForSqlObject( $update )
                        ->execute()
                        ->getAffectedRows();

        if ( $affected < 1 )
        {
            $insert = $sql->insert()
                          ->values( array(
                              'menuId'  => $id,
                              'name'    => $name,
                              'value'   => $value,
                          ) );

            $affected = $sql->prepareStatementForSqlObject( $insert )
                            ->execute()
                            ->getAffectedRows();
        }

        return $affected;
    }

    /**
     * Save a property
     *
     * @param int $id
     * @param string $name
     * @param mixed $value
     * @return int
     */
    protected function saveProperty( $id, $name, $value )
    {
        $rows   = 0;
        $sql    = $this->sql( $this->getTableInSchema(
            static::$propertyTableName
        ) );

        $like = strtr( $name, array(
            '\\' => '\\\\',
            '%' => '\%',
            '_' => '\_',
        ) ) . '.%';

        if ( is_array( $value ) )
        {
            $nameLikeOrEq = new Sql\Predicate\PredicateSet( array(
                new Sql\Predicate\Like( 'name', $like ),
                new Sql\Predicate\Operator( 'name', Sql\Predicate\Operator::OP_EQ, $name )
            ), Sql\Predicate\PredicateSet::OP_OR );

            if ( empty( $value ) )
            {
                $delete = $sql->delete()
                              ->where( array(
                                  'menuId'  => $id,
                                  $nameLikeOrEq,
                              ) );

                $rows += $sql->prepareStatementForSqlObject( $delete )
                             ->execute()
                             ->getAffectedRows();
            }
            else
            {
                $keys = array();

                foreach ( $value as $idx => $val )
                {
                    $keys[] = $key = $name . '.' . $idx;
                    $rows += $this->saveSingleProperty( $id, $key, $val );
                }

                $delete = $sql->delete()
                              ->where( array(
                                  'menuId'  => $id,
                                  $nameLikeOrEq,
                                  new NotIn( 'name', $keys ),
                              ) );

                $rows += $sql->prepareStatementForSqlObject( $delete )
                             ->execute()
                             ->getAffectedRows();
            }
        }
        else
        {
            $rows += $this->saveSingleProperty( $id, $name, $value );

            $delete = $sql->delete()
                          ->where( array(
                              'menuId'  => $id,
                              new Sql\Predicate\Like( 'name', $like ),
                          ) );

            $rows += $sql->prepareStatementForSqlObject( $delete )
                         ->execute()
                         ->getAffectedRows();
        }

        return $rows;
    }

    /**
     * Save a label
     *
     * @param int $id
     * @param string $label
     * @return int
     */
    protected function saveLabel( $id, $label )
    {
        $locale = $this->getLocale();
        $sql    = $this->sql( $this->getTableInSchema(
            static::$labelTableName
        ) );

        $update = $sql->update()
                      ->set( array(
                          'label'   => $label,
                      ) )
                      ->where( array(
                          'menuId'  => $id,
                          'locale'  => $locale,
                      ) );

        $affected = $sql->prepareStatementForSqlObject( $update )
                        ->execute()
                        ->getAffectedRows();

        if ( $affected < 1 )
        {
            $insert = $sql->insert()
                          ->values( array(
                              'menuId'  => $id,
                              'locale'  => $locale,
                              'label'   => $label,
                          ) );

            $affected = $sql->prepareStatementForSqlObject( $insert )
                            ->execute()
                            ->getAffectedRows();
        }

        return $affected;
    }

    /**
     * Save element structure to datasource
     *
     * @param \Menu\Model\Menu\Structure\ProxyAbstract $structure
     * @return int Number of affected rows
     */
    public function save( & $structure )
    {
        if ( ! $structure instanceof Structure\ProxyAbstract ||
             empty( $structure->type ) )
        {
            return 0;
        }

        $data   = ArrayUtils::iteratorToArray( $structure->getBaseIterator() );
        $label  = empty( $data['label'] ) ? '' : $data['label'];
        unset( $data['label'] );

        $result = parent::save( $data );

        if ( $result > 0 )
        {
            if ( empty( $structure->id ) )
            {
                $structure->setOption( 'id', $id = $data['id'] );
            }
            else
            {
                $id = $structure->id;
            }

            $result += $this->saveLabel( $id, $label );

            foreach ( $structure->getPropertiesIterator() as $property => $value )
            {
                $result += $this->saveProperty( $id, $property, $value );
            }
        }

        return $result;
    }

    /**
     * @param $sourceNode int
     * @param $position string
     * @param $relatedNode int
     * @return boolean
     */
    public function moveNode( $sourceNode, $position, $relatedNode )
    {
        return $this->sql()
                    ->menu_move( (int)     $sourceNode,
                                 (string)  $position,
                                 (int)     $relatedNode );
    }

    /**
     * Interleave paragraph-nodes
     *
     * Update $updateNode's descendant menu-paragraphs
     * to be more like in $likeNode's.
     *
     * @param int $updateNode
     * @param int $likeNode
     * @return bool
     */
    public function interleaveParagraphs( $updateNode, $likeNode )
    {
        return $this->sql()
                    ->menu_interleave_paragraph( (int) $updateNode,
                                                 (int) $likeNode );
    }

}
