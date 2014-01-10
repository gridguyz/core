<?php

namespace Grid\Core\SiteConfiguration;

use Zork\Db\SiteInfo;
use Zend\Db\Adapter\Adapter as DbAdapter;
use Zork\Db\SiteConfiguration\RedirectionService;
use Zork\Db\SiteConfiguration\AbstractDomainAware;
use Zend\ServiceManager\Exception;

/**
 * Singlesite
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Singlesite extends AbstractDomainAware
{

    /**
     * Setup services which depends on the db
     *
     * @param   \Zend\Db\Adapter\Adapter $db
     * @return  \Zend\Db\Adapter\Adapter
     */
    public function configure( DbAdapter $db )
    {
        $sm         = $this->getServiceLocator();
        $platform   = $db->getPlatform();
        $driver     = $db->getDriver();
        $matches    = array();
        $fulldomain = strtolower( $this->getDomain() );
        $config     = $driver->getConnection()
                             ->getConnectionParameters();

        /// TODO: remove this
        if ( isset( $_SERVER['GRIDGUYZ_DOMAIN'] ) )
        {
            $domain     = $_SERVER['GRIDGUYZ_DOMAIN'];
            $subdomain  = isset( $_SERVER['GRIDGUYZ_SUBDOMAIN'] )
                        ? $_SERVER['GRIDGUYZ_SUBDOMAIN']
                        : '';
        }
        elseif ( preg_match( '/^[\da-fA-F:]+/', $fulldomain ) ||    // probably ipv6
                 preg_match( '/^\d+(\.\d+){3}$/', $fulldomain ) )   // ipv4
        {
            $subdomain  = '';
            $domain     = $fulldomain;
        }
        else
        {
            $fullLength = strlen( $fulldomain );

            if ( empty( $config['validDomains'] ) )
            {
                $validDomains = array( 'localhost' );
            }
            else
            {
                $validDomains = array_map(
                    'strtolower',
                    (array) $config['validDomains']
                );
            }

            if ( ! empty( $config['defaultDomain'] ) )
            {
                $defaultDomain = strtolower( $config['defaultDomain'] );

                if ( ! in_array( $defaultDomain, $validDomains ) )
                {
                    array_unshift( $validDomains, $defaultDomain );
                }
            }

            foreach ( $validDomains as $validDomain )
            {
                if ( $fulldomain == $validDomain )
                {
                    $subdomain  = '';
                    $domain     = $fulldomain;
                    break;
                }

                $length = $fullLength - strlen( $validDomain ) - 1;

                if ( $length > 0 &&
                     ( '.' . $validDomain ) == substr( $fulldomain, $length ) )
                {
                    $subdomain  = substr( $fulldomain, 0, $length );
                    $domain     = $validDomain;
                    break;
                }
            }
        }

        if ( ! isset( $subdomain ) || ! isset( $domain ) )
        {
            if ( preg_match( '/^(.+)\.([a-z0-9-]+\.[a-z]+)$/', $fulldomain, $matches ) )
            {
                $subdomain  = $matches[1];
                $domain     = $matches[2];
            }
            else
            {
                $subdomain  = '';
                $domain     = $fulldomain;
            }
        }

        $query = $db->query( '
            SELECT *
              FROM ' . $platform->quoteIdentifier( 'subdomain' ) . '
             WHERE ' . $platform->quoteIdentifier( 'subdomain' ) . '
                 = LOWER( ' . $driver->formatParameterName( 'subdomain' ) . ' )
        ' );

        $result = $query->execute( array(
            'subdomain' => $subdomain
        ) );

        if ( $result->getAffectedRows() > 0 )
        {
            $schema = $db->getCurrentSchema();

            foreach ( $result as $data )
            {
                $info = new SiteInfo( array(
                    'schema'        => $schema,
                    'domain'        => $domain,
                    'subdomain'     => $subdomain,
                    'subdomainId'   => $data['id'],
                    'fulldomain'    => $fulldomain,
                ) );

                $sm->setService( 'SiteInfo', $info );
                return $db;
            }
        }
        else if ( $domain )
        {
            $sm->setService(
                'RedirectToDomain',
                new RedirectionService(
                    $domain,
                    'sub-domain not found',
                    false
                )
            );
        }
        else
        {
            if ( empty( $config['defaultDomain'] ) )
            {
                throw new Exception\InvalidArgumentException(
                    'Domain not found, and default domain not set'
                );
            }
            else
            {
                $sm->setService(
                    'RedirectToDomain',
                    new RedirectionService(
                        $config['defaultDomain'],
                        'sub-domain not found',
                        false
                    )
                );
            }
        }

        return $db;
    }

}
