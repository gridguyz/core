<?php

namespace Grid\Core\Model\Uri;

use Zork\Model\LocaleAwareTrait;
use Zork\Model\LocaleAwareInterface;
use Zork\Model\Structure\MapperAwareAbstract;

/**
 * Structure
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Structure extends MapperAwareAbstract
             implements LocaleAwareInterface
{

    use LocaleAwareTrait;

    /**
     * @var string
     */
    const VALID_URIS            = '^((?!(app|images|scripts|styles|thumbnails|tmp|uploads)\/)(?!favicon\.ico|sitemap\.xml|robots\.txt).+(?!index\.php))$';

    /**
     * Field: id
     *
     * @var int
     */
    protected $id               = null;

    /**
     * Field: subdomainId
     *
     * @var int
     */
    protected $subdomainId      = null;

    /**
     * Field: contenId
     *
     * @var int
     */
    protected $contentId        = null;

    /**
     * Field: uri
     *
     * @var string
     */
    protected $uri              = '';

    /**
     * Field: default
     *
     * @var bool
     */
    protected $default          = false;

    /**
     * @param type $uri
     * @return string
     */
    public static function trimUri( $uri )
    {
        $uri = preg_replace(
            '#/+#', '/',
            trim(
                str_replace(
                    '\\', '/',
                    (string) $uri
                ),
                '/'
            )
        );

        if ( ! preg_match( '/' . static::VALID_URIS . '/', $uri ) )
        {
            $uri = '~' . $uri;
        }

        if ( $uri == '/' || empty( $uri ) )
        {
            $uri = null;
        }

        return $uri;
    }

    /**
     * Setter for: uri
     *
     * @param string $uri
     * @return CoreSubDomain_Model_Structure_Uri
     */
    public function setUri( $uri )
    {
        $this->uri = static::trimUri( $uri );
        return $this;
    }

    /**
     * Set default flag
     *
     * @param bool $flag optional, default: true
     * @return CoreSubDomain_Model_Structure_Uri
     */
    public function setDefault( $flag = true )
    {
        $this->default = (bool) $flag;
        return $this;
    }

    /**
     * Get safe uri (for embedding / redirecting)
     *
     * @return string
     */
    public function getSafeUri()
    {
        static $safe = array(
            '%2F' => '/',
            '%2f' => '/',
        );

        if ( empty( $this->uri ) )
        {
            return null;
        }

        return strtr( rawurlencode( $this->uri ), $safe );
    }

}
