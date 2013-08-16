<?php

namespace Grid\User\Datasheet;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zork\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * EvenetHandler
 * handling datasheet events
 *
 * @author Kristof Matos <kristof.matos@megaweb.hu>
 */
class EventHandler implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;
    
    /**
     * Constructor
     * 
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     */
    public function __construct(ServiceLocatorInterface $serviceLocator) 
    {
        $this->setServiceLocator($serviceLocator);
    }
    
    /**
     * Handles register event
     * 
     * @param \Grid\User\Datasheet\Event\Register $event
     * @return \Grid\User\Datasheet\Event\Register
     */
    public function onRegister(Event\Register $event)
    {
        $userModel = $this->getServiceLocator()
                          ->get('Grid\User\Model\User\Model');
        $user      = $userModel->register( $event->getData() );
        $event->setUser($user);
        if( is_null($event->getUser()) )
        {
            $event->stopPropagation(true);
        }
        return $event;
    }
    
    /**
     * Handles save event
     * 
     * @param \Grid\User\Datasheet\Event\Save $event
     * @return \Grid\User\Datasheet\Event\Save
     */
    public function onSave(Event\Save $event)
    {
        $success = $event->getUser()->save();
        $event->setResult( (bool)$success );
        if( $event->getResult() === false )
        {
            $event->stopPropagation(true);
        }
        return $event;
    }
    
    /**
     * Handles delete event
     * 
     * @param \Grid\User\Datasheet\Event\Delete $event
     * @return \Grid\User\Datasheet\Event\Delete
     */
    public function onDelete(Event\Delete $event)
    {
        $success = $event->getUser()->delete();
        $event->setResult( (bool)$success );
        if( $event->getResult() === false )
        {
            $event->stopPropagation(true);
        }
        return $event;
    }

}