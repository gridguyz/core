<?php

namespace Grid\Core\View\Helper\RowSet\Type;

/**
 * Text
 * 
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Text extends AbstractHelper
{
    
    /**
     * @var int
     */
    protected $maxLength = 20;
    
    /**
     * @return int
     */
    public function getMaxLength()
    {
        return $this->maxLength;
    }
    
    /**
     * @param int $maxLength
     * @return \Core\View\Helper\RowSet\Type\Text
     */
    public function setMaxLength( $maxLength )
    {
        $this->maxLength = (int) $maxLength;
        return $this; 
    }
    
    /**
     * @param int $maxLength
     */
    public function __construct( $maxLength = null )
    {
        if ( null !== $maxLength )
        {
            $this->setMaxLength( $maxLength );
        }
    }
    
    /**
     * Display a single value
     * 
     * @param string $value
     * @return string
     */
    public function displayValue( $value )
    {
        $value = (string) $value;
        
        if ( mb_strlen( $value, 'UTF-8' ) > $this->maxLength )
        {
            $more  = $this->view->translate( 'default.rowSet.more' );
            $value = mb_substr( $value, 0, $this->maxLength - 4, 'UTF-8' ) .
                ' ... ' .
                '<button type="button" onclick="js.require(\'js.ui.'.
                    'dialog\')({\'title\':\'' . $this->view->escapeJs( $more ) .
                    '\',\'message\':\'' . $this->view->escapeJs(
                        $this->view->escapeHtml( $value )
                    ) . '\'});">' .
                    $this->view->escapeHtml( $more ) .
                '</button>';
        }
        
        return $value;
    }
    
}
