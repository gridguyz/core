<?php

namespace Grid\Paragraph\Model\Paragraph\Structure;

/**
 * Title
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Title extends AbstractLeaf
{

    /**
     * Paragraph type
     *
     * @var string
     */
    protected static $type = 'title';

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
    protected static $viewOpen = 'grid/paragraph/render/title';

    /**
     * Separator
     *
     * @var string
     */
    protected $separator = '/';

    /**
     * Set separator
     *
     * @param string $separator
     * @return \Paragraph\Model\Paragraph\Structure\Title
     */
    public function setSeparator( $separator )
    {
        $this->separator = trim( (string) $separator );
        return $this;
    }

}
