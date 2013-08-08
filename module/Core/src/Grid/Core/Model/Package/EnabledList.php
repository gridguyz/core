<?php

namespace Grid\Core\Model\Package;

use ArrayIterator;

/**
 * EnabledList
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class EnabledList extends ArrayIterator
{

    /**
     * @const string
     */
    const DELIMITER = '/';

    /**
     * Generate regexp from pattern
     *
     * @param   string  $pattern
     * @return  string
     */
    protected static function generateRegexp( $pattern )
    {
        return str_replace(
            '\\*',
            '.*',
            preg_quote( $pattern, static::DELIMITER )
        );
    }

    /**
     * Return current list entry
     *
     * @link    http://php.net/manual/en/arrayiterator.current.php
     * @return  mixed
     */
    public function current()
    {
        $entry = parent::current();

        if ( $entry )
        {
            $entry = static::generateRegexp( $entry );
        }

        return $entry;
    }

    /**
     * Get all-matching pattern
     *
     * @return string
     */
    public function getAllPattern()
    {
        return static::DELIMITER
             . '^'
             . implode( '|', array_filter( iterator_to_array( $this ) ) )
             . '$'
             . static::DELIMITER;
    }

    /**
     * Is package (by name) enabled in listings?
     *
     * @param   string $packageName
     * @return  bool
     */
    public function isEnabled( $packageName )
    {
        return (bool) preg_match( $this->getAllPattern(), $packageName );
    }

}
