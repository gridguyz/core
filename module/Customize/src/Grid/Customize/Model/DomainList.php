<?php

namespace Grid\Customize\Model;

use ArrayIterator;
use IteratorAggregate;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * DomainList
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class DomainList implements IteratorAggregate, ServiceLocatorAwareInterface
{

    use ServiceLocatorAwareTrait;

    /**
     * @var ArrayIterator
     */
    protected $domainIterator;

    /**
     * Construct serviceLocator
     *
     * @param   ServiceLocatorInterface $serviceLocator
     */
    public function __construct( ServiceLocatorInterface $serviceLocator )
    {
        $this->setServiceLocator( $serviceLocator );
    }

    /**
     * Retrieve an external iterator
     *
     * @link    http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return  Traversable
     */
    public function getIterator()
    {
        if ( null === $this->domainIterator )
        {
            /* @var $siteInfo \Zork\Db\SiteInfo */
            /* @var $moduleManager \Zend\ModuleManager\ModuleManagerInterface */
            $serviceLocator = $this->getServiceLocator();
            $siteInfo       = $serviceLocator->get( 'Zork\Db\SiteInfo' );
            $siteId         = $siteInfo->getSiteId();
            $moduleManager  = $serviceLocator->get(
                'Zend\ModuleManager\ModuleManagerInterface'
            );

            if ( $siteId && in_array( 'Grid\MultisitePlatform',
                                      $moduleManager->getModules() ) )
            {
                /* @var $domainModel \Grid\MultisitePlatform\Model\Domain\Model */
                $domains        = array();
                $domainModel    = $serviceLocator->get(
                    'Grid\MultisitePlatform\Model\Domain\Model'
                );

                foreach ( $domainModel->findAll( $siteId ) as $domain )
                {
                    if ( ! empty( $domain->domain ) )
                    {
                        $domains[] = $domain->domain;
                    }
                }
            }
            else
            {
                /* @var $dbAdapter \Zend\Db\Adapter\Adapter */
                $dbAdapter  = $serviceLocator->get( 'Zend\Db\Adapter\Adapter' );
                $config     = $dbAdapter->getDriver()
                                        ->getConnection()
                                        ->getConnectionParameters();

                if ( empty( $config['validDomains'] ) )
                {
                    $domains = array( 'localhost' );
                }
                else
                {
                    $domains = array_map(
                        'strtolower',
                        (array) $config['validDomains']
                    );
                }

                if ( ! empty( $config['defaultDomain'] ) )
                {
                    $defaultDomain = strtolower( $config['defaultDomain'] );

                    if ( ! in_array( $defaultDomain, $domains ) )
                    {
                        array_unshift( $domains, $defaultDomain );
                    }
                }
            }

            $domain = $siteInfo->getDomain();

            if ( ! in_array( $domain, $domains ) )
            {
                array_unshift( $domains, $domain );
            }

            $this->domainIterator = new ArrayIterator( $domains );
        }

        return $this->domainIterator;
    }

}
