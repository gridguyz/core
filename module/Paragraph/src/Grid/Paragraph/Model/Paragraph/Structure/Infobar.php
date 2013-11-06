<?php

namespace Grid\Paragraph\Model\Paragraph\Structure;

use ArrayIterator;
use Zork\Stdlib\DateTime;
use Zend\ServiceManager\Exception\ServiceNotFoundException;

/**
 * Infobar
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Infobar extends AbstractLeaf
           implements ContentDependentAwareInterface
{

    use ContentDependentAwareTrait;

    /**
     * @const string
     */
    const SKIN_LEFT = 'left';

    /**
     * @const string
     */
    const SKIN_RIGHT = 'right';

    /**
     * @const string
     */
    const DEFAULT_SKIN = self::SKIN_LEFT;

    /**
     * Paragraph type
     *
     * @var string
     */
    protected static $type = 'infobar';

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
    protected static $viewOpen = 'grid/paragraph/render/infobar';

    /**
     * @var string
     */
    public $skin = self::DEFAULT_SKIN;

    /**
     * @var bool
     */
    protected $displayUserAvatar = true;

    /**
     * @var bool
     */
    protected $displayUserDisplayName = true;

    /**
     * @var bool
     */
    protected $displayPublishedDate = true;

    /**
     * Set display user-avatar
     *
     * @param   bool    $display
     * @return  \Paragraph\Model\Paragraph\Structure\Infobar
     */
    public function setDisplayUserAvatar( $display )
    {
        $this->displayUserAvatar = (bool) $display;
        return $this;
    }

    /**
     * Set display user's display-name
     *
     * @param   bool    $display
     * @return  \Paragraph\Model\Paragraph\Structure\Infobar
     */
    public function setDisplayUserDisplayName( $display )
    {
        $this->displayUserDisplayName = (bool) $display;
        return $this;
    }

    /**
     * Set display published date
     *
     * @param   bool    $display
     * @return  \Paragraph\Model\Paragraph\Structure\Infobar
     */
    public function setDisplayPublishedDate( $display )
    {
        $this->displayPublishedDate = (bool) $display;
        return $this;
    }

    /**
     * Get the rendered content
     *
     * @return mixed|null
     */
    protected function getRenderedContent()
    {
        try
        {
            return $this->getServiceLocator()
                        ->get( 'RenderedContent' );
        }
        catch ( ServiceNotFoundException $ex )
        {
            return null;
        }
    }

    /**
     * Get user
     *
     * @return UserStructure
     */
    public function getRenderedUser()
    {
        $rendered = $this->getRenderedContent();

        if ( $rendered instanceof Content )
        {
            return $rendered->user;
        }

        return null;
    }

    /**
     * Get published date
     *
     * @return \DateTime
     */
    public function getRenderedPublished()
    {
        $rendered = $this->getRenderedContent();

        if ( $rendered instanceof Content )
        {
            return DateTime::max(
                $rendered->publishedFrom,
                $rendered->created
            );
        }

        return null;
    }

    /**
     * Get root's created time
     *
     * @return  string|null
     */
    public function getRootCreated()
    {
        $content = $this->getDependentContent();

        if ( $content )
        {
            return $content->created;
        }

        return null;
    }

    /**
     * Set root's created time
     *
     * @param   string  $value
     * @return  \Paragraph\Model\Paragraph\Structure\Infobar
     */
    public function setRootCreated( $value )
    {
        $content = $this->getDependentContent();

        if ( $content )
        {
            $content->created = $value;
        }

        return $this;
    }

    /**
     * Get root's user-id
     *
     * @return  string|null
     */
    public function getRootUserId()
    {
        $content = $this->getDependentContent();

        if ( $content )
        {
            return $content->userId;
        }

        return null;
    }

    /**
     * Set root's user-id
     *
     * @param   string  $value
     * @return  \Paragraph\Model\Paragraph\Structure\Infobar
     */
    public function setRootUserId( $value )
    {
        $content = $this->getDependentContent();

        if ( $content )
        {
            $content->userId = $value;
        }

        return $this;
    }

    /**
     * Get iterator
     *
     * @return \AppendIterator
     */
    public function getIterator()
    {
        $iterator   = parent::getIterator();
        $content    = $this->getDependentContent();

        if ( $content )
        {
            $iterator->append( new ArrayIterator( array(
                'rootCreated'   => $content->created,
                'rootUserId'    => $content->userId,
            ) ) );
        }

        return $iterator;
    }

}
