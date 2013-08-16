<?php

namespace Grid\User\Datasheet\Event;

use Grid\User\Datasheet\Event as DatasheetEvent;
use Grid\User\Model\User\Structure As UserStructure;

/**
 * Register
 *
 * @author Kristof Matos <kristof.matos@megaweb.hu>
 */
class Register extends DatasheetEvent
{    
    /**
     * Constructor
     *
     * @param  string|object $target
     * @param  array|ArrayAccess $params
     */
    public function __construct($target = null, $params = null)
    {
        parent::__construct(self::EVENT_REGISTER, $target, $params);
    }
    
    /**
     *
     * @var array 
     */
    protected $data;
    
    /**
     *
     * @var null|Grid\User\Model\User\Structure
     */
    protected $user;

    /**
     * 
     * @return array
     */
    public function getData() 
    {
        return $this->data;
    }

    /**
     * 
     * @param array $data
     * @return \Grid\User\Datasheet\Event
     */
    public function setData(Array $data) 
    {
        $this->data = $data;
        return $this;
    }

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


}
