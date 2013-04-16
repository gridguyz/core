<?php

namespace Grid\User\Model\User\Group;

use Zend\Permissions\Acl;
use Zork\Model\Structure\MapperAwareAbstract;

/**
 * Structure
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Structure extends MapperAwareAbstract
             implements Acl\Role\RoleInterface,
                        Acl\Resource\ResourceInterface
{

    /**
     * Field: id
     *
     * @var int
     */
    protected $id;

    /**
     * Field: name
     *
     * @var string
     */
    public $name;

    /**
     * Field: predefined
     *
     * @var bool
     */
    protected $predefined   = false;

    /**
     * Field: default
     *
     * @var bool
     */
    protected $default      = false;

    /**
     * Is this group a predefined group?
     *
     * @return bool
     */
    public function isPredefined()
    {
        return $this->predefined;
    }

    /**
     * Is this group the default group?
     * (used on registration)
     *
     * @return bool
     */
    public function isDefault()
    {
        return $this->default;
    }

    /**
     * Is this group a removeable group?
     * (not predefined, nor default)
     *
     * @return bool
     */
    public function isRemoveable()
    {
        return ! ( $this->isPredefined() || $this->isDefault() );
    }

    /**
     * Set to default (or not default)
     *
     * @param bool $default
     * @return \User\Model\User\Group\Structure
     */
    public function setDefault( $default = true )
    {
        $this->default = (bool) $default;
        return $this;
    }

    /**
     * Returns the string identifier of the Role
     *
     * @return string
     */
    public function getRoleId()
    {
        return (string) (int) $this->id;
    }

    /**
     * Returns the string identifier of the Role
     *
     * @return string
     */
    public function getResourceId()
    {
        return 'user.group.' . ( (int) $this->id );
    }

}
