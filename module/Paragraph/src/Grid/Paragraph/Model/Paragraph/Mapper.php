<?php

namespace Grid\Paragraph\Model\Paragraph;

use Zend\Db\Sql;
use Zend\Stdlib\ArrayUtils;
use Zork\Model\LocaleAwareTrait;
use Zork\Model\LocaleAwareInterface;
use Zork\Model\MapperAwareInterface;
use Zork\Db\Sql\Predicate\ExpressionIn;
use Zork\Db\Sql\Predicate\TypedParameters;
use Zork\Model\Structure\MapperAwareAbstract;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zork\Model\Mapper\DbAware\ReadWriteMapperAbstract;

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
     * @const string
     */
    const TAG_SEPARATOR         = "\n";

    /**
     * Table name used in all queries
     *
     * @var string
     */
    protected static $tableName = 'paragraph';

    /**
     * Table name used additinally in select queries
     *
     * @var string
     */
    protected static $propertyTableName = 'paragraph_property';

    /**
     * Table name used additinally in select queries
     *
     * @var string
     */
    protected static $tagTableName = 'tag';

    /**
     * Table name used additinally in select queries
     *
     * @var string
     */
    protected static $tagJoinTableName = 'paragraph_x_tag';

    /**
     * Default column-conversion functions as types;
     * used in selected(), deselect()
     *
     * @var array
     */
    protected static $columns = array(
        'id'            => self::INT,
        'type'          => self::STR,
        'name'          => self::STR,
        'rootId'        => self::INT,
        'left'          => self::INT,
        'right'         => self::INT,
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
     * @var \Paragraph\Model\Paragraph\StructureFactory
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
     * @return \Paragraph\Model\Paragraph\Structure\ProxyBase
     */
    public function setServiceLocator( ServiceLocatorInterface $serviceLocator )
    {
        $this->serviceLocator = $serviceLocator;
        return $this;
    }

    /**
     * Get structure factory
     *
     * @return \Paragraph\Model\Paragraph\StructureFactory
     */
    public function getStructureFactory()
    {
        return $this->structureFactory;
    }

    /**
     * Set structure factory
     *
     * @param \Paragraph\Model\Paragraph\StructureFactory $structurePrototype
     * @return \Paragraph\Model\Paragraph\Mapper
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
     * @param \Paragraph\Model\Paragraph\StructureFactory $paragraphStructureFactory
     * @param \Paragraph\Model\Paragraph\Structure\ProxyBase $paragraphStructurePrototype
     * @param string $locale
     */
    public function __construct( ServiceLocatorInterface $serviceLocator,
                                 StructureFactory $paragraphStructureFactory,
                                 Structure\ProxyBase $paragraphStructurePrototype = null,
                                 $locale = null )
    {
        parent::__construct( $paragraphStructurePrototype ?: new Structure\ProxyBase );

        $this->setServiceLocator( $serviceLocator )
             ->setStructureFactory( $paragraphStructureFactory )
             ->setLocale( $locale );
    }

    /**
     * Create structure from plain data
     *
     * @param array $data
     * @return \Paragraph\Model\Paragraph\StructureInterface
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

        if ( isset( $data['tagIds'] ) )
        {
            $proxyData['tagIds'] = $data['tagIds'];
            unset( $data['tagIds'] );
        }

        if ( isset( $data['localeTags'] ) )
        {
            $proxyData['localeTags'] = $data['localeTags'];
            unset( $data['localeTags'] );
        }

        $proxyData['proxyBase'] = parent::createStructure( $data );
        $proxyData['type']      = $proxyData['proxyBase']->type;

        return $this->structureFactory
                    ->factory( $proxyData );
    }

    /**
     * Create structure from plain data
     *
     * @param array|\Traversable $data
     * @return \Paragraph\Model\Paragraph\StructureInterface
     */
    public function create( $data )
    {
        $structure = parent::create( $data );

        if ( $structure instanceof Structure\ProxyAbstract )
        {
            $structure->prepareCreate();
        }

        return $structure;
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

        if ( null === $columns )
        {
            $tags = true;
        }
        elseif ( ( $index = array_search( 'tags', $columns ) ) )
        {
            $tags = true;
            unset( $columns[$index] );
        }
        else
        {
            $tags = false;
        }

        if ( null === $columns )
        {
            $tagIds = true;
        }
        elseif ( ( $index = array_search( 'tagIds', $columns ) ) )
        {
            $tagIds = true;
            unset( $columns[$index] );
        }
        else
        {
            $tagIds = false;
        }

        if ( null === $columns )
        {
            $localeTags = true;
        }
        elseif ( ( $index = array_search( 'localeTags', $columns ) ) )
        {
            $localeTags = true;
            unset( $columns[$index] );
        }
        else
        {
            $localeTags = false;
        }

        $columns  = parent::getSelectColumns( $columns );
        $platform = $this->getDbAdapter()
                         ->getPlatform();

        if ( $proxyData )
        {
            $columns['proxyData'] = new Sql\Expression( '(' .
                $this->sql( $this->getTableInSchema(
                        static::$propertyTableName
                     ) )
                     ->select()
                     ->columns( array(
                         new Sql\Expression( 'TEXT( ARRAY_TO_JSON(
                             ARRAY_AGG(
                                 ?
                                 ORDER BY CASE ?
                                    WHEN ? THEN 1
                                    WHEN ? THEN 2
                                    WHEN ? THEN 3
                                    WHEN ? THEN 4
                                    ELSE 5
                                 END DESC
                             )
                         ) )', array(
                             static::$propertyTableName,
                             'locale',
                             $this->getLocale(),
                             $this->getPrimaryLanguage(),
                             $this->getDefaultLocale(),
                             'en',
                         ), array(
                             Sql\Expression::TYPE_IDENTIFIER,
                             Sql\Expression::TYPE_IDENTIFIER,
                             Sql\Expression::TYPE_VALUE,
                             Sql\Expression::TYPE_VALUE,
                             Sql\Expression::TYPE_VALUE,
                             Sql\Expression::TYPE_VALUE,
                         ) ),
                     ) )
                     ->where( array(
                         new Sql\Predicate\Operator(
                             static::$propertyTableName . '.paragraphId',
                             Sql\Predicate\Operator::OPERATOR_EQUAL_TO,
                             static::$tableName . '.id',
                             Sql\Predicate\Operator::TYPE_IDENTIFIER,
                             Sql\Predicate\Operator::TYPE_IDENTIFIER
                         ),
                     ) )
                     ->getSqlString( $platform ) .
            ')' );
        }
        else
        {
            $original = array_keys( static::getColumns() ) + array(
                'tags', 'tagIds', 'localeTags',
            );

            foreach ( $columns as $key => $column )
            {
                if ( is_numeric( $key ) && ! in_array( $column, $original ) )
                {
                    unset( $columns[$key] );

                    if ( ! isset( $columns[$column] ) )
                    {
                        $columns[$column] = new Sql\Expression( '(' .
                            $this->sql( $this->getTableInSchema(
                                     static::$propertyTableName
                                 ) )
                                 ->select()
                                 ->columns( array( 'value' ) )
                                 ->where( array(
                                     new Sql\Predicate\Operator(
                                         static::$propertyTableName . '.paragraphId',
                                         Sql\Predicate\Operator::OPERATOR_EQUAL_TO,
                                         static::$tableName . '.id',
                                         Sql\Predicate\Operator::TYPE_IDENTIFIER,
                                         Sql\Predicate\Operator::TYPE_IDENTIFIER
                                     ),
                                     'name' => $column,
                                 ) )
                                 ->order( array(
                                     new Sql\Expression(
                                         'CASE ? ' .
                                             'WHEN ? THEN 1 ' .
                                             'WHEN ? THEN 2 ' .
                                             'WHEN ? THEN 3 ' .
                                             'ELSE 4 ' .
                                         'END ASC',
                                         array(
                                             'locale',
                                             $this->getLocale(),
                                             $this->getPrimaryLanguage(),
                                             '*',
                                         ),
                                         array(
                                             Sql\Expression::TYPE_IDENTIFIER,
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
                }
            }
        }

        if ( $tags )
        {
            $columns['tags'] = new Sql\Expression( '(' .
                $this->sql( $this->getTableInSchema(
                        static::$tagTableName
                     ) )
                     ->select()
                     ->columns( array(
                         new Sql\Expression(
                             'STRING_AGG( ?, ? ORDER BY ? ASC )',
                             array(
                                 'name',
                                 static::TAG_SEPARATOR,
                                 'name',
                             ),
                             array(
                                 Sql\Expression::TYPE_IDENTIFIER,
                                 Sql\Expression::TYPE_VALUE,
                                 Sql\Expression::TYPE_IDENTIFIER,
                             )
                         ),
                     ) )
                     ->join(
                         $this->getTableInSchema( static::$tagJoinTableName ),
                         static::$tagTableName . '.id = ' .
                         static::$tagJoinTableName . '.tagId',
                         array()
                     )
                     ->where( array(
                         new Sql\Predicate\Operator(
                             static::$tagJoinTableName . '.paragraphId',
                             Sql\Predicate\Operator::OPERATOR_EQUAL_TO,
                             static::$tableName . '.id',
                             Sql\Predicate\Operator::TYPE_IDENTIFIER,
                             Sql\Predicate\Operator::TYPE_IDENTIFIER
                         ),
                     ) )
                     ->getSqlString( $platform ) .
            ')' );
        }

        if ( $tagIds )
        {
            $columns['tagIds'] = new Sql\Expression( '(' .
                $this->sql( $this->getTableInSchema(
                        static::$tagJoinTableName
                     ) )
                     ->select()
                     ->columns( array(
                         new Sql\Expression(
                             'STRING_AGG( TEXT( ? ), ? ORDER BY ? ASC )',
                             array(
                                 'tagId',
                                 static::TAG_SEPARATOR,
                                 'tagId',
                             ),
                             array(
                                 Sql\Expression::TYPE_IDENTIFIER,
                                 Sql\Expression::TYPE_VALUE,
                                 Sql\Expression::TYPE_IDENTIFIER,
                             )
                         ),
                     ) )
                     ->where( array(
                         new Sql\Predicate\Operator(
                             static::$tagJoinTableName . '.paragraphId',
                             Sql\Predicate\Operator::OPERATOR_EQUAL_TO,
                             static::$tableName . '.id',
                             Sql\Predicate\Operator::TYPE_IDENTIFIER,
                             Sql\Predicate\Operator::TYPE_IDENTIFIER
                         ),
                     ) )
                     ->getSqlString( $platform ) .
            ')' );
        }

        if ( $localeTags )
        {
            $columns['localeTags'] = new Sql\Expression( '(' .
                $this->sql( $this->getTableInSchema(
                        static::$tagTableName
                     ) )
                     ->select()
                     ->columns( array(
                         new Sql\Expression(
                             'STRING_AGG( ?, ? ORDER BY ? ASC )',
                             array(
                                 'name',
                                 static::TAG_SEPARATOR,
                                 'name',
                             ),
                             array(
                                 Sql\Expression::TYPE_IDENTIFIER,
                                 Sql\Expression::TYPE_VALUE,
                                 Sql\Expression::TYPE_IDENTIFIER,
                             )
                         ),
                     ) )
                     ->join(
                         $this->getTableInSchema( static::$tagJoinTableName ),
                         static::$tagTableName . '.id = ' .
                         static::$tagJoinTableName . '.tagId',
                         array()
                     )
                     ->where( array(
                         new Sql\Predicate\Operator(
                             static::$tagJoinTableName . '.paragraphId',
                             Sql\Predicate\Operator::OPERATOR_EQUAL_TO,
                             static::$tableName . '.id',
                             Sql\Predicate\Operator::TYPE_IDENTIFIER,
                             Sql\Predicate\Operator::TYPE_IDENTIFIER
                         ),
                         new Sql\Predicate\PredicateSet(
                             array(
                                 new Sql\Predicate\IsNull(
                                     static::$tagTableName . '.locale'
                                 ),
                                 new Sql\Predicate\In(
                                     static::$tagTableName . '.locale',
                                     array(
                                         $this->getLocale(),
                                         $this->getPrimaryLanguage(),
                                         '',
                                     )
                                 ),
                             ),
                             Sql\Predicate\PredicateSet::COMBINED_BY_OR
                         ),
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
        if ( isset( $data['tags'] ) && is_string( $data['tags'] ) )
        {
            $data['tags'] = explode( static::TAG_SEPARATOR, $data['tags'] );
        }

        if ( isset( $data['tagIds'] ) && is_string( $data['tagIds'] ) )
        {
            $data['tagIds'] = explode( static::TAG_SEPARATOR, $data['tagIds'] );
        }

        if ( isset( $data['localeTags'] ) && is_string( $data['localeTags'] ) )
        {
            $data['localeTags'] = explode( static::TAG_SEPARATOR, $data['localeTags'] );
        }

        if ( isset( $data['proxyData'] ) && is_string( $data['proxyData'] ) )
        {
            $data['proxyData'] = $this->parseProxyData( $data['proxyData'] );
        }

        return parent::selected( $data );
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
        if ( $structure instanceof Structure\ProxyBase )
        {
            if ( isset( $data['tags'] ) && is_string( $data['tags'] ) )
            {
                $data['tags'] = explode( static::TAG_SEPARATOR, $data['tags'] );
            }

            if ( isset( $data['tagIds'] ) && is_string( $data['tagIds'] ) )
            {
                $data['tagIds'] = explode( static::TAG_SEPARATOR, $data['tagIds'] );
            }

            if ( isset( $data['localeTags'] ) && is_string( $data['localeTags'] ) )
            {
                $data['localeTags'] = explode( static::TAG_SEPARATOR, $data['localeTags'] );
            }

            if ( isset( $data['proxyData'] ) )
            {
                $proxyData = $data['proxyData'] ?: array();
                unset( $data['proxyData'] );

                if ( is_string( $proxyData ) )
                {
                    $proxyData = $this->parseProxyData( $proxyData );
                }

                if ( is_array( $proxyData ) )
                {
                    $proxyData = array_merge( $data, $proxyData );
                }

                foreach ( static::$columns as $column => $type )
                {
                    unset( $proxyData[$column] );
                }
            }
            else
            {
                $proxyData = $data;
            }

            $proxyData['proxyBase'] = parent::hydrate( $data, $structure );
            $proxyData['type']      = $proxyData['proxyBase']->type;

            return $this->structureFactory
                        ->factory( $proxyData );
        }

        return parent::hydrate( $data, $structure );
    }

    /**
     * Find root paragraph of paragraph by id
     *
     * @param int $id
     * @return \Paragraph\Model\Paragraph\StructureInterface
     */
    public function findRootOf( $id )
    {
        return $this->findOne( array(
            'id' => $this->sql()
                         ->select()
                         ->columns( array( 'rootId' ) )
                         ->where( array(
                             'id' => $id,
                         ) ),
        ) );
    }

    /**
     * Find render-list
     *
     * @param   int|string $idOrMetaName
     * @return  \Paragraph\Model\Paragraph\StructureInterface[]
     */
    public function findRenderList( $idOrMetaName )
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
                         $platform->quoteIdentifierChain( array( 'parent', 'rootId' ) ) . ' = ' .
                         $platform->quoteIdentifierChain( array( static::$tableName, 'rootId' ) )
                     ),
                     new Sql\Predicate\Expression(
                         $platform->quoteIdentifierChain( array( static::$tableName, 'left' ) ) .
                         ' BETWEEN ' . $platform->quoteIdentifierChain( array( 'parent', 'left' ) ) .
                             ' AND ' . $platform->quoteIdentifierChain( array( 'parent', 'right' ) )
                     ),
                 ) )
                 ->getSqlString( $platform ) .
        ')' );

        $select = $this->select( $columns )
                       ->join(
                           array( 'root' => self::$tableName ),
                           '( ' . self::$tableName . '.rootId = root.rootId ) AND ' .
                           '( ' . self::$tableName . '.left BETWEEN root.left AND root.right ) ',
                           array()
                       )
                       ->order( 'left' );

        if ( is_numeric( $idOrMetaName ) )
        {
            $select->where( array(
                'root.id'   => (int) $idOrMetaName,
            ) );
        }
        else
        {
            $select->where( array(
                'root.type' => 'metaContent',
                'root.name' => (string) $idOrMetaName,
            ) );
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
     * @param int $id
     * @return array
     */
    public function findChildrenIdsByType( $id )
    {
        $platform = $this->getDbAdapter()
                         ->getPlatform();

        $select = $this->sql()
                       ->select()
                       ->columns( array( 'id', 'type' ) )
                       ->where( array(
                           new Sql\Predicate\Operator(
                                $id, Sql\Predicate\Operator::OP_EQ,
                                $this->sql( null )
                                     ->select( array( 'parent' => static::$tableName ) )
                                     ->columns( array( 'id' ) )
                                     ->where( array(
                                         new Sql\Predicate\Expression(
                                             $platform->quoteIdentifierChain( array( 'parent', 'id' ) ) . ' != ' .
                                             $platform->quoteIdentifierChain( array( static::$tableName, 'id' ) )
                                         ),
                                         new Sql\Predicate\Expression(
                                             $platform->quoteIdentifierChain( array( 'parent', 'rootId' ) ) . ' = ' .
                                             $platform->quoteIdentifierChain( array( static::$tableName, 'rootId' ) )
                                         ),
                                         new Sql\Predicate\Expression(
                                             $platform->quoteIdentifierChain( array( static::$tableName, 'left' ) ) .
                                             ' BETWEEN ' . $platform->quoteIdentifierChain( array( 'parent', 'left' ) ) .
                                                 ' AND ' . $platform->quoteIdentifierChain( array( 'parent', 'right' ) )
                                         ),
                                     ) )
                                     ->order( array(
                                         'left'     => Sql\Select::ORDER_DESCENDING,
                                         'right'    => Sql\Select::ORDER_ASCENDING,
                                     ) )
                                     ->limit( 1 ),
                                Sql\Predicate\Operator::TYPE_VALUE,
                                Sql\Predicate\Operator::TYPE_VALUE
                            )
                       ) )
                       ->order( array(
                           'left'   => Sql\Select::ORDER_ASCENDING,
                           'right'  => Sql\Select::ORDER_DESCENDING,
                       ) );

        $return = array();
        $result = $this->sql()
                       ->prepareStatementForSqlObject( $select )
                       ->execute();

        foreach ( $result as $row )
        {
            $return[$row['id']] = $row['type'];
        }

        return $return;
    }

    /**
     * Save a single property
     *
     * @param int $id
     * @param bool $locale
     * @param string $name
     * @param mixed $value
     * @return int
     */
    private function saveSingleProperty( $id, $locale, $name, $value )
    {
        $sql = $this->sql( $this->getTableInSchema(
            static::$propertyTableName
        ) );

        $update = $sql->update()
                      ->set( array(
                          'value'       => $value,
                      ) )
                      ->where( array(
                          'paragraphId' => $id,
                          'locale'      => $locale,
                          'name'        => $name,
                      ) );

        $affected = $sql->prepareStatementForSqlObject( $update )
                        ->execute()
                        ->getAffectedRows();

        if ( $affected < 1 )
        {
            $insert = $sql->insert()
                          ->values( array(
                              'paragraphId' => $id,
                              'locale'      => $locale,
                              'name'        => $name,
                              'value'       => $value,
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
     * @param bool $localeAware
     * @param string $name
     * @param mixed $value
     * @return int
     */
    protected function saveProperty( $id, $localeAware, $name, $value )
    {
        $rows   = 0;
        $locale = $localeAware ? $this->getLocale() : '*';
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
                                  'paragraphId' => $id,
                                  'locale'      => $locale,
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
                    $rows += $this->saveSingleProperty( $id, $locale, $key, $val );
                }

                $delete = $sql->delete()
                              ->where( array(
                                  'paragraphId' => $id,
                                  'locale'      => $locale,
                                  $nameLikeOrEq,
                                  new Sql\Predicate\NotIn( 'name', $keys ),
                              ) );

                $rows += $sql->prepareStatementForSqlObject( $delete )
                             ->execute()
                             ->getAffectedRows();
            }
        }
        else
        {
            $rows += $this->saveSingleProperty( $id, $locale, $name, $value );

            $delete = $sql->delete()
                          ->where( array(
                              'paragraphId' => $id,
                              'locale'      => $locale,
                              new Sql\Predicate\Like( 'name', $like ),
                          ) );

            $rows += $sql->prepareStatementForSqlObject( $delete )
                         ->execute()
                         ->getAffectedRows();
        }

        return $rows;
    }

    /**
     * Delete tags not in $tags
     *
     * @param   int     $paragraphId
     * @param   array   $tags
     * @return  int
     */
    protected function deleteTagsNotIn( $paragraphId, array $tags )
    {
        $tagSql     = $this->sql( $this->getTableInSchema( static::$tagTableName ) );
        $tagJoinSql = $this->sql( $this->getTableInSchema( static::$tagJoinTableName ) );

        if ( empty( $tags ) )
        {
            $delete = $tagJoinSql->delete()
                                 ->where( array(
                                     'paragraphId' => $paragraphId,
                                 ) );
        }
        else
        {
            $delete = $tagJoinSql->delete()
                                 ->where( array(
                                     'paragraphId' => $paragraphId,
                                     new Sql\Predicate\NotIn(
                                         'tagId',
                                         $tagSql->select()
                                                ->columns( array( 'id' ) )
                                                ->where( array(
                                                    new ExpressionIn(
                                                        'LOWER(?)',
                                                        'name',
                                                        array_map( function ( $tag ) {
                                                            return mb_strtolower( $tag, 'UTF-8' );
                                                        }, $tags )
                                                    ),
                                                ) )
                                     ),
                                 ) );
        }

        return $tagJoinSql->prepareStatementForSqlObject( $delete )
                          ->execute()
                          ->getAffectedRows();
    }

    /**
     * Get tag id by its name
     *
     * @param   string  $tag
     * @return  int|null
     */
    private function getTagIdByName( $tag )
    {
        $tagSql = $this->sql( $this->getTableInSchema( static::$tagTableName ) );

        $select = $tagSql->select()
                         ->columns( array( 'id' ) )
                         ->where( array(
                             new TypedParameters(
                                 'LOWER(?) = ?',
                                 array(
                                     'name',
                                     mb_strtolower( $tag, 'UTF-8' ),
                                 ),
                                 array(
                                     TypedParameters::TYPE_IDENTIFIER,
                                     TypedParameters::TYPE_VALUE,
                                 )
                             ),
                         ) );

        $result = $tagSql->prepareStatementForSqlObject( $select )
                         ->execute();

        foreach ( $result as $row )
        {
           return $row['id'];
        }

        return null;
    }

    /**
     * Set a tag for a paragraph
     *
     * @param   int     $paragraphId
     * @param   string  $tag
     * @return  int
     */
    protected function setTagFor( $paragraphId, $tag )
    {
        $rows       = 0;
        $tagSql     = $this->sql( $this->getTableInSchema( static::$tagTableName ) );
        $tagJoinSql = $this->sql( $this->getTableInSchema( static::$tagJoinTableName ) );
        $tagId      = $this->getTagIdByName( $tag );

        if ( empty( $tagId ) )
        {
           $insert = $tagSql->insert()
                            ->values( array(
                                'locale'   => $this->getLocale(),
                                'name'     => $tag,
                            ) );

           $rows += $tagSql->prepareStatementForSqlObject( $insert )
                           ->execute()
                           ->getAffectedRows();

           $tagId = $this->getTagIdByName( $tag );
        }

        $data = array(
            'paragraphId'   => $paragraphId,
            'tagId'         => $tagId,
        );

        $select = $tagJoinSql->select()
                             ->where( $data );

        if ( ! $tagJoinSql->prepareStatementForSqlObject( $select )
                          ->execute()
                          ->getAffectedRows() )
        {
            $insert = $tagJoinSql->insert()
                                 ->values( $data );

            $rows += $tagJoinSql->prepareStatementForSqlObject( $insert )
                                ->execute()
                                ->getAffectedRows();
        }

        return $rows;
    }

    /**
     * Save element structure to datasource
     *
     * @param \Paragraph\Model\Paragraph\Structure\ProxyAbstract $structure
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

            foreach ( $structure->getPropertiesIterator() as $property => $value )
            {
                $result += $this->saveProperty(
                    $id, $structure::isPropertyLocaleAware( $property ),
                    $property, $value
                );
            }

            $tags = (array) $structure->getTags();
            $result += $this->deleteTagsNotIn( $id, $tags );

            foreach ( $tags as $tag )
            {
                $result += $this->setTagFor( $id, $tag );
            }
        }

        foreach ( $structure->getDependentStructures() as $depStruct )
        {
            if ( empty( $depStruct ) )
            {
                continue;
            }

            if ( $depStruct instanceof MapperAwareAbstract )
            {
                $result += $depStruct->save();
            }
            else if ( $depStruct instanceof MapperAwareInterface )
            {
                $result += $depStruct->getMapper()
                                     ->save( $depStruct );
            }
        }

        return $result;
    }

    /**
     * Move a node in the hierarchy
     *
     * @param  int      $sourceNode
     * @param  string   $position
     * @param  int      $relatedNode
     * @return bool
     */
    public function moveNode( $sourceNode, $position, $relatedNode )
    {
        return $this->sql()
                    ->paragraph_move( (int)     $sourceNode,
                                      (string)  $position,
                                      (int)     $relatedNode );
    }

    /**
     * Clone a node (with properties & customize)
     *
     * @param  int          $sourceNode
     * @param  string|null  $sourceSchema optional, default: current schema
     * @return int          Cloned paragraph-id
     */
    public function cloneNode( $sourceNode, $sourceSchema = null )
    {
        return $this->sql()
                    ->paragraph_clone( ( (string) $sourceSchema ) ?: null,
                                       (int)      $sourceNode );
    }

}
