<?php

namespace Grid\Core\Model\ContentUri;

use Zork\Factory\Builder;
use Zork\Factory\FactoryAbstract;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * ContentUri Factory
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Factory extends FactoryAbstract
           implements ServiceLocatorAwareInterface
{

    use ServiceLocatorAwareTrait;

    /**
     * Constructor
     *
     * @param   Builder                 $factoryBuilder
     * @param   ServiceLocatorInterface $serviceLocator
     */
    public function __construct( Builder $factoryBuilder,
                                 ServiceLocatorInterface $serviceLocator )
    {
        parent::__construct( $factoryBuilder );
        $this->setServiceLocator( $serviceLocator );
    }

    /**
     * Factory an object
     *
     * @param   string|object|array $adapter
     * @param   object|array|null   $options
     * @return  AdapterInterface
     */
    public function factory( $adapter, $options = null )
    {
        $adapter = parent::factory( $adapter, $options );

        if ( $adapter instanceof ServiceLocatorAwareInterface )
        {
            $adapter->setServiceLocator( $this->getServiceLocator() );
        }

        return $adapter;
    }

}
