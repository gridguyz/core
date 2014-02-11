<?php

namespace Grid\Customize\View\Helper;

use Countable;
use Traversable;
use ArrayAccess;
use ArrayIterator;
use IteratorAggregate;
use Zork\Db\SiteInfo;
use Zork\Db\SiteInfoAwareTrait;
use Zork\Db\SiteInfoAwareInterface;
use Zend\View\Helper\Url;
use Zend\View\Helper\HeadLink;
use Zend\View\Helper\AbstractHelper;
use Grid\Customize\Model\Extra\Model as ExtraModel;

/**
 * CustomCss
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class CustomCss extends AbstractHelper
             implements Countable,
                        ArrayAccess,
                        IteratorAggregate,
                        SiteInfoAwareInterface
{

    use SiteInfoAwareTrait;

    /**
     * @var \Zend\View\Helper\HeadLink
     */
    protected $headLinkHelper;

    /**
     * @var \Zend\View\Helper\Url
     */
    protected $urlHelper;

    /**
     * @var int[]
     */
    protected $roots = array();

    /**
     * @var ExtraModel
     */
    protected $extraModel;

    /**
     * @param ExtraModel $customizeExtraModel
     */
    public function __construct( ExtraModel $customizeExtraModel,
                                 SiteInfo $siteInfo )
    {
        $this->extraModel = $customizeExtraModel;
        $this->setSiteInfo( $siteInfo );
    }

    /**
     * Retrieve the HeadLink helper
     *
     * @return \Zend\View\Helper\HeadLink
     * @codeCoverageIgnore
     */
    protected function getHeadLinkHelper()
    {
        if ( $this->headLinkHelper )
        {
            return $this->headLinkHelper;
        }

        if ( method_exists( $this->view, 'plugin' ) )
        {
            $this->headLinkHelper = $this->view
                                         ->plugin( 'headLink' );
        }

        if ( ! $this->headLinkHelper instanceof HeadLink )
        {
            $this->headLinkHelper = new HeadLink();
        }

        return $this->headLinkHelper;
    }

    /**
     * Retrieve the Url helper
     *
     * @return \Zend\View\Helper\Url
     * @codeCoverageIgnore
     */
    protected function getUrlHelper()
    {
        if ( $this->urlHelper )
        {
            return $this->urlHelper;
        }

        if ( method_exists( $this->view, 'plugin' ) )
        {
            $this->urlHelper = $this->view
                                    ->plugin( 'url' );
        }

        if ( ! $this->urlHelper instanceof Url )
        {
            $this->urlHelper = new Url();
        }

        return $this->urlHelper;
    }

    /**
     * Invokable helper
     *
     * @param   array|string    $set
     * @return  CustomCss
     */
    public function __invoke( $set = null )
    {
        if ( null !== $set )
        {
            if ( is_array( $set ) )
            {
                return $this->setRoots( $set );
            }
            else
            {
                return $this->offsetSet( null, $set );
            }
        }

        return $this;
    }

    /**
     * Get root
     *
     * @param   int|string  $offset
     * @return  array
     */
    public function & offsetGet( $offset )
    {
        return $this->roots[$offset];
    }

    /**
     * Set root
     *
     * @param   int|string  $offset
     * @param   array       $value
     * @return  \Zork\View\Helper\OpenGraph
     */
    public function offsetSet( $offset, $value )
    {
        $value = ( (int) $value ) ?: null;

        if ( null === $offset )
        {
            $this->roots[] = $value;
        }
        else
        {
            $this->roots[$offset] = $value;
        }

        return $this;
    }

    /**
     * Property exists
     *
     * @param   int|string  $offset
     * @return  bool
     */
    public function offsetExists( $offset )
    {
        return isset( $this->roots[$offset] );
    }

    /**
     * Unset property
     *
     * @param   int|string  $offset
     * @return  \Zork\View\Helper\OpenGraph
     */
    public function offsetUnset( $offset )
    {
        unset( $this->roots[$offset] );
        return $this;
    }

    /**
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     *
     * @return  Traversable
     */
    public function getIterator()
    {
        return new ArrayIterator( $this->roots );
    }

    /**
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     *
     * @return  int
     */
    public function count()
    {
        return count( $this->roots );
    }

    /**
     * Apply custom css
     *
     * @return  string
     */
    public function apply( $global = null )
    {
        if ( method_exists( $this->view, 'plugin' ) )
        {
            $roots      = (array) $this->roots;
            $siteInfo   = $this->getSiteInfo();
            $schema     = $siteInfo->getSchema();
            $url        = $this->getUrlHelper();
            $headLink   = $this->getHeadLinkHelper();

            if ( null === $global && in_array( null, $roots ) )
            {
                $global = true;
            }

            $roots = array_filter( $roots );

            if ( $global )
            {
                array_unshift( $roots, null );
            }

            $updated = $this->extraModel
                            ->findUpdated( $roots );

            foreach ( array_reverse( $roots ) as $root )
            {
                if ( isset( $updated[$root] ) )
                {
                    $headLink->prependStylesheet(
                        $url( 'Grid\Customize\Render\CustomCss', array(
                            'schema'    => $schema,
                            'id'        => $root ? (int) $root : 'global',
                            'hash'      => $updated[$root]->toHash(),
                        ) ),
                        'all'
                    );
                }
            }

            $this->roots = array();
        }

        return $this;
    }

}
