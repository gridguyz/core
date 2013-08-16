<?php

namespace Grid\User\Datasheet;

use Zend\EventManager\EventManager;
use Zork\EventManager\EventProviderAbstract;
use Grid\User\Model\User\Structure AS UserStructure;
use Zork\Form\Form As ZorkForm;

/**
 * Service
 * 
 * Datasheet change service
 * 
 * @author Kristof Matos <kristof.matos@megaweb.hu>
 */
class Service extends EventProviderAbstract 
{
    /**
     * Constructor
     * 
     * @param \Zend\EventManager\EventManager $eventManager
     */
    public function __construct( EventManager $eventManager = null )
    {
        if ( !is_null($eventManager) )
        {
             $this->setEventManager( $eventManager );
        }
    }
    
    /**
     * User form
     * 
     * @trigger Event::EVENT_FORM
     * @param \Zork\Form\Form $form
     * @param \Grid\User\Model\User\Structure $user
     * @return type
     */
    public function form(ZorkForm &$form,UserStructure $user)
    {
        $event = new Event\Form();
        $event->setForm($form);
        $event->setUser($user);
        $this->getEventManager()->trigger($event);
        return $event->getForm();   
    }
    /**
     * User register
     * 
     * @trigger Event::EVENT_REGISTER
     * @param array $data
     * @return \Grid\User\Model\User\Structure
     */
    public function register(Array $data)
    {
        $event = new Event\Register();
        $event->setData($data);
        $this->getEventManager()->trigger($event);
        return $event->getUser();
    }
    
    /**
     * User save
     * 
     * @trigger Event::EVENT_SAVE
     * @param \Grid\User\Model\User\Structure $user
     * @param array $data
     * @return boolean
     */
    public function save(UserStructure $user)
    {
        $event = new Event\Save();
        $event->setUser($user);
        $this->getEventManager()->trigger($event);
        return $event->getResult();
    }

    /**
     * User delete
     * 
     * @trigger Event::EVENT_DELETE
     * @param \Grid\User\Model\User\Structure $user
     * @return boolean
     */
    public function delete(UserStructure $user)
    {
        $event = new Event\Delete();
        $event->setUser($user);
        $this->getEventManager()->trigger($event);
        return $event->getResult();  
    }
    
}            