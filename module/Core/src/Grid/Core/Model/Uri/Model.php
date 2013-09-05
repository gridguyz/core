<?php

namespace Grid\Core\Model\Uri;

use Zork\Model\MapperAwareTrait;
use Zork\Model\MapperAwareInterface;

/**
 * Model
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 * @method \Core\Model\Uri\Mapper getMapper()
 */
class Model implements MapperAwareInterface
{

    use MapperAwareTrait;

    /**
     * Construct model
     *
     * @param   \Grid\Core\Model\Uri\Mapper $userMapper
     */
    public function __construct( Mapper $uriMapper )
    {
        $this->setMapper( $uriMapper );
    }

    /**
     * Find a seo-friendy uri by id
     *
     * @param   int     $id
     * @return  \Grid\Core\Model\Uri\Structure
     */
    public function find( $id )
    {
        return $this->getMapper()
                    ->find( $id );
    }

    /**
     * Create a seo-friendy uri
     *
     * @param   int     $data
     * @return  \Grid\Core\Model\Uri\Structure
     */
    public function create( $data )
    {
        return $this->getMapper()
                    ->create( $data );
    }

    /**
     * Get uri (structure) by subdomain & uri
     *
     * @param   int     $subdomainId
     * @param   string  $uri
     * @return  \Grid\Core\Model\Uri\Structure
     */
    public function findBySubdomainUri( $subdomainId, $uri )
    {
        return $this->getMapper()
                    ->findBySubdomainUri( $subdomainId, $uri );
    }

    /**
     * Get default by content & subdomain
     *
     * @param   int             $contentId
     * @param   int             $subdomainId
     * @param   string|array    $locales
     * @return  \Grid\Core\Model\Uri\Structure|null
     */
    public function findDefaultByContentSubdomain( $contentId,
                                                   $subdomainId,
                                                   $locales = null )
    {
        return $this->getMapper()
                    ->findDefaultByContentSubdomain(
                        $contentId,
                        $subdomainId,
                        $locales
                    );
    }

    /**
     * Get default by content & locale
     *
     * @param   int         $contentId
     * @param   string      $locale
     * @param   int|null    $preferredSubdomainId
     * @return  \Grid\Core\Model\Uri\Structure|null
     */
    public function findDefaultByContentLocale( $contentId,
                                                $locale,
                                                $preferredSubdomainId = null )
    {
        return $this->getMapper()
                    ->findDefaultByContentLocale(
                        $contentId,
                        $locale,
                        $preferredSubdomainId
                    );
    }

    /**
     * Return true, if uri is exists in a subdomain
     *
     * @param   int             $subdomainId
     * @param   string          $uri
     * @param   array|object    $params
     * @return  bool
     */
    public function isSubdomainUriExists( $subdomainId, $uri, $params = array() )
    {
        $params = (object) $params;

        return $this->getMapper()
                    ->isSubdomainUriExists(
                        $subdomainId,
                        $uri,
                        empty( $params->id ) ? null : $params->id
                    );
    }

    /**
     * Get paginator for listing
     *
     * @return  \Zend\Paginator\Paginator
     */
    public function getPaginator()
    {
        return $this->getMapper()
                    ->getPaginator();
    }

}
