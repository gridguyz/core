<?php

namespace Grid\Paragraph\Mvc\View\Http;

use Zend\Mvc\MvcEvent;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;

class MiddleLayoutExceptionStrategy implements ListenerAggregateInterface
{
    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * Attach the aggregate to the specified event manager
     *
     * @param  EventManagerInterface $events
     * @return void
     */
    public function attach( EventManagerInterface $events )
    {
        $this->listeners[] = $events->attach(
            MvcEvent::EVENT_DISPATCH_ERROR,
            array( $this, 'setMiddleLayoutToViewModel' ),
            -200
        );
    }

    /**
     * Detach aggregate listeners from the specified event manager
     *
     * @param  EventManagerInterface $events
     * @return void
     */
    public function detach( EventManagerInterface $events )
    {
        foreach ( $this->listeners as $index => $listener )
        {
            if ( $events->detach( $listener ) )
            {
                unset( $this->listeners[$index] );
            }
        }
    }

    /**
     * @param  MvcEvent $event
     * @return void
     */
    public function setMiddleLayoutToViewModel( MvcEvent $event )
    {
        $error              = $event->getError();
        $layoutViewModel    = $event->getViewModel();

        if ( $error && $layoutViewModel->getVariable( 'middleLayout' ) === null )
        {
            $service = $event->getApplication()->getServiceManager();
            $model   = $service->get( 'Grid\Paragraph\Model\Paragraph\MiddleLayoutModel' );

            $middleLayout = $model->findMiddleParagraphLayoutById();

            if ( $middleLayout !== false )
            {
                $layoutViewModel->setVariable( 'middleLayout', $middleLayout );
            }
        }
    }

}

