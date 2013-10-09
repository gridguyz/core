<?php

namespace Grid\Paragraph\Model\Paragraph\Structure;

use Traversable;
use Zend\Stdlib\ArrayUtils;
use Grid\Core\Model\Uri\Model as UriModel;
use Zend\ServiceManager\Exception\ServiceNotFoundException;

/**
 * Html
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Language extends AbstractLeaf
{

    /**
     * Paragraph type
     *
     * @var string
     */
    protected static $type = 'language';

    /**
     * Locale-aware properties
     *
     * @var array
     */
    protected static $localeAwareProperties = array();

    /**
     * Paragraph-render view-open
     *
     * @var string
     */
    protected static $viewOpen = 'grid/paragraph/render/language';

    /**
     * Locales
     *
     * @var array
     */
    protected $locales = array();

    /**
     * Uri model
     *
     * @var \Core\Model\Uri\Model
     */
    private $_uriModel;

    /**
     * Get uri-model
     *
     * @return \Core\Model\Uri\Model
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
     * Set uri-model
     *
     * @param \Core\Model\Uri\Model $uriModel
     * @return \Paragraph\Model\Paragraph\Structure\Language
     */
    public function setUriModel( UriModel $uriModel )
    {
        $this->_uriModel = $uriModel;
        return $this;
    }

    /**
     * Set locales
     *
     * @param array|\Traversable $locales
     * @return \Paragraph\Model\Paragraph\Structure\Language
     */
    public function setLocales( $locales )
    {
        if ( $locales instanceof Traversable )
        {
            $locales = ArrayUtils::iteratorToArray( $locales );
        }

        $this->locales = array_unique( array_filter(
            array_values( (array) $locales ),
            'strval'
        ) );

        return $this;
    }

    /**
     * Get links
     *
     * @return array
     */
    public function getLinks()
    {
        $result     = array();
        $service    = $this->getServiceLocator();
        $rendered   = null;
        $active     = null;
        $defaultUri = null;
        $siteInfo   = null;
        $available  = $service->get( 'Locale' )
                              ->getAvailableFlags();

        try
        {
            /* @var $rendered \Paragraph\Model\Paragraph\Structure\Content */
            $rendered = $service->get( 'RenderedContent' );

            if ( ! $rendered instanceof Content )
            {
                throw new ServiceNotFoundException;
            }

            $defaultUri = '/app/%locale%/paragraph/render/' . $rendered->id;
            $active     = $rendered->getMapper()->getLocale();
        }
        catch ( ServiceNotFoundException $ex )
        {
            /* @var $request \Zend\Http\PhpEnvironment\Request */
            $request    = $service->get( 'Request' );
            $requestUri = $request->getRequestUri();
            $matches    = array();

            if ( preg_match( '#^/?app/([^/]+)/(.*)$#', $requestUri, $matches ) )
            {
                $defaultUri = '/app/%locale%/' . $matches[2];
                $active     = $matches[1];
            }
        }

        if ( ! empty( $defaultUri ) )
        {
            $selected   = $this->locales;
            $all        = array_keys( array_filter( $available ) );

            if ( empty( $selected ) )
            {
                $selected = $all;
            }

            foreach ( $all as $locale )
            {
                $link = array(
                    'selected'  => in_array( $locale, $selected ),
                    'active'    => $active == $locale,
                    'uri'       => str_replace(
                        '%locale%',
                        $locale,
                        $defaultUri
                    ),
                );

                if ( $rendered instanceof Content )
                {
                    if ( null === $siteInfo )
                    {
                        $siteInfo = $service->get( 'Zork\Db\SiteInfo' );
                    }

                    $uri = $this->getUriModel()
                                ->findDefaultByContentSubdomain(
                                    $rendered->id,
                                    $siteInfo->getSubdomainId(),
                                    $locale
                                );

                    if ( ! empty( $uri ) )
                    {
                        $link['uri'] = '/' . $uri->safeUri;
                    }
                }

                $result[$locale] = $link;
            }
        }

        return $result;
    }

}
