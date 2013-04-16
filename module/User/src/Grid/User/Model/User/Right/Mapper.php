<?php

namespace Grid\User\Model\User\Right;

use Zend\Db\Sql;
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
        'module'        => self::STR,
    );

    /**
     * Contructor
     *
     * @param \User\Model\User\Right\Structure $userRightStructurePrototype
     */
    public function __construct( Structure $userRightStructurePrototype = null )
    {
        parent::__construct( $userRightStructurePrototype ?: new Structure );
    }

    /**
     * Get all rights and granted flags to a user
     *
     * @param   int     $userId
     * @return  array
     */
    public function findAllByUser( $userId )
    {
        $select = $this->select()
                       ->join( 'user_right_x_user',
                               new Sql\Expression(
                                    '?.? = ?.? AND ?.? = ?',
                                    array(
                                        self::$tableName,
                                        'id',
                                        'user_right_x_user',
                                        'rightId',
                                        'user_right_x_user',
                                        'userId',
                                        $userId,
                                    ),
                                    array(
                                        Sql\Expression::TYPE_IDENTIFIER,
                                        Sql\Expression::TYPE_IDENTIFIER,
                                        Sql\Expression::TYPE_IDENTIFIER,
                                        Sql\Expression::TYPE_IDENTIFIER,
                                        Sql\Expression::TYPE_IDENTIFIER,
                                        Sql\Expression::TYPE_IDENTIFIER,
                                        Sql\Expression::TYPE_VALUE,
                                    )
                               ),
                               array(
                                   'granted' => 'userId'
                               ),
                               Sql\Select::JOIN_LEFT )
                        ->order( array(
                            'group'     => 'ASC',
                            'resource'  => 'ASC',
                            'privilege' => 'ASC',
                        ) );

        /* @var $result \Zend\Db\Adapter\Driver\ResultInterface */

        $return = array();
        $result = $this->sql()
                       ->prepareStatementForSqlObject( $select )
                       ->execute();

        foreach ( $result as $row )
        {
            $return[] = $this->selected( $row );
        }

        return $return;
    }

    /**
     * Get all rights and granted flags to a user
     *
     * @param   int     $groupId
     * @return  array
     */
    public function findAllByGroup( $groupId )
    {
        $select = $this->select()
                       ->join( 'user_right_x_user_group',
                               new Sql\Expression(
                                    '?.? = ?.? AND ?.? = ?',
                                    array(
                                        self::$tableName,
                                        'id',
                                        'user_right_x_user_group',
                                        'rightId',
                                        'user_right_x_user_group',
                                        'groupId',
                                        $groupId,
                                    ),
                                    array(
                                        Sql\Expression::TYPE_IDENTIFIER,
                                        Sql\Expression::TYPE_IDENTIFIER,
                                        Sql\Expression::TYPE_IDENTIFIER,
                                        Sql\Expression::TYPE_IDENTIFIER,
                                        Sql\Expression::TYPE_IDENTIFIER,
                                        Sql\Expression::TYPE_IDENTIFIER,
                                        Sql\Expression::TYPE_VALUE,
                                    )
                               ),
                               array(
                                   'granted' => 'groupId'
                               ),
                               Sql\Select::JOIN_LEFT )
                        ->order( array(
                            'group'     => 'ASC',
                            'resource'  => 'ASC',
                            'privilege' => 'ASC',
                        ) );

        /* @var $result \Zend\Db\Adapter\Driver\ResultInterface */

        $return = array();
        $result = $this->sql()
                       ->prepareStatementForSqlObject( $select )
                       ->execute();

        foreach ( $result as $row )
        {
            $return[] = $this->selected( $row );
        }

        return $return;
    }

    /**
     * Grant right
     *
     * @param   \Zend\Db\Sql\Sql    $sql
     * @param   array               $data
     * @param   bool                $grant
     * @return  int
     */
    private function grant( Sql\Sql $sql, array $data, $grant = true )
    {
        if ( $grant )
        {
            $select = $sql->select()
                          ->where( $data );

            $result = $sql->prepareStatementForSqlObject( $select )
                          ->execute();

            if ( $result->getAffectedRows() )
            {
                return 0;
            }
            else
            {
                $insert = $sql->insert()
                              ->values( $data );

                $result = $sql->prepareStatementForSqlObject( $insert )
                              ->execute();

                return $result->getAffectedRows();
            }
        }
        else
        {
            $delete = $sql->delete()
                          ->where( $data );

            $result = $sql->prepareStatementForSqlObject( $delete )
                          ->execute();

            return $result->getAffectedRows();
        }
    }

    /**
     * Grant a right to a user
     *
     * @param   int     $rightId
     * @param   int     $userId
     * @param   bool    $grant
     * @return  int
     */
    public function grantToUser( $rightId, $userId, $grant = true )
    {
        return $this->grant(
            $this->sql( $this->getTableInSchema( 'user_right_x_user' ) ),
            array(
                'rightId'   => $rightId,
                'userId'    => $userId,
            ),
            $grant
        );
    }

    /**
     * Grant a right to a user
     *
     * @param   int     $rightId
     * @param   int     $groupId
     * @param   bool    $grant
     * @return  int
     */
    public function grantToGroup( $rightId, $groupId, $grant = true )
    {
        return $this->grant(
            $this->sql( $this->getTableInSchema( 'user_right_x_user_group' ) ),
            array(
                'rightId'   => $rightId,
                'groupId'   => $groupId,
            ),
            $grant
        );
    }

}
