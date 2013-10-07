<?php

namespace Grid\Core\Controller\Plugin;

use Iterator;
use Zork\Db\SiteInfo;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\Exception\ServiceNotFoundException;

/**
 * MimicSiteInfos controller plugin
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class MimicSiteInfosIterator extends Iterator
                          implements ServiceManagerAwareInterface
{

    use MimicSiteInfosTrait;

    /**
     * @var bool
     */
    protected $isMultisite;

    /**
     * @var \Zend\Db\Adapter\Adapter
     */
    protected $dbAdapter;

    /**
     * @var array
     */
    protected $servicesBefore;

    /**
     * @var SiteInfo|null
     */
    protected $currentSiteInfo;

    /**
     * @var \Zend\Db\Adapter\Driver\ResultInterface
     */
    protected $queryResult;

    /**
     * @var array
     */
    protected $queriedSchemas = array();

    /**
     * Constructor
     *
     * @param   ServiceManager  $serviceManager
     */
    public function __construct( ServiceManager $serviceManager )
    {
        $this->setServiceManager( $serviceManager );
        $this->servicesBefore = ServicesCopy::getServiceInstances( $serviceManager );
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        $this->restoreServices();
    }

    /**
     * Restore service instances
     *
     * @return  void
     */
    protected function restoreServices()
    {
        ServicesCopy::setServiceInstances(
            $this->getServiceManager(),
            $this->servicesBefore
        );
    }

    /**
     * Set SiteInfo
     *
     * @param   SiteInfo    $siteInfo
     * @return  MimicSiteInfosIterator
     */
    protected function setSiteInfo( $siteInfo )
    {
        $setAllowOverride   = false;
        $serviceManager     = $this->getServiceManager();

        if ( $serviceManager->has( 'SiteInfo' ) &&
             ! $serviceManager->getAllowOverride() )
        {
            $serviceManager->setAllowOverride( $setAllowOverride = true );
        }

        $serviceManager->setService( 'SiteInfo', $siteInfo );

        if ( $setAllowOverride )
        {
            $serviceManager->setAllowOverride( false );
        }

        return $siteInfo;
    }

    /**
     * Get db adapter
     *
     * @return  \Zend\Db\Adapter\Adapter
     */
    protected function getDbAdapter()
    {
        if ( null === $this->dbAdapter )
        {
            $this->dbAdapter = $this->getServiceManager()
                                    ->get( 'Zend\Db\Adapter\Adapter' );
        }

        return $this->dbAdapter;
    }

    /**
     * Is multisite
     *
     * @return  bool
     */
    protected function isMultisite()
    {
        if ( null === $this->isMultisite )
        {
            $this->isMultisite = in_array(
                'Grid\MultisitePlatform',
                $this->getServiceManager()
                     ->get( 'Zend\ModuleManager\ModuleManagerInterface' )
                     ->getModules()
            );
        }

        return $this->isMultisite;
    }

    /**
     * Create SiteInfo
     *
     * @param   array       $data
     * @return  SiteInfo
     */
    protected function createSiteInfo( $data )
    {
        if ( $this->isMultisite() )
        {
            return new SiteInfo( $data );
        }
        else
        {
            $db         = $this->getDbAdapter();
            $driver     = $db->getDriver();
            $schema     = $db->getCurrentSchema();
            $config     = $driver->getConnection()->getConnectionParameters();
            $domain     = empty( $config['defaultDomain'] ) ? php_uname( 'n' ) : $config['defaultDomain'];
            $subdomain  = empty( $data['subdomain'] ) ? '' : $data['subdomain'];
            $fulldomain = ( $subdomain ? $subdomain . '.' : '' ) . $domain;

            return new SiteInfo( array(
                'schema'        => $schema,
                'domain'        => $domain,
                'subdomain'     => $subdomain,
                'subdomainId'   => empty( $data['id'] ) ? null : $data['id'],
                'fulldomain'    => $fulldomain,
            ) );
        }
    }

    /**
     * Create current SiteInfo
     *
     * @param   array   $data
     * @return  SiteInfo
     */
    protected function createCurrentSiteInfo( $data )
    {
        while ( $this->queryResult->valid() )
        {
            $data       = $this->queryResult->current();
            $current    = $this->createSiteInfo( $data );
            $schema     = $current->getSchema();

            if ( ! empty( $this->queriedSchemas[$schema] ) )
            {
                $this->queryResult->next();
            }
            else
            {
                $this->queriedSchemas[$schema] = true;
                return $this->setSiteInfo( $this->currentSiteInfo = $current );
            }
        }

        $this->currentSiteInfo = null;
        return null;
    }

    /**
     * Rewind the Iterator to the first element
     *
     * @link    http://php.net/manual/en/iterator.rewind.php
     * @return  void
     */
    public function rewind()
    {
        $this->restoreServices();

        $db         = $this->getDbAdapter();
        $platform   = $db->getPlatform();
        $driver     = $db->getDriver();

        if ( $this->isMultisite() )
        {
            $query = $db->query( '
                SELECT *
                  FROM ' . $platform->quoteIdentifier( '_central' ) .
                     '.' . $platform->quoteIdentifier( 'fulldomain' ) . '
                 WHERE ' . $platform->quoteIdentifier( 'subdomain' ) . '
                     = ' . $driver->formatParameterName( 'subdomain' ) . '
            ' );
        }
        else
        {
            $query = $db->query( '
                SELECT *
                  FROM ' . $platform->quoteIdentifier( 'subdomain' ) . '
                 WHERE ' . $platform->quoteIdentifier( 'subdomain' ) . '
                     = ' . $driver->formatParameterName( 'subdomain' ) . '
            ' );
        }

        $this->queriedSchemas   = array();
        $this->queryResult      = $query->execute( array(
            'subdomain' => '',
        ) );

        $this->queryResult->rewind();
        return $this->createCurrentSiteInfo();
    }

    /**
     * Move forward to next element
     *
     * @link    http://php.net/manual/en/iterator.next.php
     * @return  void
     */
    public function next()
    {
        if ( ! $this->queryResult ||
             ! $this->queryResult->getAffectedRows() )
        {
            $this->currentSiteInfo = null;
            return null;
        }

        $this->restoreServices();
        $this->queryResult->next();
        return $this->createCurrentSiteInfo();
    }

    /**
     * Checks if current position is valid
     *
     * @link    http://php.net/manual/en/iterator.valid.php
     * @return  bool
     */
    public function valid()
    {
        return $this->queryResult
            && $this->queryResult->getAffectedRows()
            && $this->currentSiteInfo instanceof SiteInfo;
    }

    /**
     * Return the key of the current element
     *
     * @link    http://php.net/manual/en/iterator.key.php
     * @return  scalar
     */
    public function key()
    {
        return $this->valid() ? $this->currentSiteInfo->getSchema() : null;
    }

    /**
     * Return the current element
     *
     * @link    http://php.net/manual/en/iterator.current.php
     * @return  SiteInfo
     */
    public function current()
    {
        return $this->valid() ? $this->currentSiteInfo : null;
    }

}
