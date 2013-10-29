<?php

namespace Grid\Core\Model\Module;

use Zork\Model\MapperAwareTrait;
use Zork\Model\MapperAwareInterface;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;
use Zork\EventManager\EventProviderAbstract;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * Model
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Model extends EventProviderAbstract
         implements MapperAwareInterface,
                    ServiceLocatorAwareInterface
{

    use MapperAwareTrait,
        ServiceLocatorAwareTrait;

    /**
     * Construct model
     *
     * @param   Mapper                  $moduleMapper
     * @param   ServiceLocatorInterface $serviceLocator
     * @param   EventManager            $eventManager
     */
    public function __construct( Mapper                     $moduleMapper,
                                 ServiceLocatorInterface    $serviceLocator,
                                 EventManager               $eventManager = null )
    {
        $this->setMapper( $moduleMapper )
             ->setServiceLocator( $serviceLocator );

        if ( null !== $eventManager )
        {
            $this->setEventManager( $eventManager );
        }
    }

    /**
     * Set the event manager instance used by this context
     *
     * @param   EventManagerInterface $events
     * @return  \User\Authentication\Service
     */
    public function setEventManager( EventManagerInterface $events )
    {
        $events->setEventClass( __NAMESPACE__ . '\ModulesEvent' )
               ->attach(
                    ModulesEvent::EVENT_MODULES,
                    array( $this, 'onModules' ),
                    1000
                );

        return parent::setEventManager( $events );
    }

    /**
     * Create a module
     *
     * @param   array   $data
     * @return  \Grid\Core\Model\SubDomain\Structure
     */
    public function create( $data )
    {
        return $this->getMapper()
                    ->create( $data );
    }

    /**
     * Find a module by id
     *
     * @param   int     $id
     * @return  \Grid\Core\Model\SubDomain\Structure
     */
    public function find( $id )
    {
        return $this->getMapper()
                    ->find( $id );
    }

    /**
     * Find a module by name
     *
     * @param   string  $name
     * @return  \Grid\Core\Model\SubDomain\Structure
     */
    public function findByName( $name )
    {
        return $this->getMapper()
                    ->findByName( $name );
    }

    /**
     * Default action on modules-set event
     *
     * @param   ModulesEvent    $event
     * @return  int
     */
    public function onModules( ModulesEvent $event )
    {
        $saved      = 0;
        $model      = $event->getTarget();
        $mapper     = $model->getMapper();
        $modules    = $event->getParams();

        foreach ( $modules as $name => $enabled )
        {
            if ( empty( $name ) )
            {
                continue;
            }

            $name   = (string) $name;
            $module = $model->findByName( $name );

            if ( empty( $module ) )
            {
                $module = $model->create( array(
                    'module'    => $name,
                    'enabled'   => $enabled,
                ) );
            }
            else
            {
                $module->enabled = $enabled;
            }

            $saved += $mapper->save( $module );
        }

        return $saved;
    }

    /**
     * Set module status (enabled / disabled)
     *
     * @param   array|\Traversable  $modules
     * @return  int
     */
    public function setModules( $modules )
    {
        $saved = 0;
        $event = new ModulesEvent(
            ModulesEvent::EVENT_MODULES,
            $this,
            $modules
        );

        $event->setServiceLocator( $this->getServiceLocator() );

        /* @var $responses \Zend\EventManager\ResponseCollection */
        $responses = $this->getEventManager()
                          ->trigger( $event );

        foreach ( $responses as $response )
        {
            $saved += (int) $response;
        }

        return $saved;
    }

}
