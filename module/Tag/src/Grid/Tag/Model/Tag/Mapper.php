<?php

namespace Grid\Tag\Model\Tag;

use Zend\Db\Sql;
use Zork\Model\LocaleAwareTrait;
use Zork\Model\LocaleAwareInterface;
use Zork\Model\Mapper\DbAware\ReadWriteMapperAbstract;

/**
 * Mapper
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Mapper extends ReadWriteMapperAbstract
          implements LocaleAwareInterface
{

    use LocaleAwareTrait;

    /**
     * Table name used in all queries
     *
     * @var string
     */
    protected static $tableName = 'tag';

    /**
     * Additional table name used in all queries
     *
     * @var string
     */
    protected static $paragraphJoinTableName = 'paragraph_x_tag';

    /**
     * Additional table name used in all queries
     *
     * @var string
     */
    protected static $paragraphTableName = 'paragraph';

    /**
     * Default column-conversion functions as types;
     * used in selected(), deselect()
     *
     * @var array
     */
    protected static $columns = array(
        'id'        => self::INT,
        'locale'    => self::STR,
        'name'      => self::STR,
    );

    /**
     * Contructor
     *
     * @param   \Tag\Model\Tag\Structure    $tagStructurePrototype
     * @param   string                      $locale
     */
    public function __construct( Structure $tagStructurePrototype   = null,
                                 $locale                            = null )
    {
        parent::__construct( $tagStructurePrototype ?: new Structure );
        $this->setLocale( $locale );
    }

    /**
     * Get paginator
     *
     * @param   mixed|null  $where
     * @param   mixed|null  $order
     * @param   mixed|null  $columns
     * @param   mixed|null  $joins
     * @param   mixed|null  $quantifier
     * @return  \Zend\Paginator\Paginator
     * /
    public function getPaginator( $where        = null,
                                  $order        = null,
                                  $columns      = null,
                                  $joins        = null,
                                  $quantifier   = null )
    {
        $joins = array_merge( (array) $joins, array(
        / * 'user_group' => array(
                'table'     => $this->getTableInSchema( 'user_group' ),
                'where'     => 'user.groupId = user_group.id',
                'columns'   => array(
                    'groupName' => 'name',
                ),
            ), * /
        ) );

        return parent::getPaginator( $where, $order, $columns, $joins, $quantifier );
    }
    */

    /**
     * Get tag by name
     *
     * @param   string $name
     * @return  \Tag\Model\Tag\Structure|null
     */
    public function findByName( $name )
    {
        $platform = $this->getDbAdapter()
                         ->getPlatform();

        return $this->findOne( array(
            new Sql\Predicate\Expression(
                'LOWER(' . $platform->quoteIdentifier( 'name' ) . ') = ?',
                mb_strtolower( $name, 'UTF-8' )
            ),
        ) );
    }

    /**
     * Find tag usages by locale(s)
     *
     * @param   array $locales
     * @return  array
     */
    public function findUsagesByLocales( array $locales )
    {
        $select = $this->sql()
                       ->select()
                       ->columns( array(
                           'id',
                           'name',
                           'locale',
                           'usage' => new Sql\Expression(
                                'COUNT( ?.? )',
                                array(
                                    static::$paragraphJoinTableName,
                                    'paragraphId',
                                ),
                                array(
                                    Sql\Expression::TYPE_IDENTIFIER,
                                    Sql\Expression::TYPE_IDENTIFIER,
                                )
                            ),
                        ) )
                       ->join(
                            static::$paragraphJoinTableName,
                            static::$tableName . '.id = ' .
                            static::$paragraphJoinTableName . '.tagId',
                            array(),
                            Sql\Select::JOIN_INNER
                        )
                       ->join(
                            static::$paragraphTableName,
                            static::$paragraphTableName . '.id = ' .
                            static::$paragraphJoinTableName . '.paragraphId',
                            array(),
                            Sql\Select::JOIN_INNER
                        )
                       ->where( array(
                            'paragraph.type' => 'content',
                            new Sql\Predicate\PredicateSet(
                                array(
                                    new Sql\Predicate\IsNull( 'locale' ),
                                    new Sql\Predicate\In( 'locale', $locales ),
                                ),
                                Sql\Predicate\PredicateSet::OP_OR
                            ),
                        ) )
                       ->group( array(
                           static::$tableName . '.id',
                           static::$tableName . '.name',
                           static::$tableName . '.locale',
                        ) )
                       ->order( array(
                           static::$tableName . '.name',
                           static::$tableName . '.locale',
                       ) );

        return $this->sql()
                    ->prepareStatementForSqlObject( $select )
                    ->execute();
    }

    /**
     * Is name already exists
     *
     * @param   string      $name
     * @param   int|null    $excludeId
     * @return  bool
     */
    public function isNameExists( $name, $excludeId = null )
    {
        $platform = $this->getDbAdapter()
                         ->getPlatform();

        $nameEq = new Sql\Predicate\Expression(
            'LOWER(' . $platform->quoteIdentifier( 'name' ) . ') = ?',
            mb_strtolower( $name, 'UTF-8' )
        );

        return $this->isExists( empty( $excludeId ) ? array(
            $nameEq,
        ) : array(
            $nameEq,
            new Sql\Predicate\Operator(
                'id',
                Sql\Predicate\Operator::OP_NE,
                $excludeId
            ),
        ) );
    }

    /**
     * Delete a tag
     *
     * @param   int|array|\Tag\Model\Tag\Structure $tagOrId
     * @return  int
     */
    public function delete( $tagOrId )
    {
        if ( is_numeric( $tagOrId ) )
        {
            $tag = $this->find( (int) $tagOrId );
        }
        else if ( is_scalar( $tagOrId ) )
        {
            $tag = $this->findByName( (string) $tagOrId );
        }
        else if ( is_array( $tagOrId ) )
        {
            if ( ! empty( $tagOrId['id'] ) )
            {
                $tag = $this->find( (int) $tagOrId['id'] );
            }
            else if ( ! empty( $tagOrId['name'] ) )
            {
                $tag = $this->findByName( (string) $tagOrId['name'] );
            }
        }
        else if ( $tagOrId instanceof Structure )
        {
            $tag = $tagOrId;
        }

        if ( empty( $tag ) )
        {
            return 0;
        }

        return parent::delete( $tag );
    }

}
