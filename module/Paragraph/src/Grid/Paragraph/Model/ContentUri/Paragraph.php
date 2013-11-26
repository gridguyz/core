<?php

namespace Grid\Paragraph\Model\ContentUri;

use Grid\Core\Model\ContentUri\AdapterAbstract;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * Paragraph
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Paragraph extends AdapterAbstract
             implements ServiceLocatorAwareInterface
{

    use ServiceLocatorAwareTrait;

    /**
     * @const string
     */
    const TYPE = 'paragraph';

    /**
     * @var \Zork\Db\SiteInfo
     */
    private $siteInfo;

    /**
     * Get cached site-info
     *
     * @return \Zork\Db\SiteInfo
     */
    protected function getSiteInfo()
    {
        if ( null === $this->siteInfo )
        {
            $this->siteInfo = $this->getServiceLocator()
                                   ->get( 'SiteInfo' );
        }

        return $this->siteInfo;
    }

    /**
     * Get fallback uri for a paragraph
     *
     * @param   int     $paragraphId
     * @param   bool    $absolute
     * @return  string
     */
    protected function getFallbackUri( $paragraphId, $absolute = false )
    {
        $uri = '/app/' . $this->locale . '/paragraph/render/' . $paragraphId;

        if ( $absolute )
        {
            $domain = $this->getSiteInfo()
                           ->getFulldomain();
            $uri    = 'http://' . $domain . $uri;
        }

        return $uri;
    }

    /**
     * Get domain by subdomain id
     *
     * @staticvar   array       $subdomains
     * @param       int|null    $subdomainId
     * @return      string
     */
    protected function getDomain( $subdomainId = null )
    {
        static $subdomains = array();
        $siteInfo = $this->getSiteInfo();

        if ( empty( $subdomainId ) || $siteInfo->getSubdomainId() == $subdomainId )
        {
            return $siteInfo->getFulldomain();
        }

        if ( ! isset( $subdomains[$subdomainId] ) )
        {
            /* @var $model \Grid\Core\Model\Uri\Model */
            $service    = $this->getServiceLocator();
            $model      = $service->get( 'Grid\Core\Model\SubDomain\Model' );
            $subdomain  = $model->find( $subdomainId );

            if ( empty( $subdomain ) )
            {
                $subdomains[$subdomainId] = '';
            }
            else
            {
                $subdomains[$subdomainId] = $subdomain->subdomain;
            }
        }

        $subdomain = $subdomains[$subdomainId];
        $domain    = $siteInfo->getDomain();
        return ( $subdomain ? $subdomain . '.' : '' ) . $domain;
    }

    /**
     * Get uri for a content paragraph
     *
     * @param   int     $contentId
     * @param   bool    $absolute
     * @return  string
     */
    protected function getUriForContent( $contentId, $absolute = false )
    {
        /* @var $model \Grid\Core\Model\Uri\Model */
        $service    = $this->getServiceLocator();
        $model      = $service->get( 'Grid\Core\Model\Uri\Model' );
        $subdomain  = $this->getSiteInfo()->getSubdomainId();
        $uri        = $model->findDefaultByContentLocale(
            $contentId,
            $this->locale,
            $subdomain
        );

        if ( empty( $uri ) )
        {
            return $this->getFallbackUri( $contentId, $absolute );
        }

        $result = '/' . $uri->safeUri;

        if ( $absolute || $subdomain != $uri->subdomainId )
        {
            $result = 'http://' . $this->getDomain( $uri->subdomainId ) . $result;
        }

        return $result;
    }

    /**
     * Get uri by id
     *
     * @param   int     $paragraphId
     * @param   bool    $absolute
     * @return  string
     */
    protected function getUriById( $paragraphId, $absolute = false )
    {
        /* @var $model \Grid\Paragraph\Model\Paragraph\Model */
        $service    = $this->getServiceLocator();
        $model      = $service->get( 'Grid\Paragraph\Model\Paragraph\Model' );
        $paragraph  = $model->find( $paragraphId );

        if ( empty( $paragraph ) )
        {
            return '#error-paragraph-notFound:' . $paragraphId;
        }

        if ( 'content' == $paragraph->type )
        {
            return $this->getUriForContent( $this->contentId, $absolute );
        }

        if ( $paragraph->id == $paragraph->rootId )
        {
            return $this->getFallbackUri( $paragraphId, $absolute );
        }

        return $this->getUriById( $paragraph->rootId, $absolute )
             . '#paragraph-' . $paragraph->id;
    }

    /**
     * Get uri for a paragraph
     *
     * @param   bool    $absolute
     * @return  string
     */
    public function getUri( $absolute = false )
    {
        if ( empty( $this->contentId ) )
        {
            return '#error-paragraph-missing:contentId';
        }

        if ( 'content' == $this->subType )
        {
            return $this->getUriForContent( $this->contentId, $absolute );
        }

        return $this->getUriById( $this->contentId );
    }

}
