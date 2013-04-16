<?php

namespace Grid\Core\View\Helper\RowSet\Type;

/**
 * Translate
 * 
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Translate extends AbstractHelper
{
    
    /**
     * @var string
     */
    protected $translatePrefix      = '';
    
    /**
     * @var string
     */
    protected $translatePostfix     = '';
    
    /**
     * @var string
     */
    protected $translateTextDomain  = 'default';
    
    /**
     * @return string
     */
    public function getTranslatePrefix()
    {
        return $this->translatePrefix;
    }
    
    /**
     * @param string $translatePrefix
     * @return \Core\View\Helper\RowSet
     */
    public function setTranslatePrefix( $translatePrefix )
    {
        $this->translatePrefix = (string) $translatePrefix;
        return $this;
    }
    
    /**
     * @return string
     */
    public function getTranslatePostfix()
    {
        return $this->translatePostfix;
    }
    
    /**
     * @param string $translatePostfix
     * @return \Core\View\Helper\RowSet
     */
    public function setTranslatePostfix( $translatePostfix )
    {
        $this->translatePostfix = (string) $translatePostfix;
        return $this;
    }
    
    /**
     * @return string
     */
    public function getTranslateTextDomain()
    {
        return $this->translateTextDomain;
    }
    
    /**
     * @param string $translateTextDomain
     * @return \Core\View\Helper\RowSet
     */
    public function setTranslateTextDomain( $translateTextDomain )
    {
        $this->translateTextDomain = (string) $translateTextDomain;
        return $this;
    }
    
    /**
     * @param string $translatePrefix
     * @param string $translatePostfix
     * @param string $translateTextDomain
     */
    public function __construct( $translatePrefix = null,
                                 $translatePostfix = null,
                                 $translateTextDomain = null )
    {
        if ( null !== $translatePrefix )
        {
            $this->setTranslatePrefix( $translatePrefix );
        }
        
        if ( null !== $translatePostfix )
        {
            $this->setTranslatePostfix( $translatePostfix );
        }
        
        if ( null !== $translateTextDomain )
        {
            $this->setTranslateTextDomain( $translateTextDomain );
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
        return $this->view->translate(
            ( empty( $this->translatePrefix )
              ? '' : $this->translatePrefix . '.' ) .
            $value .
            ( empty( $this->translatePostfix )
              ? '' : '.' . $this->translatePostfix ),
            $this->translateTextDomain
        );
    }
    
}
