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
     * Constructor
     *
     * @param   array   $packages
     * @param   array   $order
     */
    public function __construct( array $packages    = array(),
                                 array $order       = array() )
    {
        if ( ! empty( $order ) )
        {
            $packagesOrdered = array();
            asort( $order );

            foreach ( $order as $key => $_ )
            {
                if ( isset( $packages[$key] ) )
                {
                    $packagesOrdered[$key] = $packages[$key];
                }
            }

            foreach ( $packages as $key => $data )
            {
                if ( ! isset( $packagesOrdered[$key] ) )
                {
                    $packagesOrdered[$key] = $data;
                }
            }

            $packages = $packagesOrdered;
        }

        parent::__construct( $packages );
    }

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

    /**
     * Get all keys
     *
     * @return  array
     */
    public function getKeys()
    {
        return array_keys( $this->getArrayCopy() );
    }

    /**
     * Get pattern count
     *
     * @return  int
     */
    public function getPatternCount()
    {
        $count = 0;

        foreach ( $this as $patterns )
        {
            $count += count( $patterns );
        }

        return $count;
    }

}
