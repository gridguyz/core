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
{

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
        $root = $this->getRootParagraph();
        return $root instanceof Content ? $root->created : null;
    }

    /**
     * Set root's created time
     *
     * @param   string  $value
     * @return  \Paragraph\Model\Paragraph\Structure\Infobar
     */
    public function setRootCreated( $value )
    {
        $root = $this->getRootParagraph();

        if ( $root instanceof Content )
        {
            $root->created = $value;
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
        $root = $this->getRootParagraph();
        return $root instanceof Content ? $root->userId : null;
    }

    /**
     * Set root's user-id
     *
     * @param   string  $value
     * @return  \Paragraph\Model\Paragraph\Structure\Infobar
     */
    public function setRootUserId( $value )
    {
        $root = $this->getRootParagraph();

        if ( $root instanceof Content )
        {
            $root->userId = $value;
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
        $iterator = parent::getIterator();

        if ( $this->rootId )
        {
            $iterator->append( new ArrayIterator( array(
                'rootCreated'   => $this->getRootCreated(),
                'rootUserId'    => $this->getRootUserId(),
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
        $root       = $this->getRootParagraph();

        if ( $root instanceof Content )
        {
            $dependents[] = $root;
        }

        return $dependents;
    }

}
