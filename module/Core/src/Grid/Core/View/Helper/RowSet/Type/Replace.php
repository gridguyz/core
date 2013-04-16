<?php

namespace Grid\Core\View\Helper\RowSet\Type;

/**
 * Replace
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Replace extends String
{

    /**
     * @var string
     */
    protected $from     = "\n";

    /**
     * @var string
     */
    protected $to       = "<br />\n";

    /**
     * @var bool
     */
    protected $regular  = false;

    /**
     * @return string
     */
    protected function getFrom()
    {
        return $this->from;
    }

    /**
     * @param string|array $from
     * @return \Core\View\Helper\RowSet\Type\Replace
     */
    protected function setFrom( $from )
    {
        $this->from = $from;
        return $this;
    }

    /**
     * @return string
     */
    protected function getTo()
    {
        return $this->to;
    }

    /**
     * @param string|array $to
     * @return \Core\View\Helper\RowSet\Type\Replace
     */
    protected function setTo( $to )
    {
        $this->to = $to;
        return $this;
    }

    /**
     * @return bool
     */
    protected function getUseRegularExpression()
    {
        return $this->regular;
    }

    /**
     * @param bool $use
     * @return \Core\View\Helper\RowSet\Type\Replace
     */
    protected function setUseRegularExpression( $use = true )
    {
        $this->regular = (bool) $use;
        return $this;
    }

    /**
     * @param string|array $from
     * @param string|array $to
     * @param bool $regular
     */
    public function __construct( $from      = null,
                                 $to        = null,
                                 $regular   = null )
    {
        if ( null !== $from )
        {
            $this->setFrom( $from );
        }

        if ( null !== $to )
        {
            $this->setTo( $to );
        }

        if ( null !== $regular )
        {
            $this->setUseRegularExpression( $regular );
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
        if ( $this->getUseRegularExpression() )
        {
            return preg_replace( $this->getFrom(), $this->getTo(), $value );
        }
        else
        {
            return str_replace( $this->getFrom(), $this->getTo(), $value );
        }
    }

}
