<?php

namespace Grid\Core\View\Helper\RowSet\Type;

/**
 * Enum
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Enum extends Translate
{

    /**
     * @var array
     */
    protected $values = array();

    /**
     * @var bool
     */
    protected $translationEnabled = true;

    /**
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * @param   array $values
     * @return  \Grid\Core\View\Helper\RowSet\Type\Enum
     */
    public function setValues( array $values )
    {
        $this->values = $values;
        return $this;
    }

    /**
     * @return array
     */
    public function getTranslationEnabled()
    {
        return $this->translationEnabled;
    }

    /**
     * @param   bool $translationEnabled
     * @return  \Grid\Core\View\Helper\RowSet\Type\Enum
     */
    public function setTranslationEnabled( $translationEnabled )
    {
        $this->translationEnabled = (bool) $translationEnabled;
        return $this;
    }

    /**
     * Create enumeration
     *
     * @param   array   $values
     * @param   string  $translatePrefix
     * @param   string  $translatePostfix
     * @param   string  $translateTextDomain
     * @param   bool    $translationEnabled
     */
    public function __construct( array $values          = array(),
                                 $translatePrefix       = null,
                                 $translatePostfix      = null,
                                 $translateTextDomain   = null,
                                 $translationEnabled    = true )
    {
        parent::__construct(
            $translatePrefix,
            $translatePostfix,
            $translateTextDomain
        );

        $this->setValues( $values )
             ->setTranslationEnabled( $translationEnabled );
    }

    /**
     * Display a single value
     *
     * @param string $value
     * @return string
     */
    public function displayValue( $value )
    {
        if ( isset( $this->values[$value] ) )
        {
            $value = $this->values[$value];
        }

        if ( $this->translationEnabled )
        {
            $value = parent::displayValue( $value );
        }

        return $value;
    }

}
