<?php

namespace Grid\Menu\Model\Menu\Structure;

use Traversable;
use AppendIterator;
use Zork\Factory\AdapterInterface;
use Zork\Model\MapperAwareInterface;
use Zork\Model\Exception\LogicException;
use Zork\Model\Structure\StructureAbstract;
use Grid\Menu\Model\Menu\StructureInterface;

/**
 * ProxyAbstract
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
abstract class ProxyAbstract
       extends StructureAbstract
    implements AdapterInterface,
               StructureInterface,
               MapperAwareInterface
{

    /**
     * Menu type
     *
     * @var string
     * @abstract
     */
    protected static $type;

    /**
     * Proxy base object
     *
     * @var \Menu\Model\Menu\Structure\ProxyBase
     */
    private $proxyBase;

    /**
     * Cached children
     *
     * @var \Menu\Model\Menu\Structure\ProxyAbstract[]
     */
    private $children = array();

    /**
     * Cached request uri
     *
     * @var string
     */
    private $requestUri;

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
     * @return \Menu\Model\Menu\Structure\ProxyAbstract
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
     * @return \Menu\Model\Menu\Structure\ProxyAbstract
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
     * @return \Menu\Model\Menu\Structure\ProxyBase
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
     * @param \Menu\Model\Menu\Mapper $mapper
     * @return \Menu\Model\Menu\Structure\ProxyAbsract
     */
    public function setMapper( $mapper = null )
    {
        $this->proxyBase
             ->setMapper( $mapper );

        return $this;
    }

    /**
     * Save me
     *
     * @return int Number of affected rows
     */
    public function save()
    {
        return $this->getMapper()
                    ->save( $this );;
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
     * Get label of the paragraph
     *
     * @return string|null
     */
    public function getLabel()
    {
        return $this->proxyBase->label;
    }

    /**
     * Get target of the paragraph
     *
     * @return string|null
     */
    public function getTarget()
    {
        return $this->proxyBase->target;
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
     * Set label of the paragraph
     *
     * @return string|null
     */
    public function setLabel( $label )
    {
        $this->proxyBase->label = ( (string) $label ) ?: null;
        return $this;
    }

    /**
     * Set target of the paragraph
     *
     * @return string|null
     */
    public function setTarget( $target )
    {
        $this->proxyBase->target = ( (string) $target ) ?: null;
        return $this;
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
     * Get visibility
     *
     * @return bool
     */
    public function isVisible()
    {
        return true;
    }

    /**
     * Add a cached child
     *
     * @param \Menu\Model\Menu\Structure\ProxyAbstract $child
     * @return \Menu\Model\Menu\Structure\ProxyAbstract
     */
    public function addChild( ProxyAbstract $child )
    {
        $this->children[] = $child;
        return $this;
    }

    /**
     * Has cached children?
     *
     * @return bool
     */
    public function hasChildren()
    {
        return ! empty( $this->children );
    }

    /**
     * Get cached children
     *
     * @return \Menu\Model\Menu\Structure\ProxyAbstract[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Is this menu-item is active?
     *
     * @param string $requestUri [optional]
     * @return bool
     */
    public function isActive( $requestUri = null )
    {
        $uri = rawurldecode( preg_replace( '/[\\?#].*$/', '', (string) $this->getUri() ) );

        if ( empty( $uri ) || '/' !== $uri[0] )
        {
            return false;
        }

        if ( null === $requestUri )
        {
            if ( null === $this->requestUri )
            {
                $this->requestUri = $this->getServiceLocator()
                                         ->get( 'Request' )
                                         ->getRequestUri();

                $this->requestUri = preg_replace(
                    '/\\?.*$/', '',
                    $this->requestUri
                );
            }

            $requestUri = $this->requestUri;
        }
        else
        {
            $requestUri = preg_replace(
                '/[\\?#].*$/', '',
                (string) $requestUri
            );
        }

        $requestUri = rawurldecode( $requestUri );

        return $requestUri === $uri || (
            mb_strlen( $requestUri ) > mb_strlen( $uri ) &&
            mb_substr( $requestUri, 0, mb_strlen( $uri ) + 1 ) === ( $uri . '/' )
        );
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

    /**
     * Get URI of this menu-item
     *
     * @return string
     */
    abstract public function getUri();

}
