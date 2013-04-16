<?php

namespace Grid\Paragraph\Model\Paragraph\Structure;

/**
 * Boxes
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Boxes extends AbstractContainer
{

    /**
     * Paragraph type
     *
     * @var string
     */
    protected static $type = 'boxes';

    /**
     * Locale-aware properties
     *
     * @var array
     */
    protected static $localeAwareProperties = array();

    /**
     * This paragraph can be only parent of ...
     *
     * @var string
     */
    protected static $onlyParentOf = 'box';

    /**
     * @return \Paragraph\Model\Paragraph\Structure\Columns
     */
    public function prepareCreate()
    {
        $mapper = $this->getMapper();

        $this->bindChild( $mapper->create( array(
            'type' => 'box',
        ) ) );

        return parent::prepareCreate();
    }

}
