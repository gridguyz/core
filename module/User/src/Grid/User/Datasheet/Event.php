<?php

namespace Grid\User\Datasheet;

use Zend\EventManager\Event as ZendEvent;

/**
 * Event
 *
 * @author Kristof Matos <kristof.matos@megaweb.hu>
 */
abstract class Event extends ZendEvent
{
    /**
     * @const string
     */
    const EVENT_REGISTER = 'register';
    
    /**
     * @const string
     */
    const EVENT_SAVE     = 'save';

    /**
     * @const string
     */
    const EVENT_DELETE   = 'delete';
  
   /**
     * @const string
     */
    const EVENT_FORM     = 'form';
    
    


}
