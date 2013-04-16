<?php

namespace Grid\User\Model\Permissions;

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
    public $label       = '';
    
    /**
     * Field: group
     * 
     * @var string
     */
    public $group       = '';
    
    /**
     * Field: resource
     * 
     * @var string
     */
    public $resource    = '';
    
    /**
     * Field: privilege
     * 
     * @var string
     */
    public $privilege   = '';
    
    /**
     * Field: optional
     * 
     * @var bool
     */
    protected $optional = false;
    
    /**
     * Required
     * 
     * @return bool
     */
    public function getRequired()
    {
        return ! $this->optional;
    }
    
    /**
     * Is required
     * 
     * @return bool
     */
    public function isRequired()
    {
        return $this->required;
    }
    
    /**
     * Is optional
     * 
     * @return bool
     */
    public function isOptional()
    {
        return $this->optional;
    }
    
    /**
     * Set optional flag
     * 
     * @param bool $flag
     * @return \User\Model\Permissions\Structure
     */
    public function setOptional( $flag = true )
    {
        $this->optional = (bool) $flag;
        return $this;
    }
    
    /**
     * Set required flag
     * 
     * @param bool $flag
     * @return \User\Model\Permissions\Structure
     */
    public function setRequired( $flag = true )
    {
        $this->optional = ! $flag;
        return $this;
    }
    
}
