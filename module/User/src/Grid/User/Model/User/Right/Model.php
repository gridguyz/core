<?php

namespace Grid\User\Model\User\Right;

use Zork\Model\MapperAwareTrait;
use Zork\Model\MapperAwareInterface;

/**
 * Model
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Model implements MapperAwareInterface
{

    use MapperAwareTrait;

    /**
     * Construct model
     *
     * @param \User\Model\User\Right\Mapper $userRightMapper
     */
    public function __construct( Mapper $userRightMapper )
    {
        $this->setMapper( $userRightMapper );
    }

    /**
     * Create a group
     *
     * @param array|\Traversable $data
     * @return \User\Model\User\Group\Structure
     */
    public function create( $data )
    {
        return $this->getMapper()
                    ->create( $data );
    }

    /**
     * Find a right by id
     *
     * @param int $id
     * @return \User\Model\User\Group\Structure
     */
    public function find( $id )
    {
        return $this->getMapper()
                    ->find( $id );
    }

    /**
     * Get all rights and granted flags to a user
     *
     * @param   int     $userId
     * @return  array
     */
    public function findAllByUser( $userId, $where=array() )
    {
        return $this->getMapper()
                    ->findAllByUser( $userId, $where );
    }

    /**
     * Get all rights and granted flags to a group
     *
     * @param   int     $groupId
     * @return  array
     */
    public function findAllByGroup( $groupId, $where=array() )
    {
        return $this->getMapper()
                    ->findAllByGroup( $groupId, $where );
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
        return $this->getMapper()
                    ->grantToUser( $rightId, $userId, $grant );
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
        return $this->getMapper()
                    ->grantToGroup( $rightId, $groupId, $grant );
    }

}
