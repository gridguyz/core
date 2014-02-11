<?php

namespace Grid\Customize\Model\Extra;

use Traversable;
use Zend\Db\Sql\Predicate;
use Zend\Stdlib\ArrayUtils;
use Zork\Model\Mapper\DbAware\ReadWriteMapperAbstract;

/**
 * Rule mapper
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
    protected static $tableName = 'customize_extra';

    /**
     * Default column-conversion functions as types;
     * used in selected(), deselect()
     *
     * @var array
     */
    protected static $columns = array(
        'id'                => self::INT,
        'rootParagraphId'   => self::INT,
        'extra'             => self::STR,
        'updated'           => self::DATETIME,
    );

    /**
     * Contructor
     *
     * @param \Customize\Model\Extra\Structure $customizeExtraStructurePrototype
     */
    public function __construct( Structure $customizeExtraStructurePrototype = null )
    {
        parent::__construct( $customizeExtraStructurePrototype ?: new Structure );
    }

    /**
     * Find structure by root paragraph
     *
     * @param int|null $rootParagraphId
     * @return \Customize\Model\Extra\Structure
     */
    public function findByRoot( $rootParagraphId )
    {
        return $this->findOne( array(
            'rootParagraphId' => ( (int) $rootParagraphId ) ?: null,
        ) );
    }

    /**
     * Find updated times
     *
     * @param   array|int   $rootParagraphIds
     * @param   bool|null   $global
     * @return  DateTime[]
     */
    public function findUpdated( $rootParagraphIds, $global = null )
    {
        if ( $rootParagraphIds instanceof Traversable )
        {
            $rootParagraphIds = ArrayUtils::iteratorToArray( $rootParagraphIds );
        }
        else if ( ! is_array( $rootParagraphIds ) )
        {
            $rootParagraphIds = (array) $rootParagraphIds;
        }

        if ( null === $global && in_array( null, $rootParagraphIds ) )
        {
            $global = true;
        }

        $rootParagraphIds = array_filter( $rootParagraphIds );

        if ( $global )
        {
            $where = array(
                new Predicate\PredicateSet(
                    array(
                        new Predicate\IsNull( 'rootParagraphId' ),
                        new Predicate\In( 'rootParagraphId', $rootParagraphIds ),
                    ),
                    Predicate\PredicateSet::COMBINED_BY_OR
                ),
            );
        }
        else
        {
            $where = array(
                'rootParagraphId' => $rootParagraphIds,
            );
        }

        $sql        = $this->sql();
        $platform   = $sql->getAdapter()
                          ->getPlatform();
        $select     = $sql->select()
                          ->columns( array( 'rootParagraphId', 'updated' ) )
                          ->where( $where )
                          ->order( array(
                              'COALESCE( '
                                . $platform->quoteIdentifier( 'rootParagraphId' )
                                . ', 0 ) ASC'
                          ) );

        /* @var $result \Zend\Db\Adapter\Driver\ResultInterface */

        $result = $this->sql()
                       ->prepareStatementForSqlObject( $select )
                       ->execute();

        $affected = $result->getAffectedRows();

        if ( $affected < 1 )
        {
            return array();
        }

        $updated = array();

        foreach ( $result as $row )
        {
            $updated[$row['rootParagraphId']] = DateTime::create(
                $row['updated']
            );
        }

        return $updated;
    }

}
