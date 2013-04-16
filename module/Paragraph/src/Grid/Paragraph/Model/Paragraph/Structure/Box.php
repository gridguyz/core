<?php

namespace Grid\Paragraph\Model\Paragraph\Structure;

/**
 * Box
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Box extends AbstractContainer
{

    /**
     * Paragraph type
     *
     * @var string
     */
    protected static $type = 'box';

    /**
     * Locale-aware properties
     *
     * @var array
     */
    protected static $localeAwareProperties = array(
        'title' => true,
    );

    /**
     * This paragraph can be only child of ...
     *
     * @var string
     */
    protected static $onlyChildOf = 'boxes';

    /**
     * Paragraph-render view-open
     *
     * @var string
     */
    protected static $viewOpen = 'grid/paragraph/render/box';

    /**
     * Box-title
     *
     * @var string
     */
    public $title = '';

    /**
     * @return \Paragraph\Model\Paragraph\Structure\Column
     */
    public function prepareCreate()
    {
        $mapper = $this->getMapper();

        $this->bindChild( $mapper->create( array(
            'type' => 'html',
        ) ) );

        return parent::prepareCreate();
    }

}
