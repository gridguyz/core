<?php

namespace Grid\User\Datasheet\Event;

use Grid\User\Datasheet\Event as DatasheetEvent;
use Grid\User\Model\User\Structure As UserStructure;
use Zork\Form\Form As ZorkForm;

/**
 * Form
 *
 * @author Kristof Matos <kristof.matos@megaweb.hu>
 */
class Form extends DatasheetEvent
{    
    
    /**
     * Constructor
     *
     * @param  string|object $target
     * @param  array|ArrayAccess $params
     */
    public function __construct($target = null, $params = null)
    {
        parent::__construct(self::EVENT_FORM, $target, $params);
    }
    
    /**
     *
     * @var Grid\User\Model\User\Structure
     */
    protected $user;

    /**
     * 
     * @var \Zork\Form\Form 
     */
    protected $form;
    
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
     * @param null|\Grid\User\Model\User\Structure $form
     * @return \Grid\User\Datasheet\Event
     */
    public function setUser(UserStructure $user=null) 
    {
        $this->user = $user;
        return $this;
    }

    /**
     * 
     * @return null|\Zork\Form\Form
     */
    public function getForm() 
    {
        return $this->form;
    }

    /**
     * 
     * @param \Zork\Form\Form $form
     * @return \Grid\User\Datasheet\Event\Form
     */
    public function setForm(ZorkForm &$form) 
    {
        $this->form = $form;
        return $this;
    }

}
