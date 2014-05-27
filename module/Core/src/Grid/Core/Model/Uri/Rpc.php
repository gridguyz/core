<?php

namespace Grid\Core\Model\Uri;

use Zork\Rpc\CallableTrait;
use Zork\Rpc\CallableInterface;
use Zork\Model\MapperAwareTrait;
use Zork\Model\MapperAwareInterface;

/**
 * Rpc
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Rpc implements CallableInterface,
                     MapperAwareInterface
{

    use CallableTrait,
        MapperAwareTrait;

    /**
     * Construct rpc
     *
     * @param \Core\Model\Uri\Mapper $uriMapper
     */
    public function __construct( Mapper $uriMapper )
    {
        $this->setMapper( $uriMapper );
    }

    /**
     * Return true, if uri is available in a subdomain
     *
     * @param   string          $uri
     * @param   array|object    $params subdomainId, [id]
     * @return  bool
     */
    public function isUriAvailable( $uri, $params = array() )
    {
        $params = (object) $params;

        if ( empty( $params->subdomainId ) )
        {
            return false;
        }

        return ! $this->getMapper()
                      ->isSubdomainUriExists(
                          $params->subdomainId,
                          $uri,
                          empty( $params->id ) ? null : $params->id
                      );
    }

}
