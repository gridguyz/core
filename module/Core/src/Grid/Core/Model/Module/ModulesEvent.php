<?php

namespace Grid\Core\Model\Module;

use Traversable;
use ArrayAccess;
use Zend\EventManager\Event;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * ModulesEvent
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 * @method Model getTarget()
 */
class ModulesEvent extends Event
                implements ServiceLocatorAwareInterface
{

    use ServiceLocatorAwareTrait;

    /**
     * @const string
     */
    const EVENT_MODULES = 'modules';

    /**
     * Set parameters
     *
     * Overwrites parameters
     *
     * @param   array|ArrayAccess|Traversable|object $params
     * @return  ModulesEvent
     */
    public function setParams( $params )
    {
        if ( $params instanceof Traversable )
        {
            $params = iterator_to_array( $params );
        }
        else if ( is_object( $params ) && ! $params instanceof ArrayAccess )
        {
            $params = (array) $params;
        }

        return parent::setParams( $params );
    }

}
