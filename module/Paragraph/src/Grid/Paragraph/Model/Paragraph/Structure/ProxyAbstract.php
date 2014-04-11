<?php

namespace Grid\Paragraph\Model\Paragraph\Structure;

use Traversable;
use AppendIterator;
use Zork\Factory\AdapterInterface;
use Zork\Model\MapperAwareInterface;
use Grid\Tag\Model\TagsAwareInterface;
use Zork\Model\Exception\LogicException;
use Zend\View\Renderer\RendererInterface;
use Zork\Model\Structure\StructureAbstract;
use Grid\Paragraph\Model\Paragraph\StructureInterface;
use Zend\Permissions\Acl\Resource\ResourceInterface;

/**
 * ProxyAbstract
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
abstract class ProxyAbstract
       extends StructureAbstract
    implements AdapterInterface,
               ResourceInterface,
               StructureInterface,
               TagsAwareInterface,
               MapperAwareInterface,
               EditRestrictedInterface,
               AccessRestrictedInterface,
               DeleteRestrictedInterface
{

    /**
     * @var string
     */
    const PROPERTY_DRAG = 'drag';

    /**
     * @var string
     */
    const PROPERTY_DROP = 'drop';

    /**
     * @var string
     */
    const PROPERTY_EDIT = 'edit';

    /**
     * @var string
     */
    const PROPERTY_EDIT_CONTENT = 'editContent';

    /**
     * @var string
     */
    const PROPERTY_EDIT_LAYOUT = 'editLayout';

    /**
     * @var string
     */
    const PROPERTY_DELETE = 'delete';

    /**
     * @var string
     */
    const PROPERTY_REPRESENTS_TEXT = 'representsText';

    /**
     * @var string
     */
    const PROPERTY_REPRESENTS_IMAGES = 'representsImages';

    /**
     * @const string
     */
    const CONTAINER_TAG = 'div';

    /**
     * @const string
     */
    const PARAGRAPH_TAG = 'div';

    /**
     * @const string
     */
    const CONTENT_OPEN_TAG = 'div';

    /**
     * @const string
     */
    const CONTENT_CLOSE_TAG = 'div';

    /**
     * @const string
     */
    const CHILDREN_TAG = 'div';

    /**
     * This paragraph can be only child of ...
     *
     * @var string
     * @abstract
     */
    protected static $onlyChildOf   = '*';

    /**
     * This paragraph can be only parent of ...
     *
     * @var string
     * @abstract
     */
    protected static $onlyParentOf  = '*';

    /**
     * Locale-aware properties
     *
     * @var array
     * @abstract
     */
    protected static $localeAwareProperties = array(
        'title' => true,
    );

    /**
     * Paragraph type
     *
     * @var string
     * @abstract
     */
    protected static $type;

    /**
     * Paragraph-render view-open
     *
     * @var string
     * @abstract
     */
    protected static $viewOpen;

    /**
     * Paragraph-render view-close
     *
     * @var string
     * @abstract
     */
    protected static $viewClose;

    /**
     * Proxy base object
     *
     * @var \Paragraph\Model\Paragraph\Structure\ProxyBase
     */
    private $proxyBase;

    /**
     * Connected tag ids
     *
     * @var Content
     */
    private $tagIds = array();

    /**
     * Connected (locale-aware) tags
     *
     * @var Content
     */
    private $localeTags = array();

    /**
     * Property: acl-resource id
     *
     * @var string
     */
    private $aclResourceId;

    /**
     * Root paragraph
     *
     * @var Content
     */
    private $rootParagraph;

    /**
     * Constructor
     *
     * @param array $data
     * @throws \Zork\Model\Exception\LogicException if type does not match
     */
    public function __construct( $data = array() )
    {
        parent::__construct( $data );
        $proxyBase = $this->proxyBase;

        if ( empty( $proxyBase->type ) )
        {
            $proxyBase->type = static::$type;
        }
        else if ( ! empty( static::$type ) &&
                  static::$type !== $proxyBase->type )
        {
            throw new LogicException( 'Type does not match' );
        }
    }

    /**
     * Set option enhanced to be able to set id
     *
     * @param string $key
     * @param mixed $value
     * @return \Paragraph\Model\Paragraph\Structure\ProxyAbstract
     */
    public function setOption( $key, $value )
    {
        if ( 'id' == $key )
        {
            $this->proxyBase->setOption( $key, $value );
            return $this;
        }

        return parent::setOption( $key, $value );
    }

    /**
     * Set options
     *
     * @param mixed $options
     * @return \Paragraph\Model\Paragraph\Structure\ProxyAbstract
     */
    public function setOptions( $options )
    {
        if ( $options instanceof Traversable )
        {
            $options = iterator_to_array( $options );
        }

        if ( isset( $options['proxyBase'] ) )
        {
            $this->setProxyBase( $options['proxyBase'] );
            unset( $options['proxyBase'] );
        }

        return parent::setOptions( $options );
    }

    /**
     * Get proxy base object
     *
     * @return \Paragraph\Model\Paragraph\Structure\ProxyBase
     */
    public function setProxyBase( ProxyBase $proxyBase )
    {
        $this->proxyBase = $proxyBase;
        return $this;
    }

    /**
     * Get service-locator
     *
     * @return \Zend\ServiceManager\ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->proxyBase
                    ->getServiceLocator();
    }

    /**
     * Get dependent structures
     *
     * @return \Zork\Model\Structure\MapperAwareAbstract[]
     */
    public function getDependentStructures()
    {
        return array();
    }

    /**
     * Get the mapper object
     *
     * @return \Paragraph\Model\Paragraph\Mapper
     */
    public function getMapper()
    {
        return $this->proxyBase
                    ->getMapper();
    }

    /**
     * Set the mapper object
     *
     * @param \Paragraph\Model\Paragraph\Mapper $mapper
     * @return \Paragraph\Model\Paragraph\Structure\ProxyAbsract
     */
    public function setMapper( $mapper = null )
    {
        $this->proxyBase
             ->setMapper( $mapper );

        return $this;
    }

    /**
     * @return \Paragraph\Model\Paragraph\Structure\ProxyAbstract
     */
    public function prepareCreate()
    {
        $this->proxyBase->left  = 1;
        $this->proxyBase->right = 2;
        return $this;
    }

    /**
     * Save me
     *
     * @return int Number of affected rows
     */
    public function save()
    {
        $rows   = $this->getMapper()
                       ->save( $this );
        $rootId = $this->getRootId();

        if ( ! $rootId )
        {
            $this->setRootId( $rootId = $this->getId() );
        }

        return $rows;
    }

    /**
     * Delete me
     *
     * @return int Number of affected rows
     */
    public function delete()
    {
        return $this->getMapper()
                    ->delete( $this );
    }

    /**
     * Get the proxy-base
     *
     * @return \Paragraph\Model\Paragraph\Structure\ProxyBase
     */
    protected function & proxyBase()
    {
        return $this->proxyBase;
    }

    /**
     * Get ID of the paragraph
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->proxyBase->id;
    }

    /**
     * Get type of the paragraph
     *
     * @return string|null
     */
    public function getType()
    {
        return $this->proxyBase->type;
    }

    /**
     * Get name of the paragraph
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->proxyBase->name;
    }

    /**
     * Get name-label of the paragraph
     *
     * @return string|null
     */
    public function getNameLabel()
    {
        return $this->getName();
    }

    /**
     * Get root-ID of the paragraph
     *
     * @return int|null
     */
    public function getRootId()
    {
        return $this->proxyBase->rootId;
    }

    /**
     * Get tags of the paragraph
     *
     * @return array
     */
    public function getTags()
    {
        return $this->proxyBase->tags;
    }

    /**
     * Get tag ids of the paragraph
     *
     * @return array
     */
    public function getTagIds()
    {
        return $this->tagIds;
    }

    /**
     * Get locale-tags of the paragraph
     *
     * @return array
     */
    public function getLocaleTags()
    {
        return $this->localeTags;
    }

    /**
     * Set type of the paragraph
     *
     * @return string|null
     * @thorws \Zork\Model\Exception\LogicException
     */
    public function setType( $type )
    {
        if ( empty( static::$type ) )
        {
            $this->proxyBase->type = $type;
        }
        elseif ( static::$type != $type )
        {
            throw new LogicException( 'Cannot alter type after creation' );
        }

        return $this;
    }

    /**
     * Set name of the paragraph
     *
     * @return string|null
     */
    public function setName( $name )
    {
        $this->proxyBase->name = $name;
        return $this;
    }

    /**
     * Set root-ID of the paragraph
     *
     * @return int|null
     */
    public function setRootId( $rootId )
    {
        $this->proxyBase->rootId = $rootId;
        return $this;
    }

    /**
     * Set tags of the paragraph
     *
     * @return array
     */
    public function setTags( $tags )
    {
        $this->proxyBase->tags = array_filter( (array) $tags );
        return $this;
    }

    /**
     * Set tag ids of the paragraph
     *
     * @return array
     */
    public function setTagIds( $tagIds )
    {
        $this->tagIds = (array) $tagIds;
        return $this;
    }

    /**
     * Set locale-tags of the paragraph
     *
     * @return array
     */
    public function setLocaleTags( $localeTags )
    {
        $this->localeTags = (array) $localeTags;
        return $this;
    }

    /**
     * Get root paragraph
     *
     * @return  \Paragraph\Model\Paragraph\Structure\AbstractRoot
     */
    public function getRootParagraph()
    {
        if ( $this->proxyBase->rootId && (
                null === $this->rootParagraph ||
                $this->rootParagraph->id != $this->proxyBase->rootId
            ) )
        {
            $this->rootParagraph = $this->getMapper()
                                        ->find( $this->proxyBase->rootId );
        }

        return $this->rootParagraph;
    }

    /**
     * Render view-open
     *
     * @param \Zend\View\Renderer\RendererInterface $renderer
     * @return string
     */
    public function renderOpen( RendererInterface $renderer )
    {
        if ( ! empty( static::$viewOpen ) )
        {
            return $renderer->render( static::$viewOpen, array(
                'paragraph' => $this,
            ) );
        }

        return '';
    }

    /**
     * Render view-close
     *
     * @param \Zend\View\Renderer\RendererInterface $renderer
     * @return string
     */
    public function renderClose( RendererInterface $renderer )
    {
        if ( ! empty( static::$viewClose ) )
        {
            return $renderer->render( static::$viewClose, array(
                'paragraph' => $this,
            ) );
        }

        return '';
    }

    /**
     * Return additional attributes of paragraph tag
     *
     * @return  array
     */
    public function getAdditionalAttributes()
    {
        return array();
    }

    /**
     * Returns the base iterator (only basic properties)
     *
     * @return \Zork\Model\Structure\StructureIterator
     */
    public function getBaseIterator()
    {
        return $this->proxyBase
                    ->getIterator();
    }

    /**
     * Returns the properties iterator (only additional properties)
     *
     * @return \Zork\Model\Structure\StructureIterator
     */
    public function getPropertiesIterator()
    {
        return parent::getIterator();
    }

    /**
     * Get iterator
     *
     * @return \AppendIterator
     */
    public function getIterator()
    {
        $result = new AppendIterator;
        $result->append( $this->getBaseIterator() );
        $result->append( $this->getPropertiesIterator() );
        return $result;
    }

    /**
     * This paragraph can be only child of ...
     *
     * @return string
     */
    public static function onlyChildOf()
    {
        return static::$onlyChildOf;
    }

    /**
     * This paragraph can be only parent of ...
     *
     * @return string
     */
    public static function onlyParentOf()
    {
        return static::$onlyParentOf;
    }

    /**
     * This paragraph-type functions
     *
     * @return array
     */
    public static function getAllowedFunctions()
    {
        $properties = array(
            static::PROPERTY_DRAG,
            static::PROPERTY_DROP,
            static::PROPERTY_EDIT,
            static::PROPERTY_DELETE,
        );

        $class = get_called_class();

        if ( is_a( $class, __NAMESPACE__ . '\RepresentsTextContentInterface', true ) )
        {
            $properties[] = static::PROPERTY_REPRESENTS_TEXT;
        }

        if ( is_a( $class, __NAMESPACE__ . '\RepresentsImageContentsInterface', true ) )
        {
            $properties[] = static::PROPERTY_REPRESENTS_IMAGES;
        }

        return $properties;
    }

    /**
     * Is the provided property locale-aware?
     *
     * @param string $property
     * @return bool
     */
    public static function isPropertyLocaleAware( $property )
    {
        return ! empty( static::$localeAwareProperties[$property] );
    }

    /**
     * Returns the string identifier of the Resource
     *
     * @return string
     */
    public function getResourceId()
    {
        if ( null === $this->aclResourceId )
        {
            $this->aclResourceId = 'paragraph.' .
                $this->getType() . '.' . ( (int) $this->getId() );
        }

        return $this->aclResourceId;
    }

    /**
     * Get authenticated user
     *
     * @return \User\Model\User\Structure|null
     */
    private function getAuthenticatedUser()
    {
        $auth = $this->getServiceLocator()
                     ->get( 'Zend\Authentication\AuthenticationService' );

        if ( $auth->hasIdentity() )
        {
            return $auth->getIdentity();
        }

        return null;
    }

    /**
     * Is accessible for the logged-in user
     *
     * @return  bool
     */
    public function isAccessible()
    {
        $root = $this->getRootParagraph();

        if ( ! empty( $root->allAccess ) )
        {
            return true;
        }

        $user = $this->getAuthenticatedUser();

        if ( empty( $user ) )
        {
            return ! empty( $root->accessUsers ) && in_array( '', $root->accessUsers )
                || ! empty( $root->accessGroups ) && in_array( '', $root->accessGroups );
        }

        return ! empty( $root->accessUsers ) && in_array( $user->id, $root->accessUsers )
            || ! empty( $root->accessGroups ) && in_array( $user->groupId, $root->accessGroups )
            || $this->getServiceLocator()
                    ->get( 'Grid\User\Model\Permissions\Model' )
                    ->isAllowed( $root, 'view' );
    }

    /**
     * Is editable for the logged-in user
     *
     * @return  bool
     */
    public function isEditable()
    {
        $user = $this->getAuthenticatedUser();

        if ( empty( $user ) )
        {
            return false;
        }

        $root = $this->getRootParagraph();

        return ! empty( $root->editUsers ) && in_array( $user->id, $root->editUsers )
            || ! empty( $root->editGroups ) && in_array( $user->groupId, $root->editGroups )
            || $this->getServiceLocator()
                    ->get( 'Grid\User\Model\Permissions\Model' )
                    ->isAllowed( $root, 'edit' );
    }

    /**
     * Is editable for the logged-in user
     *
     * @return  bool
     */
    public function isDeletable()
    {
        $user = $this->getAuthenticatedUser();

        if ( empty( $user ) )
        {
            return false;
        }

        $root = $this->getRootParagraph();

        return ! empty( $root->editUsers ) && in_array( $user->id, $root->editUsers )
            || ! empty( $root->editGroups ) && in_array( $user->groupId, $root->editGroups )
            || $this->getServiceLocator()
                    ->get( 'Grid\User\Model\Permissions\Model' )
                    ->isAllowed( $root, $this->id == $this->rootId ? 'delete' : 'edit' );
    }

    /**
     * Return true if and only if $options accepted by this adapter
     * If returns float as likelyhood the max of these will be used as adapter
     *
     * @param array $options;
     * @return float
     */
    public static function acceptsOptions( array $options )
    {
        return isset( $options['type'] ) && $options['type'] === static::$type;
    }

    /**
     * Return a new instance of the adapter by $options
     *
     * @param array $options;
     * @return \Paragraph\Model\Paragraph\Structure\ProxyAbstract
     */
    public static function factory( array $options = null )
    {
        return new static( $options );
    }

}
