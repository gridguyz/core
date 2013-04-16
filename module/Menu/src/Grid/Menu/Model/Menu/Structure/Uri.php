<?php

namespace Grid\Menu\Model\Menu\Structure;

/**
 * Uri
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Uri extends ProxyAbstract
{

    /**
     * @var string
     */
    const DEFAULT_SCHEME    = 'http://';

    /**
     * @var string
     */
    const VALID_SCHEMES     = '#//|https?://|s?ftp://|irc://|mailto:|javascript:|magnet:|data:|news:';

    /**
     * Menu type
     *
     * @var string
     */
    protected static $type = 'uri';

    /**
     * Uri
     *
     * @var string
     */
    protected $uri = '';

    /**
     * Getter for uri
     *
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Setter for uri
     *
     * @param string $uri
     * @return Grid\Menu\Model\Menu\Structure\Uri
     */
    public function setUri( $uri )
    {
        $uri = (string) $uri;

        if ( empty( $uri ) )
        {
            $uri = '#';
        }
        else if ( ! preg_match( '(^(' . static::VALID_SCHEMES . '))', $uri ) )
        {
            if ( preg_match( '(^[a-z0-9-]+(\.[a-z0-9-]+)+(/.*)?$)', $uri ) )
            {
                $uri = static::DEFAULT_SCHEME . $uri;
            }
            else
            {
                $uri = '/' . ltrim( $uri, '/' );
            }
        }

        $this->uri = $uri;
        return $this;
    }

}
