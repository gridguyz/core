<?php

namespace Grid\User\Model\Authentication;

use Grid\User\Model\User\Model;
use Zork\Factory\Builder;
use Zork\Factory\FactoryAbstract;
use Zork\Model\ModelAwareTrait;
use Zork\Model\ModelAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zork\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * \User\Model\Authentication\AdapterFactory
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class AdapterFactory extends FactoryAbstract
                  implements ModelAwareInterface
{

    use ModelAwareTrait,
        ServiceLocatorAwareTrait;

    /**
     * Constructor
     *
     * @param \Zork\Factory\Builder                         $factoryBuilder
     * @param \User\Model\User\Mapper                       $userMapper
     * @param \Zend\ServiceManager\ServiceLocatorInterface  $serviceLocator
     */
    public function __construct( Builder                    $factoryBuilder,
                                 Model                      $userModel,
                                 ServiceLocatorInterface    $serviceLocator )
    {
        parent::__construct( $factoryBuilder );
        $this->setModel( $userModel )
             ->setServiceLocator( $serviceLocator );
    }

    /**
     * Factory an object
     *
     * @param string|object|array $adapter
     * @param object|array|null $options
     * @return \Zork\Factory\AdapterInterface
     */
    public function factory( $adapter, $options = null )
    {
        $adapter = parent::factory( $adapter, $options );
        $adapter->setModel( $this->getModel() );

        if ( $adapter instanceof ServiceLocatorAwareInterface )
        {
            $adapter->setServiceLocator( $this->getServiceLocator() );
        }

        return $adapter;
    }

}
