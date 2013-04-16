<?php

namespace Grid\User\Model\User\Right;

use Zork\Model\Structure\MapperAwareAbstract;

/**
 * Structure
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Structure extends MapperAwareAbstract
{

    /**
     * Field: id
     *
     * @var int
     */
    protected $id;

    /**
     * Field: label
     *
     * @var string
     */
    public $label;

    /**
     * Field: group
     *
     * @var string
     */
    public $group;

    /**
     * Field: resource
     *
     * @var string
     */
    public $resource;

    /**
     * Field: privilege
     *
     * @var string
     */
    public $privilege;

    /**
     * Field: optional
     *
     * @var bool
     */
    protected $optional = true;

    /**
     * Field: module
     *
     * @var string|null
     */
    protected $module;

    /**
     * Field: granted
     *
     * @var bool
     */
    private $_granted = false;

    /**
     * Is this right optional?
     *
     * @return bool
     */
    public function isOptional()
    {
        return $this->optional;
    }

    /**
     * Set to optional (or not optional)
     *
     * @param   bool $optional
     * @return  \User\Model\User\Right\Structure
     */
    public function setOptional( $optional = true )
    {
        $this->optional = (bool) $optional;
        return $this;
    }

    /**
     * Set module dependenies
     *
     * @param   string|null $module
     * @return  \User\Model\User\Right\Structure
     */
    public function setModule( $module )
    {
        $this->module = ( (string) $module ) ?: null;
        return $this;
    }

    /**
     * Is this right granted?
     *
     * @return bool
     */
    public function isGranted()
    {
        return $this->_granted;
    }

    /**
     * Is this right granted?
     *
     * @return bool
     */
    public function getGranted()
    {
        return $this->_granted;
    }

    /**
     * Set to granted (or not granted)
     *
     * @param   bool $granted
     * @return  \User\Model\User\Right\Structure
     */
    public function setGranted( $granted = true )
    {
        $this->_granted = (bool) $granted;
        return $this;
    }

}
