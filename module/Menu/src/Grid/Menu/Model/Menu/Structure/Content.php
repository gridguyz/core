<?php

namespace Grid\Menu\Model\Menu\Structure;

use Grid\Core\Model\Uri\Model as UriModel;
use Grid\Core\Model\SubDomain\Model as SubDomainModel;
use Grid\Paragraph\Model\Paragraph\Model as ParagraphModel;

/**
 * Content
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Content extends ProxyAbstract
{

    /**
     * Type
     *
     * @var string
     */
    protected static $type = 'content';

    /**
     * Content id
     *
     * @var int
     */
    protected $contentId = null;

    /**
     * Subdomain id
     *
     * @var int
     */
    protected $subdomainId = null;

    /**
     * Stored uri-model
     *
     * @var \Grid\Core\Model\Uri\Model
     */
    private $_uriModel = null;

    /**
     * Stored subdomain-model
     *
     * @var \Grid\Core\Model\SubDomain\Model
     */
    private $_subDomainModel = null;

    /**
     * Stored paragraph-model
     *
     * @var \Grid\Paragraph\Model\Paragraph\Model
     */
    private $_paragraphModel = null;

    /**
     * Stored uri
     *
     * @var string
     */
    private $_uriCache = null;

    /**
     * Stored visibility
     *
     * @var bool
     */
    private $_visible = null;

    /**
     * Getter for content-id
     *
     * @return  string
     */
    public function getContentId()
    {
        return $this->contentId;
    }

    /**
     * Setter for content-id
     *
     * @param   int $id
     * @return  \Grid\Menu\Model\Menu\Structure\Content
     */
    public function setContentId( $id )
    {
        $this->contentId    = empty( $id ) ? null : (int) $id;
        $this->_uriCache    = null;
        $this->_visible     = null;
        return $this;
    }

    /**
     * Getter for subdomain-id
     *
     * @return  string
     */
    public function getSubdomainId()
    {
        return $this->subdomainId;
    }

    /**
     * Setter for subdomain-id
     *
     * @param   int $id
     * @return  \Grid\Menu\Model\Menu\Structure\Content
     */
    public function setSubdomainId( $id )
    {
        $this->subdomainId  = empty( $id ) ? null : (int) $id;
        $this->_uriCache    = null;
        return $this;
    }

    /**
     * Get the stored uri-mapper
     *
     * @return  \Grid\Core\Model\Uri\Model
     */
    public function getUriModel()
    {
        if ( null === $this->_uriModel )
        {
            $this->_uriModel = $this->getServiceLocator()
                                    ->get( 'Grid\Core\Model\Uri\Model' );
        }

        return $this->_uriModel;
    }

    /**
     * Set the stored uri-model
     *
     * @param   \Grid\Core\Model\Uri\Model $uriModel
     * @return  \Grid\Menu\Model\Menu\Structure\Content
     */
    public function setUriModel( UriModel $uriModel )
    {
        $this->_uriModel = $uriModel;
        return $this;
    }

    /**
     * Get the stored subdomain-mapper
     *
     * @return  \Grid\Core\Model\SubDomain\Model
     */
    public function getSubDomainModel()
    {
        if ( null === $this->_subDomainModel )
        {
            $this->_subDomainModel = $this->getServiceLocator()
                                          ->get( 'Grid\Core\Model\SubDomain\Model' );
        }

        return $this->_subDomainModel;
    }

    /**
     * Set the stored subdomain-model
     *
     * @param   \Grid\Core\Model\SubDomain\Model $subDomainModel
     * @return  \Grid\Menu\Model\Menu\Structure\Content
     */
    public function setSubDomainModel( SubDomainModel $subDomainModel )
    {
        $this->_subDomainModel = $subDomainModel;
        return $this;
    }

    /**
     * Get the stored paragraph-mapper
     *
     * @return  \Grid\Paragraph\Model\Paragraph\Model
     */
    public function getParagraphModel()
    {
        if ( null === $this->_paragraphModel )
        {
            $this->_paragraphModel = $this->getServiceLocator()
                                          ->get( 'Grid\Paragraph\Model\Paragraph\Model' );
        }

        return $this->_paragraphModel;
    }

    /**
     * Set the stored paragraph-model
     *
     * @param   \Grid\Paragraph\Model\Paragraph\Model $uriModel
     * @return  \Grid\Menu\Model\Menu\Structure\Content
     */
    public function setParagraphModel( ParagraphModel $paragraphModel )
    {
        $this->_paragraphModel = $paragraphModel;
        return $this;
    }

    /**
     * Get visibility
     *
     * @return bool
     */
    public function isVisible()
    {
        if ( ! parent::isVisible() )
        {
            return false;
        }

        if ( null === $this->_visible )
        {
            $content = $this->getParagraphModel()
                            ->find( $this->contentId );

            $this->_visible = empty( $content )
                            ? false
                            : $content->isPublished() && $content->isAccessible();
        }

        return $this->_visible;
    }

    /**
     * Getter for uri
     *
     * @return  string
     */
    public function getUri()
    {
        if ( empty( $this->_uriCache ) )
        {
            $subdomainId = $this->getSubdomainId();
            $info = $this->getServiceLocator()
                         ->get( 'Zork\Db\SiteInfo' );

            if ( empty( $subdomainId ) )
            {
                $subdomainId = $info->getSubdomainId();
            }
            else if ( $subdomainId != $info->getSubdomainId() )
            {
                $subdomain = $this->getSubDomainModel()
                                  ->find( $subdomainId );

                if ( empty( $subdomain ) )
                {
                    $subdomainId = $info->getSubdomainId();
                }
            }

            if ( ! empty( $this->contentId ) )
            {
                $base   = '/';
                $locale = $this->getMapper()
                               ->getLocale();
                $uri    = $this->getUriModel()
                               ->findDefaultByContentSubdomain(
                                    $this->getContentId(),
                                    $subdomainId,
                                    $locale
                                );

                if ( ! empty( $subdomain ) )
                {
                    $base = 'http://' . $subdomain->subdomain .
                            ( $subdomain->subdomain ? '.' : '' ) .
                            $info->getDomain() . '/';
                }

                if ( empty( $uri ) )
                {
                    $uri = $base . 'app/' . $locale .
                           '/paragraph/render/' . $this->getContentId();
                }
                else
                {
                    $uri = $base . ltrim( $uri->safeUri, '/' );
                    $this->_uriCache = $uri;
                }

                return $uri;
            }
        }

        return $this->_uriCache;
    }

}
