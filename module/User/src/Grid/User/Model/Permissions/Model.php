<?php

namespace Grid\User\Model\Permissions;

use Zend\Permissions\Acl;
use Zend\Authentication\AuthenticationService;
use Zork\Model\MapperAwareTrait;
use Zork\Model\MapperAwareInterface;
use Zork\Permissions\Acl\AclAwareTrait;
use Zork\Permissions\Acl\AclAwareInterface;
use Zork\Authentication\AuthenticationServiceAwareTrait;

/**
 * Model
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Model implements AclAwareInterface,
                       MapperAwareInterface
{

    use AclAwareTrait,
        MapperAwareTrait,
        AuthenticationServiceAwareTrait;

    /**
     * @var string
     */
    const ROLE_GUEST_ID     = '0';

    /**
     * @var string
     */
    const PRIVILEGE_DEFAULT = 'view';

    /**
     * Inner acl-role
     *
     * @var \Zend\Permissions\Acl\Role\RoleInterface
     */
    protected $role;

    /**
     * @var array
     */
    private $userGroups;

    /**
     * Construct model
     *
     * @param   Mapper                  $userPermissionsMapper
     * @param   Acl\Acl                 $acl
     * @param   AuthenticationService   $auth
     */
    public function __construct( Mapper                 $userPermissionsMapper,
                                 Acl\Acl                $acl,
                                 AuthenticationService  $auth )
    {
        $this->setMapper( $userPermissionsMapper )
             ->setAcl( $acl )
             ->setAuthenticationService( $auth );
    }

    /**
     * Get inner acl-role
     *
     * @return \Zend\Permissions\Acl\Role\RoleInterface
     */
    public function getRole()
    {
        if ( null === $this->role )
        {
            $auth = $this->getAuthenticationService();

            if ( $auth->hasIdentity() )
            {
                $this->role = $auth->getIdentity();
            }
            else
            {
                $this->role = new Acl\Role\GenericRole( self::ROLE_GUEST_ID );
            }
        }

        return $this->role;
    }

    /**
     * Set inner acl-role
     *
     * @param \Zend\Permissions\Acl\Role\RoleInterface $role
     * @return \User\Model\Permissions\Model
     */
    public function setRole( Acl\Role\RoleInterface $role = null )
    {
        $this->role = $role;
        return $this;
    }

    /**
     * Get parent id of a role / resource id
     *
     * @param string $id
     * @return string|null
     */
    protected function getParentId( $id )
    {
        $matches = array();

        if ( preg_match( '/^(.*)\.[^\.]+$/', $id, $matches ) )
        {
            return $matches[1];
        }

        return null;
    }

    /**
     * Check existence of a resource
     *
     * @param string|\Zend\Permissions\Acl\Resource\ResourceInterface $resource
     * @return string
     */
    public function checkResource( $resource, $parent = null )
    {
        if ( null === $resource )
        {
            return null;
        }

        if ( $resource instanceof Acl\Resource\ResourceInterface )
        {
            $resource = $resource->getResourceId();
        }

        $acl = $this->getAcl();

        if ( ! $acl->hasResource( $resource ) )
        {
            if ( null === $parent )
            {
                $parent = $this->getParentId( $resource );
            }
            elseif ( $parent instanceof Acl\Resource\ResourceInterface )
            {
                $parent = $parent->getResourceId();
            }

            $parent = $this->checkResource( $parent );
            $acl->addResource( $resource, $parent );
        }

        return $resource;
    }

    /**
     * Check existence of a role
     *
     * @param string|\Zend\Permissions\Acl\Role\RoleInterface $role
     * @return string
     */
    public function checkRole( $role, $parent = null )
    {
        if ( null === $role )
        {
            return null;
        }

        if ( $role instanceof Acl\Role\RoleInterface )
        {
            $role = $role->getRoleId();
        }

        $acl = $this->getAcl();

        if ( ! $acl->hasRole( $role ) )
        {
            if ( null === $parent )
            {
                $parent = $this->getParentId( $role );
            }
            elseif ( $parent instanceof Acl\Role\RoleInterface )
            {
                $parent = $parent->getRoleId();
            }

            $parent = $this->checkRole( $parent );
            $acl->addRole( $role, (array) $parent );

            $matches    = array();
            $structures = array();

            if ( preg_match( '/^(\d+)$/', $role, $matches ) ) //group
            {
                $structures = $this->getMapper()
                                   ->findAllByGroupId( $matches[1] );
            }
            else if ( preg_match( '/^(\d+)\.(\d+)$/', $role, $matches ) ) //user
            {
                $structures = $this->getMapper()
                                   ->findAllByUserId( $matches[2] );

                $resource = 'user.group.' . $matches[1] .
                            '.identity.' . $matches[2];

                $this->checkResource( $resource );
                $acl->allow( $role, $resource );
            }

            foreach ( $structures as $structure )
            {
                $this->checkResource( $structure->resource );
                $acl->allow( $role, $structure->resource, $structure->privilege );
            }
        }

        return $role;
    }

    /**
     * Is a privilege (on a resource) allowed, or not
     *
     * @param string|\Zend\Permissions\Acl\Resource\ResourceInterface $resource
     * @param string $privilege
     * @return bool
     */
    public function isAllowed( $resource, $privilege = self::PRIVILEGE_DEFAULT, $role = null )
    {
        if ( empty( $role ) )
        {
            $role = $this->getRole();
        }

        $role       = $this->checkRole( $role );
        $resource   = $this->checkResource( $resource );

        return $this->getAcl()
                    ->isAllowed( $role,
                                 $resource,
                                 $privilege ?: self::PRIVILEGE_DEFAULT );
    }

    /**
     * What extra users statisfy this 'query' (above $this->allowedUserGroups()):
     * <code>$this->isAllowed( 'user.group.*.%id%', $privilege )</code>
     *
     * @param string $privilege
     * @return array :id => :name pairs
     */
    public function allowedUsers( $privilege = self::PRIVILEGE_DEFAULT )
    {
        $auth = $this->getAuthenticationService();

        if ( $auth->hasIdentity() )
        {
            $identity = $auth->getIdentity();

            return array(
                $identity->id => $identity->displayName,
            );
        }
        else
        {
            return array();
        }
    }

    /**
     * What user groups statisfy this 'query':
     * <code>$this->isAllowed( 'user.group.%id%', $privilege )</code>
     *
     * @param string $privilege
     * @return array :id => :name pairs
     */
    public function allowedUserGroups( $privilege = self::PRIVILEGE_DEFAULT )
    {
        $result = array();

        if ( null === $this->userGroups )
        {
            $this->userGroups = $this->getMapper()
                                     ->findUserGroups();
        }

        foreach ( $this->userGroups as $id => $name )
        {
            if ( $this->isAllowed( 'user.group.' . $id, $privilege ) )
            {
                $result[$id] = $name;
            }
        }

        return $result;
    }

}
