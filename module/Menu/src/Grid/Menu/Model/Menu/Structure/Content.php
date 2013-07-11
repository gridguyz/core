<?php

namespace Grid\Menu\Model\Menu\Structure;

use Grid\Core\Model\Uri\Model as UriModel;
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

            if ( empty( $subdomainId ) )
            {
                $info = $this->getServiceLocator()
                             ->get( 'Zork\Db\SiteInfo' );

                $subdomainId = $info->getSubdomainId();
            }

            if ( ! empty( $this->contentId ) )
            {
                $locale = $this->getMapper()
                               ->getLocale();

                $uri = $this->getUriModel()
                            ->findDefaultByContentSubdomain(
                                $this->getContentId(),
                                $subdomainId,
                                $locale
                            );

                if ( empty( $uri ) )
                {
                    $uri = '/app/' . $locale .
                           '/paragraph/render/' . $this->getContentId();
                }
                else
                {
                    $uri = '/' . ltrim( $uri->safeUri, '/' );
                    $this->_uriCache = $uri;
                }

                return $uri;
            }
        }

        return $this->_uriCache;
    }

}
