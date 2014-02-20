<?php

namespace Grid\Customize\View\Helper;

use Countable;
use Traversable;
use ArrayAccess;
use ArrayIterator;
use IteratorAggregate;
use Zork\Stdlib\Message;
use Zork\Db\SiteInfo;
use Zork\Db\SiteInfoAwareTrait;
use Zork\Db\SiteInfoAwareInterface;
use Zend\View\Helper\Url;
use Zend\View\Helper\HeadLink;
use Zork\View\Helper\Messenger;
use Zend\I18n\View\Helper\Translate;
use Zend\View\Helper\AbstractHelper;
use Grid\Customize\Service\CssPreview;
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
     * @var \Zork\View\Helper\Messenger
     */
    protected $messengerHelper;

    /**
     * @var \Zend\I18n\View\Helper\Translate
     */
    protected $translateHelper;

    /**
     * @var CssPreview
     */
    protected $cssPreview;

    /**
     * @var int[]
     */
    protected $roots = array();

    /**
     * @var ExtraModel
     */
    protected $extraModel;

    /**
     * @param   ExtraModel  $customizeExtraModel
     * @param   CssPreview  $customizeCssPreview
     * @param   SiteInfo    $siteInfo
     */
    public function __construct( ExtraModel $customizeExtraModel,
                                 CssPreview $customizeCssPreview,
                                 SiteInfo $siteInfo )
    {
        $this->extraModel = $customizeExtraModel;
        $this->cssPreview = $customizeCssPreview;
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
     * Retrieve the Messenger helper
     *
     * @return \Zork\View\Helper\Messenger
     * @codeCoverageIgnore
     */
    protected function getMessengerHelper()
    {
        if ( $this->messengerHelper )
        {
            return $this->messengerHelper;
        }

        if ( method_exists( $this->view, 'plugin' ) )
        {
            $this->messengerHelper = $this->view
                                          ->plugin( 'messenger' );
        }

        if ( ! $this->messengerHelper instanceof Messenger )
        {
            $this->messengerHelper = new Messenger();
        }

        return $this->messengerHelper;
    }

    /**
     * Retrieve the Translate helper
     *
     * @return \Zend\I18n\View\Helper\Translate
     * @codeCoverageIgnore
     */
    protected function getTranslateHelper()
    {
        if ( $this->translateHelper )
        {
            return $this->translateHelper;
        }

        if ( method_exists( $this->view, 'plugin' ) )
        {
            $this->translateHelper = $this->view
                                          ->plugin( 'translate' );
        }

        if ( ! $this->translateHelper instanceof Translate )
        {
            $this->translateHelper = new Translate();
        }

        return $this->translateHelper;
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
                return $this->append( $set );
            }
        }

        return $this;
    }

    /**
     * Get offset
     *
     * @param   int|string  $offset
     * @return  int
     */
    public function & offsetGet( $offset )
    {
        return $this->roots[$offset];
    }

    /**
     * Set offset
     *
     * @param   int|string|null $offset
     * @param   int|null        $value
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
     * Append
     *
     * @param   int|null    $value
     * @return  \Zork\View\Helper\OpenGraph
     */
    public function append( $value )
    {
        $this->roots[] = ( (int) $value ) ?: null;
        return $this;
    }

    /**
     * Use global
     *
     * @param   int|null    $value
     * @return  \Zork\View\Helper\OpenGraph
     */
    public function useGlobal( $use = true )
    {
        $this->roots = array_filter( $this->roots );

        if ( $use )
        {
            $this->roots[] = null;
        }

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
     * @return  CustomCss
     */
    public function apply()
    {
        if ( method_exists( $this->view, 'plugin' ) )
        {
            $global     = false;
            $roots      = (array) $this->roots;
            $headLink   = $this->getHeadLinkHelper();

            if ( in_array( null, $roots ) )
            {
                $global = true;
            }

            $roots = array_filter( $roots );

            if ( $global )
            {
                $roots[] = null;
            }

            $find       = array();
            $urls       = array();
            $preview    = false;

            foreach ( $roots as $root )
            {
                if ( $this->cssPreview->hasPreviewById( $root ) )
                {
                    $urls[$root] = $this->cssPreview
                                        ->getPreviewById( $root );
                }

                if ( empty( $urls[$root] ) )
                {
                    $find[] = $root;
                }
                else
                {
                    $preview = true;
                }
            }

            if ( ! empty( $find ) )
            {
                $url        = $this->getUrlHelper();
                $siteInfo   = $this->getSiteInfo();
                $schema     = $siteInfo->getSchema();
                $updated    = $this->extraModel
                                   ->findUpdated( $find );

                foreach ( $find as $root )
                {
                    if ( isset( $updated[$root] ) )
                    {
                        $urls[$root] = $url( 'Grid\Customize\Render\CustomCss', array(
                            'schema'    => $schema,
                            'id'        => $root ? (int) $root : 'global',
                            'hash'      => $updated[$root]->toHash(),
                        ) );
                    }
                }
            }

            foreach ( array_reverse( $roots ) as $root )
            {
                if ( ! empty( $urls[$root] ) )
                {
                    $id = $root ? (int) $root : 'global';

                    $headLink->prependStylesheet(
                        $urls[$root],
                        'all',
                        false,
                        array(
                            'class'             => 'customize-stylesheet',
                            'data-customize'    => $id,
                        )
                    );
                }
            }

            if ( $preview )
            {
                if ( empty( $url ) )
                {
                    $url = $this->getUrlHelper();
                }

                $translate = $this->getTranslateHelper();

                $this->getMessengerHelper()
                     ->add(
                         sprintf(
                             $translate(
                                 'customize.preview.applied.reset-link.%s',
                                 'customize'
                             ),
                             $url( 'Grid\Customize\CssAdmin\ResetPreviews', array(
                                 'locale' => \Locale::getDefault(),
                             ) )
                         ),
                         false,
                         Message::LEVEL_WARN
                     );
            }

            $this->roots = array();
        }

        return $this;
    }

}
