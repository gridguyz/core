<?php

namespace Grid\Core\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * Grid\Core\View\Helper\AppService
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class AppService extends AbstractHelper
              implements ServiceLocatorAwareInterface
{

    use ServiceLocatorAwareTrait
    {
        ServiceLocatorAwareTrait::setServiceLocator as protected setExactServiceLocator;
    }

    /**
     * Set service locator
     *
     * @param   ServiceLocatorInterface $serviceLocator
     * @return  AppService
     */
    public function setServiceLocator( ServiceLocatorInterface $serviceLocator )
    {
        if ( null === $this->serviceLocator )
        {
            $this->setExactServiceLocator( $serviceLocator );
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
