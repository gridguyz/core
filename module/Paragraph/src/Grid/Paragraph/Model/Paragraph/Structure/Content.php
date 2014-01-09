<?php

namespace Grid\Paragraph\Model\Paragraph\Structure;

use ArrayIterator;
use Zork\Stdlib\String;
use Zork\Stdlib\DateTime;
use DateTime as BaseDateTime;
use Grid\User\Model\User\Structure as UserStructure;

/**
 * Content
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Content extends AbstractRoot
           implements LayoutAwareInterface,
                      PublishRestrictedInterface,
                      RepresentsTextContentInterface,
                      RepresentsImageContentsInterface
{

    /**
     * Paragraph type
     *
     * @var string
     */
    protected static $type = 'content';

    /**
     * Locale-aware properties
     *
     * @var array
     */
    protected static $localeAwareProperties = array(
        'title'             => true,
        'leadText'          => true,
        'metaKeywords'      => true,
        'metaDescription'   => true,
    );

    /**
     * Paragraph-render view-open
     *
     * @var string
     */
    protected static $viewOpen = 'grid/paragraph/render/content';

    /**
     * Content-title
     *
     * @var string
     */
    public $title = '';

    /**
     * Laoyut-ID
     *
     * @var int
     */
    protected $layoutId;

    /**
     * Layout
     *
     * @var \Paragraph\Model\Paragraph\Structure\Layout
     */
    private $_layout;

    /**
     * Content created-by user-id
     *
     * @var int
     */
    protected $userId = null;

    /**
     * Content created-by user
     *
     * @var \User\Model\User\Structure
     */
    private $_user;

    /**
     * User-mapper
     *
     * @var \User\Model\User\Mapper
     */
    private $_userMapper;

    /**
     * Created
     *
     * @var \DateTime
     */
    protected $created = null;

    /**
     * Last modified
     *
     * @var \DateTime
     */
    private $_lastModified = null;

    /**
     * Is published
     *
     * @var bool
     */
    protected $published = true;

    /**
     * Published from
     *
     * @var \DateTime
     */
    protected $publishedFrom = null;

    /**
     * Published to
     *
     * @var \DateTime
     */
    protected $publishedTo = null;

    /**
     * Accessible for everyone
     *
     * @var bool
     */
    protected $allAccess = true;

    /**
     * Accessible user-groups
     *
     * @var array
     */
    protected $accessGroups = array();

    /**
     * Accessible users
     *
     * @var array
     */
    protected $accessUsers = array();

    /**
     * Editable user-groups
     *
     * @var array
     */
    protected $editGroups = array();

    /**
     * Editable users
     *
     * @var array
     */
    protected $editUsers = array();

    /**
     * Lead image
     *
     * @var string
     */
    public $leadImage = '';

    /**
     * Lead text
     *
     * @var string
     */
    public $leadText = '';

    /**
     * Meta-robots
     *
     * @var string
     */
    public $metaRobots = '';

    /**
     * Meta-keywords
     *
     * @var string
     */
    public $metaKeywords = '';

    /**
     * Meta-description
     *
     * @var string
     */
    public $metaDescription = '';

    /**
     * Uri-model
     *
     * @var \Core\Model\Uri\Model
     */
    private $_uriModel;

    /**
     * Seo-uri
     *
     * @var string
     */
    private $_seoUri;

    /**
     * Seo-uri structure
     *
     * @var \Core\Model\Uri\Structure
     */
    private $_seoUriStructure;

    /**
     * Get layout-ID
     *
     * @return int
     */
    public function getLayoutId()
    {
        return $this->layoutId;
    }

    /**
     * Set layout-id
     *
     * @param int $layoutId
     * @return \Paragraph\Model\Paragraph\Structure\Content
     */
    public function setLayoutId( $layoutId )
    {
        $this->_layout  = null;
        $this->layoutId = ( (int) $layoutId ) ?: null;
        return $this;
    }

    /**
     * Get layout
     *
     * @return \Paragraph\Model\Paragraph\Structure\Layout
     */
    public function getLayout()
    {
        if ( null === $this->_layout && $this->layoutId )
        {
            $this->_layout = $this->getMapper()
                                  ->find( $this->layoutId );
        }

        return $this->_layout;
    }

    /**
     * Set layout
     *
     * @param \Paragraph\Model\Paragraph\Structure\Layout $layout
     * @return \Paragraph\Model\Paragraph\Structure\Content
     */
    public function setLayout( Layout $layout = null )
    {
        if ( null === $layout )
        {
            $this->layoutId = null;
            $this->_layout  = null;
        }
        else
        {
            $this->layoutId = $layout->id;
            $this->_layout  = $layout;
        }

        return $this;
    }

    /**
     * Get user-ID
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set user-id
     *
     * @param int $userId
     * @return \Paragraph\Model\Paragraph\Structure\Content
     */
    public function setUserId( $userId )
    {
        $this->_user    = null;
        $this->userId   = ( (int) $userId ) ?: null;
        return $this;
    }

    /**
     * Get user mapper
     *
     * @return \User\Model\User\Mapper
     */
    protected function getUserMapper()
    {
        if ( null === $this->_userMapper )
        {
            $mapper = $this->getMapper();

            $this->_userMapper = $this->getServiceLocator()
                                      ->get( 'Di' )
                                      ->get( 'Grid\User\Model\User\Mapper', array(
                                          'dbAdapter' => $mapper->getDbAdapter(),
                                          'dbSchema'  => $mapper->getDbSchema(),
                                      ) );
        }

        return $this->_userMapper;
    }

    /**
     * Get user
     *
     * @return \User\Model\User\Structure
     */
    public function getUser()
    {
        if ( null === $this->_user && $this->userId )
        {
            $this->_user = $this->getUserMapper()
                                ->find( $this->userId );
        }

        return $this->_user;
    }

    /**
     * Set user
     *
     * @param \User\Model\User\Structure $user
     * @return \Paragraph\Model\Paragraph\Structure\Content
     */
    public function setUser( UserStructure $user = null )
    {
        if ( null === $user )
        {
            $this->_user    = null;
            $this->userId   = null;

        }
        else
        {
            $this->_user    = $user;
            $this->userId   = $user->id;
        }

        return $this;
    }

    /**
     * Input date
     *
     * @param string $date
     * @param string $format
     * @return \DateTime
     */
    protected function inputDate( $date, $format = null )
    {
        if ( ! $date instanceof DateTime )
        {
            if ( $date instanceof BaseDateTime )
            {
                $date = DateTime::createFromFormat(
                    DateTime::ISO8601,
                    $date->format( DateTime::ISO8601 )
                );
            }

            if ( is_int( $date ) )
            {
                $date = new DateTime( '@' . $date );
            }
            else if ( empty( $format ) )
            {
                $date = new DateTime( $date );
            }
            else
            {
                $date = DateTime::createFromFormat( $format, $date );
            }
        }

        return $date;
    }

    /**
     * Set created date
     *
     * @param \DateTime|string $date
     * @param string|null $format
     * @return \Paragraph\Model\Paragraph\Structure\Content
     */
    public function setCreated( $date, $format = null )
    {
        $this->created = $this->inputDate( $date, $format );
        return $this;
    }

    /**
     * Get last-modified date
     *
     * @return \DateTime
     */
    public function getLastModified()
    {
        return $this->_lastModified;
    }

    /**
     * Set last-modified date
     *
     * @param \DateTime|string $date
     * @param string|null $format
     * @return \Paragraph\Model\Paragraph\Structure\Content
     */
    public function setLastModified( $date, $format = null )
    {
        $this->_lastModified = $this->inputDate( $date, $format );
        return $this;
    }

    /**
     * Set published flag
     *
     * @param bool $published
     * @return \Paragraph\Model\Paragraph\Structure\Content
     */
    public function setPublished( $published )
    {
        $this->published = (bool) $published;
        return $this;
    }

    /**
     * Set published-from date
     *
     * @param \DateTime|string $date
     * @param string|null $format
     * @return \Paragraph\Model\Paragraph\Structure\Content
     */
    public function setPublishedFrom( $date, $format = null )
    {
        $this->publishedFrom = empty( $date ) ? null : $this->inputDate( $date, $format );
        return $this;
    }

    /**
     * Set published-to date
     *
     * @param \DateTime|string $date
     * @param string|null $format
     * @return \Paragraph\Model\Paragraph\Structure\Content
     */
    public function setPublishedTo( $date, $format = null )
    {
        $this->publishedTo = empty( $date ) ? null : $this->inputDate( $date, $format );
        return $this;
    }

    /**
     * Is published at a given time point
     *
     * @param int|string|\DateTime $now default: null
     * @return bool
     */
    public function isPublished( $now = null )
    {
        if ( ! $this->published )
        {
            return false;
        }

        if ( empty( $now ) )
        {
            $now = new DateTime();
        }
        else
        {
            $now = $this->inputDate( $now );
        }

        if ( ! empty( $this->publishedTo ) &&
             ! $this->publishedTo->diff( $now )->invert )
        {
            return false;
        }

        if ( ! empty( $this->publishedFrom ) &&
             $this->publishedFrom->diff( $now )->invert )
        {
            return false;
        }

        return true;
    }

    /**
     * Set all access
     *
     * @param   bool $allAccess
     * @return  \Paragraph\Model\Paragraph\Structure\Content
     */
    public function setAllAccess( $allAccess )
    {
        $this->allAccess = (bool) $allAccess;
        return $this;
    }

    /**
     * Set access users
     *
     * @param   array|string $users
     * @return  \Paragraph\Model\Paragraph\Structure\Content
     */
    public function setAccessUsers( $users )
    {
        $this->accessUsers = array_unique(
            is_array( $users ) ? $users : preg_split( '/[,\s]+/', $users )
        );

        return $this;
    }

    /**
     * Set access groups
     *
     * @param   array|string $groups
     * @return  \Paragraph\Model\Paragraph\Structure\Content
     */
    public function setAccessGroups( $groups )
    {
        $this->accessGroups = array_unique(
            is_array( $groups ) ? $groups : preg_split( '/[,\s]+/', $groups )
        );

        return $this;
    }

    /**
     * Set edit users
     *
     * @param   array|string $users
     * @return  \Paragraph\Model\Paragraph\Structure\Content
     */
    public function setEditUsers( $users )
    {
        $this->editUsers = array_unique(
            is_array( $users ) ? $users : preg_split( '/[,\s]+/', $users )
        );

        return $this;
    }

    /**
     * Set edit groups
     *
     * @param   array|string $groups
     * @return  \Paragraph\Model\Paragraph\Structure\Content
     */
    public function setEditGroups( $groups )
    {
        $this->editGroups = array_unique(
            is_array( $groups ) ? $groups : preg_split( '/[,\s]+/', $groups )
        );

        return $this;
    }

    /**
     * Get uri model
     *
     * @return \Core\Model\Uri\Model
     */
    protected function getUriModel()
    {
        if ( null === $this->_uriModel )
        {
            $this->_uriModel = $this->getServiceLocator()
                                    ->get( 'Grid\Core\Model\Uri\Model' );
        }

        return $this->_uriModel;
    }

    /**
     * Get uri structure by locale(s)
     *
     * @param   null|string|array $locales
     * @return  \Core\Model\Uri\Structure
     */
    protected function getUriStructure( array $locales )
    {
        return $this->getUriModel()
                    ->findDefaultByContentSubdomain(
                           $this->id,
                           $this->getServiceLocator()
                                ->get( 'SiteInfo' )
                                ->getSubdomainId(),
                           $locales
                       );
    }

    /**
     * Get uri (string) for view content
     *
     * @param   array|string|null $locale
     * @return  string
     */
    public function getUri( $locales = null )
    {
        if ( empty( $locales ) )
        {
            $locales = $this->getMapper()
                            ->getLocale();
        }

        $locales = (array) $locales;
        $uri     = $this->getUriStructure( $locales );

        if ( empty( $uri ) )
        {
            $locale = null;
            $maxQ   = -1;

            foreach ( (array) $locales as $l => $q )
            {
                if ( is_numeric( $l ) )
                {
                    $l = $q;
                    $q = 1;
                }

                if ( $q > $maxQ )
                {
                    $maxQ   = $q;
                    $locale = $l;
                }
            }

            return '/app/' . $locale . '/paragraph/render/' . $this->id;
        }

        return '/' . $uri->safeUri;
    }

    /**
     * Get seo-friendly uri
     *
     * @return string
     */
    public function getSeoUri()
    {
        if ( ! empty( $this->_seoUri ) )
        {
            return $this->_seoUri;
        }

        if ( empty( $this->id ) )
        {
            return '';
        }

        if ( empty( $this->_seoUri ) )
        {
            $this->_seoUriStructure = $this->getUriStructure( array(
                $this->getMapper()
                     ->getLocale()
            ) );

            if ( ! empty( $this->_seoUriStructure ) )
            {
                $this->_seoUri = $this->_seoUriStructure->uri;
            }
        }

        return $this->_seoUri;
    }

    /**
     * Set seo-uri
     *
     * @param   string $seoUri
     * @return  \Paragraph\Model\Paragraph\Structure\Content
     */
    public function setSeoUri( $seoUri )
    {
        $this->_seoUri = (string) $seoUri;
        return $this;
    }

    /**
     * Get iterator
     *
     * @return \AppendIterator
     */
    public function getIterator()
    {
        $iterator = parent::getIterator();

        if ( $this->id )
        {
            $iterator->append( new ArrayIterator( array(
                'seoUri' => $this->getSeoUri(),
            ) ) );
        }

        return $iterator;
    }

    /**
     * Get dependent structures
     *
     * @return \Zork\Model\Structure\MapperAwareAbstract[]
     */
    public function getDependentStructures()
    {
        $dependents = parent::getDependentStructures();

        if ( ! empty( $this->id ) && isset( $this->_seoUri ) )
        {
            $subdomainId = $this->getServiceLocator()
                                ->get( 'SiteInfo' )
                                ->getSubdomainId();

            if ( empty( $this->_seoUriStructure ) )
            {
                if ( ! empty( $this->_seoUri ) )
                {
                    $dependents[] = $this->getUriModel()
                                         ->create( array(
                                             'default'      => true,
                                             'contentId'    => $this->id,
                                             'subdomainId'  => $subdomainId,
                                             'uri'          => $this->_seoUri,
                                             'locale'       => $this->getMapper()
                                                                    ->getLocale(),
                                         ) );
                }
            }
            else if ( $this->_seoUriStructure->uri != $this->_seoUri )
            {
                $uri = $this->getUriModel()
                            ->findBySubdomainUri( $subdomainId, $this->_seoUri );

                if ( $uri && $uri->contentId == $this->id )
                {
                    $dependents[] = $uri->setDefault();
                }
                else
                {
                    $dependents[] = $this->_seoUriStructure
                                         ->setUri( $this->_seoUri );
                }
            }
        }

        return $dependents;
    }

    /**
     * @return  string
     */
    public function getRepresentedTextContent()
    {
        $parts = array();

        if ( ! empty( $this->title ) )
        {
            $parts[] = $this->title;
        }

        if ( ! empty( $this->leadText ) )
        {
            $parts[] = String::stripHtml( $this->leadText );
        }

        if ( ! empty( $this->metaDescription ) )
        {
            $parts[] = $this->metaDescription;
        }

        return implode( "\n\n", $parts ) ?: null;
    }

    /**
     * @return  array
     */
    public function getRepresentedImageContentUrls()
    {
        $urls = array();

        if ( ! empty( $this->leadImage ) )
        {
            $urls[] = $this->leadImage;
        }

        return $urls;
    }

    /**
     * This paragraph-type properties
     *
     * @return array
     */
    public static function getAllowedFunctions()
    {
        return array_diff(
            parent::getAllowedFunctions(),
            array( static::PROPERTY_DELETE )
        );
    }

    /**
     * @return \Paragraph\Model\Paragraph\Structure\Content
     */
    public function prepareCreate()
    {
        if ( empty( $this->created ) )
        {
            $this->created = new DateTime();
        }

        $mapper = $this->getMapper();

        $this->bindChild( $mapper->create( array(
            'type' => 'html',
        ) ) );

        return parent::prepareCreate();
    }

}
