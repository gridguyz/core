<?php

namespace Grid\User\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Grid\User\Model\Permissions\Model as PermissionsModel;

/**
 * IsAllowed
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class IsAllowed extends AbstractHelper
{

    /**
     * @var \User\Model\Permissions\Model
     */
    protected $permissionsModel;

    /**
     * @return \User\Model\Permissions\Model
     */
    public function getPermissionsModel()
    {
        return $this->permissionsModel;
    }

    /**
     * @param \User\Model\Permissions\Model $userPermissionsModel
     * @return \User\View\Helper\IsAllowed
     */
    public function setPermissionsModel( PermissionsModel $userPermissionsModel )
    {
        $this->permissionsModel = $userPermissionsModel;
        return $this;
    }

    /**
     * Constructor
     *
     * @param \User\Model\Permissions\Model $userPermissionsModel
     */
    public function __construct( PermissionsModel $userPermissionsModel )
    {
        $this->setPermissionsModel( $userPermissionsModel );
    }

    /**
     * Is a permission allowed or not
     *
     * @param   string|\Zend\Permissions\Acl\Resource\ResourceInterface $resource
     * @param   string                                                  $privilege
     * @param   string|\Zend\Permissions\Acl\Role\RoleInterface         $role
     * @return  bool
     */
    public function isAllowed( $resource, $privilege = null, $role = null )
    {
        return $this->getPermissionsModel()
                    ->isAllowed( $resource, $privilege, $role );
    }

    /**
     * Is a permission allowed or not
     *
     * @param   string|\Zend\Permissions\Acl\Resource\ResourceInterface $resource
     * @param   string                                                  $privilege
     * @param   string|\Zend\Permissions\Acl\Role\RoleInterface         $role
     * @return  bool
     */
    public function __invoke( $resource, $privilege = null, $role = null )
    {
        return $this->isAllowed( $resource, $privilege, $role );
    }

}
