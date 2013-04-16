<?php

namespace Grid\Core\Model\SubDomain;

use Zend\Db\Sql\Select;
use Zend\Db\Sql\Predicate;
use Zork\Model\Mapper\DbAware\ReadWriteMapperAbstract;

/**
 * Mapper
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Mapper extends ReadWriteMapperAbstract
{

    /**
     * Table name used in all queries
     *
     * @var string
     */
    protected static $tableName = 'subdomain';

    /**
     * Default column-conversion functions as types;
     * used in selected(), deselect()
     *
     * @var array
     */
    protected static $columns = array(
        'id'                => self::INT,
        'subdomain'         => self::STR,
        'locale'            => self::STR,
        'defaultLayoutId'   => self::INT,
        'defaultContentId'  => self::INT,
    );

    /**
     * Contructor
     *
     * @param \Core\Model\SubDomain\Structure $subDomainStructurePrototype
     */
    public function __construct( Structure $subDomainStructurePrototype = null )
    {
        parent::__construct( $subDomainStructurePrototype ?: new Structure );
    }

    /**
     * Is subdomain already exists
     *
     * @param   string      $subdomain
     * @param   int|null    $excludeId
     * @return  bool
     */
    public function isSubdomainExists( $subdomain, $excludeId = null )
    {
        return $this->isExists( empty( $excludeId ) ? array(
            'subdomain' => $subdomain,
        ) : array(
            'subdomain' => $subdomain,
            new Predicate\Operator(
                'id',
                Predicate\Operator::OP_NE,
                $excludeId
            ),
        ) );
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
     */
    public function getPaginator( $where        = null,
                                  $order        = null,
                                  $columns      = null,
                                  $joins        = null,
                                  $quantifier   = null )
    {
        $joins = array_merge( (array) $joins, array(
            'paragraph_layout' => array(
                'table'     => array( 'paragraph_layout' => 'paragraph' ),
                'where'     => static::$tableName . '.defaultLayoutId = paragraph_layout.id',
                'columns'   => array( 'defaultLayoutName' => 'name' ),
                'type'      => Select::JOIN_LEFT,
            ),
            'paragraph_content' => array(
                'table'     => array( 'paragraph_content' => 'paragraph' ),
                'where'     => static::$tableName . '.defaultContentId = paragraph_content.id',
                'columns'   => array( 'defaultContentName' => 'name' ),
                'type'      => Select::JOIN_LEFT,
            ),
        ) );

        return parent::getPaginator( $where, $order, $columns, $joins, $quantifier );
    }

}
