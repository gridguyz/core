<?php

namespace Grid\Paragraph\Model\Paragraph\Structure;

/**
 * Columns
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Columns extends AbstractContainer
{

    /**
     * Paragraph type
     *
     * @var string
     */
    protected static $type = 'columns';

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
    protected static $onlyParentOf = 'column';

    /**
     * @var int
     */
    private $columnCount = 1;

    /**
     * @var array
     */
    private $columnIds      = null;

    /**
     * @var array
     */
    private $columnWidths   = null;

    /**
     * @param int $count
     * @return \Paragraph\Model\Paragraph\Structure\Columns
     */
    protected function setColumnCount( $count = 1 )
    {
        $this->columnCount = max( 1, $count );
        return $this;
    }

    /**
     * @return \Paragraph\Model\Paragraph\Structure\Columns
     */
    public function prepareCreate()
    {
        $mapper = $this->getMapper();
        $width  = max( 1, (int) ( 100 / $this->columnCount ) );
        $currw  = 100;

        for ( $i = $this->columnCount; $i > 0; --$i )
        {
            if ( $i === 1 )
            {
                $initialWidth = $currw;
            }
            else
            {
                $initialWidth = $width;
                $currw -= $width;
            }

            $this->bindChild( $mapper->create( array(
                'type'          => 'column',
                'initialWidth'  => $initialWidth,
            ) ) );
        }

        return parent::prepareCreate();
    }

    /**
     * @return array
     */
    private function getColumnIds()
    {
        if ( null === $this->columnIds && ( $id = $this->getId() ) )
        {
            $ids = $this->getMapper()
                        ->findChildrenIdsByType( $id );

            $this->columnIds = array_keys( array_filter(
                $ids,
                function ( $val ) {
                    return $val == 'column';
                }
            ) );
        }

        return $this->columnIds;
    }

    /**
     * @return array :column-id => :width
     */
    protected function getColumnWidths()
    {
        if ( null === $this->columnWidths &&
             null !== ( $ids = $this->getColumnIds() ) )
        {
            $this->columnWidths = array();
            $ruleModel          = $this->getServiceLocator()
                                       ->get( 'Grid\Customize\Model\Rule\Model' );

            foreach ( (array) $this->getColumnIds() as $id )
            {
                $selector = sprintf( Column::WIDTH_SELECTOR, $id );
                $this->columnWidths[$id] = (int) $ruleModel->findBySelector( $selector )
                                                           ->getPropertyValue( 'width' );
            }
        }

        return $this->columnWidths;
    }

    /**
     * @param array $widths
     * @return \Paragraph\Model\Paragraph\Structure\Columns
     */
    protected function setColumnWidths( array $widths )
    {
        $ids = (array) $this->getColumnIds();

        foreach ( $widths as $id => $width )
        {
            if ( ! in_array( $id, $ids ) )
            {
                unset( $widths[$id] );
            }
        }

        foreach ( $ids as $id )
        {
            if ( empty( $widths[$id] ) )
            {
                $widths[$id] = 1;
            }
        }

        $last   = null;
        $all    = 100;
        $sum    = array_sum( $widths );

        foreach ( $widths as $id => & $width )
        {
            $width = (int) ( $width * 100 / $sum );
            $all  -= $width;
            $last &= $width;
        }

        if ( null !== $last )
        {
            $last += $all;
        }

        $this->columnWidths = $widths;
        return $this;
    }

    /**
     * Save me
     *
     * @return int Number of rows affected
     */
    public function save()
    {
        $rows = parent::save();

        if ( $rows && null !== $this->columnWidths )
        {
            $ruleModel = $this->getServiceLocator()
                              ->get( 'Grid\Customize\Model\Rule\Model' );

            foreach ( $this->columnWidths as $id => $width )
            {
                $selector   = sprintf( Column::WIDTH_SELECTOR, $id );
                $rule       = $ruleModel->findBySelector( $selector );

                $rule->setProperty(
                    'width',
                    ( (int) $width ) . '%',
                    $rule::PRIORITY_IMPORTANT
                );

                $rows += $rule->save();
            }
        }

        return $rows;
    }

    /**
     * Convert to array
     *
     * @return array
     */
    public function toArray()
    {
        $data = parent::toArray();
        $data['columnWidths'] = $this->getColumnWidths();
        return $data;
    }

}
