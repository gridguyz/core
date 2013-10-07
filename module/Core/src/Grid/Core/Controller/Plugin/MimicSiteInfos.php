<?php

namespace Grid\Core\Controller\Plugin;

use IteratorAggregate;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * MimicSiteInfos controller plugin
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class MimicSiteInfos extends AbstractPlugin
                  implements IteratorAggregate,
                             ServiceManagerAwareInterface
{

    use MimicSiteInfosTrait;

    /**
     * Constructor
     *
     * @param   ServiceManager  $serviceManager
     */
    public function __construct( ServiceManager $serviceManager )
    {
        $this->setServiceManager( $serviceManager );
    }

    /**
     * Retrieve an external iterator
     *
     * @link    http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return  \Iterator
     */
    public function getIterator()
    {
        return new MimicSiteInfosIterator( $this->getServiceManager() );
    }

    /**
     * Invokable plugin
     *
     * @return \Iterator
     */
    public function __invoke()
    {
        return $this->getIterator();
    }

}
