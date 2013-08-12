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
    const DELIMITER = '#';

    /**
     * Return current list entry
     *
     * @link    http://php.net/manual/en/arrayiterator.current.php
     * @return  mixed
     */
    public function current()
    {
        return (array) parent::current();
    }

    /**
     * Get all/key-matching pattern
     *
     * @param   string  $key
     * @return  string
     */
    public function getPattern( $key = null )
    {
        $patterns = array();

        if ( empty( $key ) )
        {
            foreach ( $this as $subPatterns )
            {
                $patterns = array_merge(
                    $patterns,
                    array_values( $subPatterns )
                );
            }
        }
        else
        {
            $patterns = $this[$key];
        }

        return static::DELIMITER
             . '^'
             . implode( '|', array_filter( $patterns ) )
             . '$'
             . static::DELIMITER;
    }

    /**
     * Is package (by name) enabled in listings?
     *
     * @param   string  $packageName
     * @param   string  $key
     * @return  bool
     */
    public function isEnabled( $packageName, $key = null )
    {
        return (bool) preg_match( $this->getPattern( $key ), $packageName );
    }

}
