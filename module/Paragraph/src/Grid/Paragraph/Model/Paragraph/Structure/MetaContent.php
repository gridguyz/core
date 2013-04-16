<?php

namespace Grid\Paragraph\Model\Paragraph\Structure;

/**
 * Content
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class MetaContent extends AbstractRoot
               implements LayoutAwareInterface
{

    /**
     * Paragraph type
     *
     * @var string
     */
    protected static $type = 'metaContent';

    /**
     * Locale-aware properties
     *
     * @var array
     */
    protected static $localeAwareProperties = array(
        'title'             => true,
        'metaKeywords'      => true,
        'metaDescription'   => true,
    );

    /**
     * Paragraph-render view-open
     *
     * @var string
     */
    protected static $viewOpen = 'grid/paragraph/render/metaContent';

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
     * Translator
     *
     * @var \Zork\I18n\Translator\Translator
     */
    private $_translator;

    /**
     * Get translator
     *
     * @return \Zork\I18n\Translator\Translator
     */
    protected function getTranslator()
    {
        if ( null === $this->_translator )
        {
            $this->_translator = $this->getServiceLocator()
                                      ->get( 'Zend\I18n\Translator\Translator' );
        }

        return $this->_translator;
    }

    /**
     * Get name-label of the paragraph
     *
     * @return string|null
     */
    public function getNameLabel()
    {
        return $this->getTranslator()
                    ->translate(
                        'paragraph.metaContent.' . parent::getNameLabel(),
                        'paragraph'
                    );
    }

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
        $mapper = $this->getMapper();

        $this->bindChild( $mapper->create( array(
            'type' => 'contentPlaceholder',
        ) ) );

        return parent::prepareCreate();
    }

}
