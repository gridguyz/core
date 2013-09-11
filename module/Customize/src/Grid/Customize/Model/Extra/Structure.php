<?php

namespace Grid\Customize\Model\Extra;

use Zork\Model\Structure\MapperAwareAbstract;

/**
 * Structure
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Structure extends MapperAwareAbstract
{

    /**
     * @const string
     */
    const DEFAULT_CSS = '@charset "utf-8";';

    /**
     * @const int
     */
    const DEFAULT_CSS_MATCH_LENGTH = 8;

    /**
     * @var string
     */
    protected $css;

    /**
     * Get css
     *
     * @return  string
     */
    public function getCss()
    {
        if ( empty( $this->css ) )
        {
            return static::DEFAULT_CSS . PHP_EOL . PHP_EOL;
        }

        return $this->css;
    }

    /**
     * Set css
     *
     * @param   string  $css
     * @return  Structure
     */
    protected function setCss( $css )
    {
        $css = trim( $css ) . PHP_EOL;
        $len = static::DEFAULT_CSS_MATCH_LENGTH;

        if ( strlen( $css ) < $len ||
             substr( $css, 0, $len ) != substr( static::DEFAULT_CSS, 0, $len ) )
        {
            $css = static::DEFAULT_CSS . PHP_EOL . PHP_EOL . $css;
        }

        $this->css = $css;
        return $this;
    }

}
