<?php

namespace Grid\Core\Model\SubDomain;

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
     * @param \Core\Model\SubDomain\Mapper $userMapper
     */
    public function __construct( Mapper $userMapper )
    {
        $this->setMapper( $userMapper );
    }

    /**
     * Return true, if subdomain is available
     *
     * @param   string          $subdomain
     * @param   array|object    $params
     * @return  bool
     */
    public function isSubdomainAvailable( $subdomain, $params = array() )
    {
        $params = (object) $params;

        return ! $this->getMapper()
                      ->isSubdomainExists(
                          $subdomain,
                          empty( $params->id ) ? null : $params->id
                      );
    }

}
