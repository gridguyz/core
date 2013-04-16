<?php

namespace Grid\User\Model\User\Group;

use Zork\Model\MapperAwareTrait;
use Zork\Model\MapperAwareInterface;
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
     * @var \User\Model\Permissions\Model
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
     * @param   \User\Model\Permissions\Model $permissionsModel
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
     * @param   \User\Model\User\Group\Mapper $userMapper
     */
    public function __construct( Mapper             $userGroupMapper,
                                 PermissionsModel   $permissionsModel )
    {
        $this->setMapper( $userGroupMapper )
             ->setPermissionsModel( $permissionsModel );
    }

    /**
     * Create a group
     *
     * @param   array|\Traversable $data
     * @return  \User\Model\User\Group\Structure
     */
    public function create( $data )
    {
        return $this->getMapper()
                    ->create( $data );
    }

    /**
     * Get paginator for listing
     *
     * @return  \Zend\Paginator\Paginator
     */
    public function getPaginator()
    {
        return $this->getMapper()
                    ->getPaginator( array(
                        'id' => array_keys(
                            $this->getPermissionsModel()
                                 ->allowedUserGroups( 'view' )
                        ),
                    ) );
    }

    /**
     * Find a group by id
     *
     * @param   int $id
     * @return  \User\Model\User\Group\Structure
     */
    public function find( $id )
    {
        return $this->getMapper()
                    ->find( $id );
    }

    /**
     * Find the default group
     *
     * @return  \User\Model\User\Group\Structure
     */
    public function findDefault()
    {
        return $this->getMapper()
                    ->findDefault();
    }

    /**
     * Find users as "$id" => "$name" pairs
     *
     * @return  array
     */
    public function findOptions( $predefined = false )
    {
        return $this->getMapper()
                    ->findOptions(
                        array(
                            'value'         => 'id',
                            'label'         => 'name',
                        ),
                        $predefined === null ? array() : array(
                            'predefined'    => $predefined ? 't' : 'f',
                        ),
                        array(
                            'name'          => 'ASC',
                            'id'            => 'ASC',
                        )
                    );
    }

}
