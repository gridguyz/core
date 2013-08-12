<?php

namespace Grid\Core\Model\Package;

use Zork\Stdlib\DateTime;
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
     * @var string
     */
    protected $displayIcon;

    /**
     * Field: displayName
     *
     * @var string
     */
    protected $displayName;

    /**
     * Field: displayDescription
     *
     * @var string
     */
    protected $displayDescription;

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
     * Get displayed name
     *
     * @return string
     */
    public function getDisplayedName()
    {
        if ( ! empty( $this->displayName ) )
        {
            return $this->displayName;
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
     * @return string
     */
    public function getDisplayedDescription()
    {
        if ( ! empty( $this->displayDescription ) )
        {
            return $this->displayDescription;
        }

        if ( ! empty( $this->description ) && strpos( $this->description, "\n" ) )
        {
            list( $name, $description ) = explode( "\n", $this->description, 2 );
            return $description;
        }

        return $this->description;
    }

    /**
     * Is a package a valid package
     *
     * @return string
     */
    public function isValid()
    {
        return preg_match( static::VALID_TYPES, $this->type );
    }

    /**
     * Can install this package
     *
     * @return bool
     */
    public function canInstall()
    {
        return empty( $this->installedVersion ) &&
             ! empty( $this->availableVersion );
    }

    /**
     * Can remove this package
     *
     * @return bool
     */
    public function canRemove()
    {
        return ! empty( $this->installedVersion );
    }

    /**
     * Can update (or install) this package
     *
     * @return bool
     */
    public function canUpdate()
    {
        return $this->canInstall() || (
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
        );
    }

}
