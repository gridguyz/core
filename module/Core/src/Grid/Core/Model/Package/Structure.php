<?php

namespace Grid\Core\Model\Package;

use Locale;
use Traversable;
use Zork\Stdlib\DateTime;
use Zork\Stdlib\ArrayUtils;
use Zork\Model\Structure\MapperAwareAbstract;

/**
 * Structure
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Structure extends MapperAwareAbstract
{

    /**
     * @const string
     */
    const VALID_TYPES = '#^gridguyz-modules?#';

    /**
     * @const string
     */
    const FALLBACK_LOCALE = 'en';

    /**
     * @const int
     */
    const DEFAULT_ICON_SIZE = 100;

    /**
     * Field: name
     *
     * @var string
     */
    protected $name;

    /**
     * Field: type
     *
     * @var string
     */
    protected $type;

    /**
     * Field: description
     *
     * @var string
     */
    protected $description;

    /**
     * Field: homepage
     *
     * @var string
     */
    protected $homepage;

    /**
     * Field: license
     *
     * @var array
     */
    protected $license = array();

    /**
     * Field: keywords
     *
     * @var array
     */
    protected $keywords = array();

    /**
     * Field: availableTime
     *
     * @var \DateTime
     */
    protected $availableTime;

    /**
     * Field: installedTime
     *
     * @var \DateTime
     */
    protected $installedTime;

    /**
     * Field: availableVersion
     *
     * @var string
     */
    protected $availableVersion;

    /**
     * Field: availableReference
     *
     * @var string
     */
    protected $availableReference;

    /**
     * Field: installedVersion
     *
     * @var string
     */
    protected $installedVersion;

    /**
     * Field: installedReference
     *
     * @var string
     */
    protected $installedReference;

    /**
     * Field: displayIcon
     *
     * @var array
     */
    protected $displayIcon = array();

    /**
     * Field: displayName
     *
     * @var array
     */
    protected $displayName = array();

    /**
     * Field: displayDescription
     *
     * @var array
     */
    protected $displayDescription = array();

    /**
     * Field: modules
     *
     * @var int
     */
    protected $modules = array();

    /**
     * Field: favourites
     *
     * @var int
     */
    protected $favourites;

    /**
     * Field: downloads
     *
     * @var int
     */
    protected $downloads;

    /**
     * Get url-safe name
     *
     * @return  string
     */
    public function getUrlSafeName()
    {
        static $safeUrl = array(
            '%2F' => '/',
            '%2f' => '/',
        );

        return strtr( rawurlencode( $this->name ), $safeUrl );
    }

    /**
     * Set license
     *
     * @param   array   $license
     * @return  \Grid\Core\Model\Package\Structure
     */
    public function setLicense( $license )
    {
        $this->license = (array) $license;
        return $this;
    }

    /**
     * Set keywords
     *
     * @param   array   $keywords
     * @return  \Grid\Core\Model\Package\Structure
     */
    public function setKeywords( $keywords )
    {
        $this->keywords = (array) $keywords;
        return $this;
    }

    /**
     * Convert to date-time
     *
     * @param   int|string|\DateTime|\Zork\Stdlib\DateTime  $time
     * @return  \Zork\Stdlib\DateTime
     */
    protected function convertToDateTime( $time )
    {
        if ( $time instanceof DateTime )
        {
            return clone $time;
        }
        else if ( $time instanceof \DateTime )
        {
            return new DateTime( $time->format( \DateTime::ISO8601 ) );
        }
        else if ( is_numeric( $time ) )
        {
            return new DateTime( '@' . $time );
        }

        return new DateTime( (string) $time );
    }

    /**
     * Set installed time
     *
     * @param   int|string|\DateTime|\Zork\Stdlib\DateTime  $time
     * @return  \Grid\Core\Model\Package\Structure
     */
    public function setInstalledTime( $time )
    {
        $this->installedTime = $this->convertToDateTime( $time );
        return $this;
    }

    /**
     * Set available time
     *
     * @param   int|string|\DateTime|\Zork\Stdlib\DateTime  $time
     * @return  \Grid\Core\Model\Package\Structure
     */
    public function setAvailableTime( $time )
    {
        $this->availableTime = $this->convertToDateTime( $time );
        return $this;
    }

    /**
     * Set modules
     *
     * @param   array   $modules
     * @return  \Grid\Core\Model\Package\Structure
     */
    public function setModules( $modules )
    {
        $this->modules = (array) $modules;
        return $this;
    }

    /**
     * Set favourites
     *
     * @param   int $favourites
     * @return  \Grid\Core\Model\Package\Structure
     */
    public function setFavourites( $favourites )
    {
        $this->favourites = empty( $favourites ) &&
                            $favourites !== 0 &&
                            $favourites !== '0' ? null : (int) $favourites;

        return $this;
    }

    /**
     * Set downloads
     *
     * @param   int $downloads
     * @return  \Grid\Core\Model\Package\Structure
     */
    public function setDownloads( $downloads )
    {
        $this->downloads = empty( $downloads ) &&
                           $downloads !== 0 &&
                           $downloads !== '0' ? null : (int) $downloads;

        return $this;
    }

    /**
     * Set display icon
     *
     * @param   array   $displayIcon
     * @return  \Grid\Core\Model\Package\Structure
     */
    public function setDisplayIcon( $displayIcon )
    {
        if ( empty( $displayIcon ) )
        {
            $displayIcon = array();
        }
        else if ( $displayIcon instanceof Traversable )
        {
            $displayIcon = ArrayUtils::iteratorToArray( $displayIcon );
        }
        else if ( ! is_array( $displayIcon ) )
        {
            $displayIcon = (array) $displayIcon;
        }

        if ( ! isset( $displayIcon[static::DEFAULT_ICON_SIZE] ) )
        {
            reset( $displayIcon );
            $key = key( $displayIcon );
            $displayIcon[static::DEFAULT_ICON_SIZE] = $displayIcon[$key];
            unset( $displayIcon[$key] );
        }

        $this->displayIcon = $displayIcon;
        return $this;
    }

    /**
     * Set display name
     *
     * @param   array   $displayName
     * @return  \Grid\Core\Model\Package\Structure
     */
    public function setDisplayName( $displayName )
    {
        if ( empty( $displayName ) )
        {
            $displayName = array();
        }
        else if ( $displayName instanceof Traversable )
        {
            $displayName = ArrayUtils::iteratorToArray( $displayName );
        }
        else if ( ! is_array( $displayName ) )
        {
            $displayName = (array) $displayName;
        }

        if ( ! isset( $displayName[static::FALLBACK_LOCALE] ) )
        {
            reset( $displayName );
            $key = key( $displayName );
            $displayName[static::FALLBACK_LOCALE] = $displayName[$key];
            unset( $displayName[$key] );
        }

        $this->displayName = $displayName;
        return $this;
    }

    /**
     * Set display description
     *
     * @param   array   $displayDescription
     * @return  \Grid\Core\Model\Package\Structure
     */
    public function setDisplayDescription( $displayDescription )
    {
        if ( empty( $displayDescription ) )
        {
            $displayDescription = array();
        }
        else if ( $displayDescription instanceof Traversable )
        {
            $displayDescription = ArrayUtils::iteratorToArray( $displayDescription );
        }
        else if ( ! is_array( $displayDescription ) )
        {
            $displayDescription = (array) $displayDescription;
        }

        if ( ! isset( $displayDescription[static::FALLBACK_LOCALE] ) )
        {
            reset( $displayDescription );
            $key = key( $displayDescription );
            $displayDescription[static::FALLBACK_LOCALE] = $displayDescription[$key];
            unset( $displayDescription[$key] );
        }

        $this->displayDescription = $displayDescription;
        return $this;
    }

    /**
     * Get displayed icon
     *
     * @param   int  $maxSize
     * @return  string|null
     */
    public function getDisplayedIcon( $maxSize = self::DEFAULT_ICON_SIZE )
    {
        if ( empty( $this->displayIcon ) )
        {
            return null;
        }

        $foundSize = 0;
        $foundIcon = null;

        foreach ( $this->displayIcon as $size => $icon )
        {
            if ( $size >= $foundSize && $size <= $maxSize )
            {
                $foundSize = $size;
                $foundIcon = $icon;
            }
        }

        return $foundIcon;
    }

    /**
     * Get displayed name
     *
     * @param   string  $locale
     * @return  string
     */
    public function getDisplayedName( $locale = self::FALLBACK_LOCALE )
    {
        if ( ! empty( $this->displayName[$locale] ) )
        {
            return $this->displayName[$locale];
        }

        $language = Locale::getPrimaryLanguage( $locale );

        if ( ! empty( $this->displayName[$language] ) )
        {
            return $this->displayName[$language];
        }

        if ( ! empty( $this->displayName[static::FALLBACK_LOCALE] ) )
        {
            return $this->displayName[static::FALLBACK_LOCALE];
        }

        if ( ! empty( $this->description ) && strpos( $this->description, "\n" ) )
        {
            list( $name, $description ) = explode( "\n", $this->description, 2 );
            return $name;
        }

        return $this->name;
    }

    /**
     * Get displayed description
     *
     * @param   string  $locale
     * @return  string
     */
    public function getDisplayedDescription( $locale = self::FALLBACK_LOCALE )
    {
        if ( ! empty( $this->displayDescription[$locale] ) )
        {
            return $this->displayDescription[$locale];
        }

        $language = Locale::getPrimaryLanguage( $locale );

        if ( ! empty( $this->displayDescription[$language] ) )
        {
            return $this->displayDescription[$language];
        }

        if ( ! empty( $this->displayDescription[static::FALLBACK_LOCALE] ) )
        {
            return $this->displayDescription[static::FALLBACK_LOCALE];
        }

        if ( ! empty( $this->description ) && strpos( $this->description, "\n" ) )
        {
            list( $name, $description ) = explode( "\n", $this->description, 2 );
            return $description;
        }

        return $this->description;
    }

    /**
     * Get license uris
     *
     * @return  array
     */
    public function getLicenseUris()
    {
        $result             = array();
        $mapper             = $this->getMapper();
        $licenseUrlPatterns = array(
            '/^cc-(by-sa|by-nd|by-nc|by-nc-sa|by-nc-nd)-(\d+\.\d+)$/i' => function ( $matches ) {
                return 'http://creativecommons.org/licenses/' . strtolower( $matches[1] ) . '/' . $matches[2];
            },
            '/^cc0-(\d+\.\d+)$/i'           => 'http://creativecommons.org/publicdomain/zero/$1',
            '/^Apache-(\d+\.\d+)$/i'        => 'http://www.apache.org/licenses/LICENSE-$1',
            '/^Artistic-(\d+)\.(\d+)$/i'    => 'http://www.perlfoundation.org/artistic_license_$1_$2',
            '/^GPL-(\d+\.\d+)\+?$/i'        => 'http://www.gnu.org/licenses/gpl-$1-standalone.html',
            '/^LGPL-(\d+\.\d+)\+?$/i'       => 'http://www.gnu.org/licenses/lgpl-$1-standalone.html',
            '/^MIT$/i'                      => 'http://opensource.org/licenses/MIT',
            '/^proprietary/i'               => '',
            '/^([^\(].*)$/i'                => function ( $matches ) use ( $mapper ) {
                static $uris = array(
                    'http://opensource.org/licenses/%s',
                    'http://spdx.org/licenses/%s'
                );

                if ( empty( $mapper ) )
                {
                    return '';
                }

                foreach ( $uris as $uriPattern )
                {
                    $uri = vsprintf( $uriPattern, $matches );

                    if ( $mapper->getHttpClient( $uri )
                                ->send()
                                ->isOk() )
                    {
                        return $uri;
                    }
                }

                return '';
            },
        );

        foreach ( $this->license as $license )
        {
            foreach ( $licenseUrlPatterns as $pattern => $replacement )
            {
                $count  = 0;
                $method = is_callable( $replacement ) ? 'preg_replace_callback' : 'preg_replace';
                $uri    = $method( $pattern, $replacement, $license, 1, $count );

                if ( $count && $uri )
                {
                    $result[$license] = $uri;
                    continue 2;
                }
            }

            $result[$license] = '';
        }

        return $result;
    }

    /**
     * Is this package a valid package
     *
     * @return  bool
     */
    public function isValid()
    {
        return preg_match( static::VALID_TYPES, $this->type );
    }

    /**
     * Is this package already installed
     *
     * @return  bool
     */
    public function isInstalled()
    {
        return ! empty( $this->installedVersion );
    }

    /**
     * Can modify packages
     *
     * @return  bool
     */
    public function canModify()
    {
        $mapper = $this->getMapper();

        if ( empty( $mapper ) )
        {
            return false;
        }

        return $mapper->canModify();
    }

    /**
     * Can install this package
     *
     * @return bool
     */
    public function canInstall()
    {
        return $this->canModify() &&
               empty( $this->installedVersion ) &&
               ! empty( $this->availableVersion );
    }

    /**
     * Can remove this package
     *
     * @return bool
     */
    public function canRemove()
    {
        return $this->canModify() &&
               ! preg_match( '#^gridguyz/(core|multisite)$#', $this->name ) &&
               ! empty( $this->installedVersion );
    }

    /**
     * Can update (or install) this package
     *
     * @return bool
     */
    public function canUpdate()
    {
        return $this->canModify() &&(
            $this->canInstall() || (
                ! empty( $this->installedVersion ) && (
                    ( ( $this->availableVersion == 'dev-master' ||
                        $this->installedVersion == 'dev-master' ) &&
                      $this->installedReference != $this->availableReference ) ||
                    version_compare(
                        $this->availableVersion,
                        $this->installedVersion,
                        '>'
                    )
                )
            )
        );
    }

}
