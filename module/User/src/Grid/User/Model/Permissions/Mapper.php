<?php

namespace Grid\User\Model\Permissions;

use Zork\Db\Sql\TableIdentifier;
use Zend\Db\Sql\Predicate\Expression;
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
    protected static $tableName = 'user_right';

    /**
     * Default column-conversion functions as types;
     * used in selected(), deselect()
     *
     * @var array
     */
    protected static $columns = array(
        'id'            => self::INT,
        'label'         => self::STR,
        'group'         => self::STR,
        'resource'      => self::STR,
        'privilege'     => self::STR,
        'optional'      => self::BOOL,
    );

    /**
     * @var array
     */
    private $userGroups;

    /**
     * Contructor
     *
     * @param \User\Model\Permissions\Structure $userPermissionsStructurePrototype
     */
    public function __construct( Structure $userPermissionsStructurePrototype = null )
    {
        parent::__construct( $userPermissionsStructurePrototype ?: new Structure );
    }

    /**
     * Get permissions by userId
     *
     * @param int $userId
     * @return \User\Model\User\Structure|null
     */
    public function findAllByUserId( $userId )
    {
        $platform = $this->getDbAdapter()
                         ->getPlatform();

        /* @var $from \Zork\Db\Sql\TableIdentifier */
        $from = $this->getTableInSchema( 'user_right_x_user' );

        if ( $from instanceof TableIdentifier )
        {
            $from = $from->getIdentifierChain();
        }
        else
        {
            $from = (array) (string) $from;
        }

        return $this->findAll( array(
            new Expression( $platform->quoteIdentifier( 'id' ) . ' IN (
                SELECT ' . $platform->quoteIdentifier( 'rightId' ) . '
                  FROM ' . $platform->quoteIdentifierChain( $from ) . '
                 WHERE ' . $platform->quoteIdentifier( 'userId' ) . ' = ?
            )', $userId ),
        ) );
    }

    /**
     * Get permissions by userGroupId
     *
     * @param int $userGroupId
     * @return \User\Model\User\Structure|null
     */
    public function findAllByGroupId( $userGroupId )
    {
        $platform = $this->getDbAdapter()
                         ->getPlatform();

        /* @var $from \Zork\Db\Sql\TableIdentifier */
        $from = $this->getTableInSchema( 'user_right_x_user_group' );

        if ( $from instanceof TableIdentifier )
        {
            $from = $from->getIdentifierChain();
        }
        else
        {
            $from = (array) (string) $from;
        }

        return $this->findAll( array(
            new Expression( $platform->quoteIdentifier( 'id' ) . ' IN (
                SELECT ' . $platform->quoteIdentifier( 'rightId' ) . '
                  FROM ' . $platform->quoteIdentifierChain( $from ) . '
                 WHERE ' . $platform->quoteIdentifier( 'groupId' ) . ' = ?
            )', $userGroupId ),
        ) );
    }

    /**
     * Find all user-groups
     *
     * return array %id% => %name% pairs
     */
    public function findUserGroups($where=array())
    {
        if ( null === $this->userGroups )
        {
            $this->userGroups = array();

            $select = $this->sql( 'user_group' )
                           ->select()
                           ->columns( array( 'id', 'name' ) )
                           ->where( $where )
                        ;

            /* @var $result \Zend\Db\Adapter\Driver\ResultInterface */
            $result = $this->sql()
                           ->prepareStatementForSqlObject( $select )
                           ->execute();

            foreach ( $result as $row )
            {
                $this->userGroups[$row['id']] = $row['name'];
            }
        }

        return $this->userGroups;
    }

}
