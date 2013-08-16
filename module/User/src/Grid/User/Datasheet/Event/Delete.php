<?php

namespace Grid\User\Datasheet\Event;

use Grid\User\Datasheet\Event as DatasheetEvent;
use Grid\User\Model\User\Structure As UserStructure;

/**
 * Delete
 *
 * @author Kristof Matos <kristof.matos@megaweb.hu>
 */
class Delete extends DatasheetEvent
{    
    /**
     * Constructor
     *
     * @param  string|object $target
     * @param  array|ArrayAccess $params
     */
    public function __construct($target = null, $params = null)
    {
        parent::__construct(self::EVENT_DELETE, $target, $params);
    }
    
    /**
     *
     * @var null|Grid\User\Model\User\Structure
     */
    protected $user;

    /**
     * Event result holder
     * (is deleted)
     * 
     * @var boolen 
     */
    protected $result;
    
    /**
     * 
     * @return null|\Grid\User\Model\User\Structure
     */
    public function getUser() 
    {
        return $this->user;
    }

    /**
     * 
     * @param \Grid\User\Model\User\Structure $result
     * @return \Grid\User\Datasheet\Event
     */
    public function setUser(UserStructure $user=null) 
    {
        $this->user = $user;
        return $this;
    }

    /**
     * 
     * @return boolean
     */
    public function getResult() 
    {
        return $this->result;
    }

    /**
     * 
     * @param boolean $result
     * @return \Grid\User\Datasheet\Event
     */
    public function setResult($result) 
    {
        $this->result = $result;
        return $this;
    }

}
