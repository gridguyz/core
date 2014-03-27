<?php

namespace Grid\User\Model\User;

use Zork\Db\SiteInfo;
use Zend\Db\Sql\Predicate;
use Zork\Db\SiteInfoAwareTrait;
use Zork\Db\SiteInfoAwareInterface;
use Zork\Model\Mapper\DbAware\ReadWriteMapperAbstract;

/**
 * Mapper
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Mapper extends ReadWriteMapperAbstract
          implements SiteInfoAwareInterface
{

    use SiteInfoAwareTrait;

    /**
     * Table name used in all queries
     *
     * @var string
     */
    protected static $tableName = 'user';

    /**
     * Default column-conversion functions as types;
     * used in selected(), deselect()
     *
     * @var array
     */
    protected static $columns = array(
        'id'            => self::INT,
        'email'         => self::STR,
        'displayName'   => self::STR,
        'passwordHash'  => self::STR,
        'locale'        => self::STR,
        'confirmed'     => self::BOOL,
        'state'         => self::STR,
        'groupId'       => self::INT,
        'avatar'        => self::STR,
    );

    /**
     * @var \User\Model\User\Group\Mapper
     */
    protected $userGroupMapper;

    /**
     * @return \User\Model\User\Group\Mapper
     */
    public function getUserGroupMapper()
    {
        return $this->userGroupMapper;
    }

    /**
     * @param \User\Model\User\Group\Mapper $userGroupMapper
     * @return \User\Model\User\Mapper
     */
    public function setUserGroupMapper( Group\Mapper $userGroupMapper )
    {
        $this->userGroupMapper = $userGroupMapper;
        return $this;
    }

    /**
     * Contructor
     *
     * @param \User\Model\User\Group\Mapper $userGroupMapper
     * @param \User\Model\User\Structure $userStructurePrototype
     */
    public function __construct( Group\Mapper   $userGroupMapper,
                                 SiteInfo       $siteInfo,
                                 Structure      $userStructurePrototype = null )
    {
        parent::__construct( $userStructurePrototype ?: new Structure );
        $this->setUserGroupMapper( $userGroupMapper )
             ->setSiteInfo( $siteInfo );
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
            'user_group' => array(
                'table'     => $this->getTableInSchema( 'user_group' ),
                'where'     => 'user.groupId = user_group.id',
                'columns'   => array(
                    'groupName' => 'name',
                ),
            ),
        ) );

        return parent::getPaginator( $where, $order, $columns, $joins, $quantifier );
    }

    /**
     * Create structure from plain data
     *
     * @param array $data
     * @return \Zork\Model\Structure\StructureAbstract
     */
    public function createStructure( array $data )
    {
        $structure = parent::createStructure( $data );

        if ( $structure instanceof SiteInfoAwareInterface )
        {
            $structure->setSiteInfo( $this->getSiteInfo() );
        }

        return $structure;
    }

    /**
     * Get user by email
     *
     * @param string $email
     * @return \User\Model\User\Structure|null
     */
    public function findByEmail( $email )
    {
        return $this->findOne( array(
            'email' => $email,
        ) );
    }

    /**
     * Get user by display-name
     *
     * @param string $displayName
     * @return \User\Model\User\Structure|null
     */
    public function findByDisplayName( $displayName )
    {
        return $this->findOne( array(
            'displayName' => $displayName,
        ) );
    }

    /**
     * Has associated identity
     *
     * @param int $userId
     * @param string $identity
     * @return int Association id
     */
    public function hasAssociatedIdentity( $userId, $identity )
    {
        $sql    = $this->sql( $this->getTableInSchema( 'user_identity' ) );
        $select = $sql->select()
                      ->columns( array( 'id' ) )
                      ->where( array(
                          'userId'      => (int) $userId,
                          'identity'    => (string) $identity,
                      ) )
                      ->limit( 1 );

        $result = $sql->prepareStatementForSqlObject( $select )
                      ->execute();

        if ( $result->getAffectedRows() < 1 )
        {
            return null;
        }

        foreach ( $result as $row )
        {
            if ( ! empty( $row['id'] ) )
            {
                return $row['id'];
            }
        }

        return null;
    }

    /**
     * Associate identity
     *
     * @param int $userId
     * @param string $identity
     * @return int affected rows (with insert)
     */
    public function associateIdentity( $userId, $identity )
    {
        if ( $this->hasAssociatedIdentity( $userId, $identity ) )
        {
            return 0;
        }

        $sql    = $this->sql( $this->getTableInSchema( 'user_identity' ) );
        $insert = $sql->insert()
                      ->values( array(
                          'userId'    => (int)    $userId,
                          'identity'  => (string) $identity,
                      ) );

        $result = $sql->prepareStatementForSqlObject( $insert )
                      ->execute();

        return $result->getAffectedRows();
    }

    /**
     * Is email already exists
     *
     * @param string $email
     * @param int|null $excludeId
     * @return bool
     */
    public function isEmailExists( $email, $excludeId = null )
    {
        return $this->isExists( empty( $excludeId ) ? array(
            'email'     => $email,
            'confirmed' => true,
            new Predicate\Operator(
                'state',
                Predicate\Operator::OP_NE,
                Structure::STATE_INACTIVE
            ),
        ) : array(
            'email' => $email,
            new Predicate\Operator(
                'id',
                Predicate\Operator::OP_NE,
                $excludeId
            ),
        ) );
    }

    /**
     * Is email already taken
     *
     * @param string $email
     * @param int|null $excludeId
     * @return bool
     */
    public function isEmailTaken( $email, $excludeId = null )
    {
        return $this->isExists( empty( $excludeId ) ? array(
            'email' => $email,
        ) : array(
            'email' => $email,
            new Predicate\Operator(
                'id',
                Predicate\Operator::OP_NE,
                $excludeId
            ),
        ) );
    }

    /**
     * Is dsplay name already exists
     *
     * @param string $displayName
     * @param int|null $excludeId
     * @return bool
     */
    public function isDisplayNameExists( $displayName, $excludeId = null )
    {
        $platform = $this->getDbAdapter()
                         ->getPlatform();

        $expr = new Predicate\Expression(
            'LOWER( ' . $platform->quoteIdentifier( 'displayName' ) .
                ' ) = LOWER( ? )', $displayName
        );

        return $this->isExists( empty( $excludeId ) ? array(
            $expr,
        ) : array(
            $expr,
            new Predicate\Operator(
                'id',
                Predicate\Operator::OP_NE,
                $excludeId
            ),
        ) );
    }

    /**
     * Delete a user
     *
     * @param int|array|\User\Model\User\Structure $userOrId
     * @return int
     */
    public function delete( $userOrId )
    {
        if ( is_scalar( $userOrId ) )
        {
            $user = $this->find( (int) $userOrId );
        }
        else if ( is_array( $userOrId ) )
        {
            if ( empty( $userOrId['id'] ) )
            {
                $id = (int) reset( $userOrId );
            }
            else
            {
                $id = (int) $userOrId['id'];
            }

            $user = $this->find( $id );
        }
        else if ( $userOrId instanceof Structure )
        {
            $user = $userOrId;
        }

        if ( empty( $user ) )
        {
            return 0;
        }

        $user->state = Structure::STATE_INACTIVE;
        return $user->save();
    }

}
