<?php

namespace Grid\Core\View\Helper\RowSet\Type;

/**
 * Set type for rowSet helper
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Set extends Enum
{

    /**
     * @var string
     */
    protected $separator = ', ';

    /**
     * @var string
     */
    protected $splitter = "\n";

    /**
     * @return string
     */
    public function getSeparator()
    {
        return $this->separator;
    }

    /**
     * @param   string $separator
     * @return  \Grid\Core\View\Helper\RowSet\Type\Set
     */
    public function setSeparator( $separator )
    {
        $this->separator = (string) $separator;
        return $this;
    }

    /**
     * @return string
     */
    public function getSplitter()
    {
        return $this->splitter;
    }

    /**
     * @param   string $splitter
     * @return  \Grid\Core\View\Helper\RowSet\Type\Set
     */
    public function setSplitter( $splitter )
    {
        $this->splitter = (string) $splitter;
        return $this;
    }

    /**
     * Create enumeration
     *
     * @param   array   $values
     * @param   string  $translatePrefix
     * @param   string  $translatePostfix
     * @param   string  $translateTextDomain
     * @param   string  $separator
     * @param   bool    $translationEnabled
     * @param   string  $splitter
     */
    public function __construct( array $values          = array(),
                                 $translatePrefix       = null,
                                 $translatePostfix      = null,
                                 $translateTextDomain   = null,
                                 $separator             = null,
                                 $translationEnabled    = true,
                                 $splitter              = null )
    {
        parent::__construct(
            $values,
            $translatePrefix,
            $translatePostfix,
            $translateTextDomain,
            $translationEnabled
        );

        if ( null !== $separator )
        {
            $this->setSeparator( $separator );
        }

        if ( null !== $splitter )
        {
            $this->setSplitter( $splitter );
        }
    }

    /**
     * Display a single value
     *
     * @param   string|array $value
     * @return  string
     */
    public function displayValue( $values )
    {
        if ( is_scalar( $values ) && ! empty( $this->splitter ) )
        {
            $values = explode( $this->splitter, $values );
        }

        $result = '';
        $first  = true;
        $values = (array) $values;

        foreach ( $values as $value )
        {
            if ( $first )
            {
                $first = false;
            }
            else
            {
                $result .= $this->separator;
            }

            $result .= parent::displayValue( $value );
        }

        return $result;
    }

}
