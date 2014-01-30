<?php

namespace Grid\Paragraph\Model\Paragraph\Structure;

/**
 * Columns
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Column extends AbstractContainer
{

    /**
     * @const string
     */
    const WIDTH_SELECTOR = '#paragraph-%s-container.paragraph-container.paragraph-column-container';

    /**
     * Paragraph type
     *
     * @var string
     */
    protected static $type = 'column';

    /**
     * Locale-aware properties
     *
     * @var array
     */
    protected static $localeAwareProperties = array();

    /**
     * This paragraph can be only child of ...
     *
     * @var string
     */
    protected static $onlyChildOf = 'columns';

    /**
     * @var int
     */
    private $initialWidth = null;

    /**
     * @param int $width
     * @return \Paragraph\Model\Paragraph\Structure\Column
     */
    protected function setInitialWidth( $width )
    {
        $this->initialWidth = min( max( 1, $width ), 100 );
        return $this;
    }

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

    /**
     * Save me
     *
     * @return int Number of affected rows
     */
    public function save()
    {
        $rows = parent::save();

        if ( $rows && null !== $this->initialWidth && ( $id = $this->getId() ) )
        {
            $rule = $this->getServiceLocator()
                         ->get( 'Grid\Customize\Model\Rule\Model' )
                         ->findBySelector( sprintf( static::WIDTH_SELECTOR, $id ) );

            $rule->setProperty(
                'width',
                ( (int) $this->initialWidth ) . '%',
                $rule::PRIORITY_IMPORTANT
            );

            $rule->rootParagraphId = $this->getRootId() ?: $id;
            $rows += $rule->save();
        }

        return $rows;
    }

}
