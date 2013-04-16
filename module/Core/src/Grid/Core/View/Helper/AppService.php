<?php

namespace Grid\Core\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * Grid\Core\View\Helper\AppService
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class AppService extends AbstractHelper
              implements ServiceLocatorAwareInterface
{

    /**
     * @var \Zend\ServiceManager\ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * @return \Zend\ServiceManager\ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return \Core\View\Helper\AppService
     */
    public function setServiceLocator( ServiceLocatorInterface $serviceLocator )
    {
        if ( null === $this->serviceLocator )
        {
            $this->serviceLocator = $serviceLocator;
        }

        return $this;
    }

    /**
     * Constructor
     *
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     */
    public function __construct( ServiceLocatorInterface $serviceLocator )
    {
        $this->setServiceLocator( $serviceLocator );
    }

    /**
     * Invokable helper
     *
     * @param  string|null $service
     * @return \Zend\ServiceManager\ServiceLocatorInterface|mixed
     */
    public function __invoke( $service = null )
    {
        $locator = $this->getServiceLocator();

        if ( null === $service )
        {
            return $locator;
        }
        else
        {
            return $locator->get( $service );
        }
    }

}
