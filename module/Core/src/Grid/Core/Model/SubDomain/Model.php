<?php

namespace Grid\Core\Model\SubDomain;

use Zork\Model\MapperAwareTrait;
use Zork\Model\MapperAwareInterface;
use Zork\Db\SiteInfo;
use Zork\Db\SiteInfoAwareTrait;
use Zork\Db\SiteInfoAwareInterface;

/**
 * Model
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Model implements MapperAwareInterface,
                       SiteInfoAwareInterface
{

    use MapperAwareTrait,
        SiteInfoAwareTrait;

    /**
     * @var \Core\Model\SubDomain\Structure
     */
    private $actual;

    /**
     * Construct model
     *
     * @param \Core\Model\SubDomain\Mapper $subDomainMapper
     * @param \Zork\Db\SiteInfo $siteInfo
     */
    public function __construct( Mapper $subDomainMapper, SiteInfo $siteInfo )
    {
        $this->setMapper( $subDomainMapper )
             ->setSiteInfo( $siteInfo );
    }

    /**
     * Create a sub-domain
     *
     * @param  array $data
     * @return \Core\Model\SubDomain\Structure
     */
    public function create( $data )
    {
        return $this->getMapper()
                    ->create( $data );
    }

    /**
     * Find a sub-domain by id
     *
     * @param int $id
     * @return \Core\Model\SubDomain\Structure
     */
    public function find( $id )
    {
        return $this->getMapper()
                    ->find( $id );
    }

    /**
     * Find the current actual sub-domain
     *
     * @return \Core\Model\SubDomain\Structure
     */
    public function findActual()
    {
        if ( null === $this->actual )
        {
            $this->actual = $this->find( $this->getSiteInfo()
                                              ->getSubdomainId() );
        }

        return $this->actual;
    }

    /**
     * Find subdomains as id => subdomain pairs
     *
     * @return array
     */
    public function findOptions()
    {
        return $this->getMapper()
                    ->findOptions( array(
                        'value' => 'id',
                        'label' => 'subdomain',
                    ) );
    }

    /**
     * Get paginator for listing
     *
     * @return \Zend\Paginator\Paginator
     */
    public function getPaginator()
    {
        return $this->getMapper()
                    ->getPaginator();
    }

}
