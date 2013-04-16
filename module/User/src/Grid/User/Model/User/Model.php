<?php

namespace Grid\User\Model\User;

use Zend\Db\Sql\Predicate\In;
use Zork\Model\MapperAwareTrait;
use Zork\Model\MapperAwareInterface;
use Zend\Db\Sql\Predicate\PredicateSet;
use Grid\User\Model\Permissions\Model as PermissionsModel;

/**
 * Model
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Model implements MapperAwareInterface
{

    use MapperAwareTrait;

    /**
     * @var     \User\Model\Permissions\Model
     */
    protected $permissionsModel;

    /**
     * @return  \User\Model\Permissions\Model
     */
    public function getPermissionsModel()
    {
        return $this->permissionsModel;
    }

    /**
     * @param   \User\Model\Permissions\Model   $permissionsModel
     * @return  \User\Model\User\Model
     */
    public function setPermissionsModel( PermissionsModel $permissionsModel )
    {
        $this->permissionsModel = $permissionsModel;
        return $this;
    }

    /**
     * Construct model
     *
     * @param   \User\Model\User\Mapper         $userMapper
     * @param   \User\Model\Permissions\Model   $permissionsModel
     */
    public function __construct( Mapper             $userMapper,
                                 PermissionsModel   $permissionsModel )
    {
        $this->setMapper( $userMapper )
             ->setPermissionsModel( $permissionsModel );
    }

    /**
     * Get paginator for listing
     *
     * @return  \Zend\Paginator\Paginator
     */
    public function getPaginator()
    {
        $permissions = $this->getPermissionsModel();

        return $this->getMapper()
                    ->getPaginator( array(
                        new PredicateSet(
                            array(
                                new In( 'user.id', array_keys(
                                    $permissions->allowedUsers( 'view' )
                                ) ),
                                new In( 'user.groupId', array_keys(
                                    $permissions->allowedUserGroups( 'view' )
                                ) ),
                            ),
                            PredicateSet::OP_OR
                        ),
                    ) );
    }

    /**
     * Create a new user from data
     *
     * @param   array|null  $data
     * @return  \User\Model\User\Structure
     */
    public function create( $data )
    {
        return $this->getMapper()
                    ->create( $data );
    }

    /**
     * User registration
     *
     * @param   array   $options
     * @return  \User\Model\User\Structure
     */
    public function register( array $options )
    {
        if ( empty( $options['email'] ) )
        {
            return null;
        }

        $user = $this->getMapper()
                     ->findByEmail( $options['email'] );

        if ( empty( $user ) )
        {
            $user = $this->getMapper()
                         ->create( $options );
        }
        else if ( $user->state == Structure::STATE_BANNED )
        {
            return null;
        }
        else
        {
            $options['state'] = Structure::STATE_ACTIVE;
            $user->setOptions( $options );
        }

        if ( $this->getMapper()
                  ->save( $user ) )
        {
            return $user;
        }

        return null;
    }

    /**
     * Find a user by id
     *
     * @param   int     $id
     * @return  \User\Model\User\Structure
     */
    public function find( $id )
    {
        return $this->getMapper()
                    ->find( $id );
    }

    /**
     * Find users as "$id" => "$displayName" pairs
     *
     * @param   null|string|array   $state
     * @param   null|bool           $confirmed
     * @return  array
     */
    public function findOptions( $state     = Structure::STATE_ACTIVE,
                                 $confirmed = true )
    {
        $where = array();

        if ( ! empty( $state ) )
        {
            $where['state'] = $state;
        }

        if ( null !== $confirmed )
        {
            $where['confirmed'] = (bool) $confirmed;
        }

        return $this->getMapper()
                    ->findOptions(
                        array(
                            'value'         => 'id',
                            'label'         => 'displayName',
                            'data-email'    => 'email',
                            'data-avatar'   => 'avatar',
                        ),
                        $where,
                        array(
                            'displayName'   => 'ASC',
                            'id'            => 'ASC',
                        )
                    );
    }

    /**
     * Find a user by email
     *
     * @param   string  $email
     * @return  \User\Model\User\Structure
     */
    public function findByEmail( $email )
    {
        return $this->getMapper()
                    ->findByEmail( $email );
    }

    /**
     * Find a user by display-name
     *
     * @param   string  $displayName
     * @return  \User\Model\User\Structure
     */
    public function findByDisplayName( $displayName )
    {
        return $this->getMapper()
                    ->findByDisplayName( $displayName );
    }

    /**
     * Has associated identity
     *
     * @param   int     $userId
     * @param   string  $identity
     * @return  int     Association id
     */
    public function hasAssociatedIdentity( $userId, $identity )
    {
        return $this->getMapper()
                    ->hasAssociatedIdentity( $userId, $identity );
    }

    /**
     * Associate identity
     *
     * @param   int     $userId
     * @param   string  $identity
     * @return  int     affected rows (with insert)
     */
    public function associateIdentity( $userId, $identity )
    {
        return $this->getMapper()
                    ->associateIdentity( $userId, $identity );
    }

    /**
     * Is display name available?
     *
     * @param   string  $displayName
     * @return  bool
     */
    public function isDisplayNameAvailable( $displayName )
    {
        return ! $this->getMapper()
                      ->isDisplayNameExists(
                          Structure::trimDisplayName( $displayName ), null
                      );
    }

}
