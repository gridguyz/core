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
     * @var bool
     */
    protected $modify = true;

    /**
     * Constructor
     *
     * @param   array   $packages
     */
    public function __construct( array $packages = array(), $modify = null )
    {
        parent::__construct( $packages );

        if ( null !== $modify )
        {
            $this->modify = (bool) $modify;
        }
    }

    /**
     * Can modify packages?
     *
     * @return  bool
     */
    public function canModify()
    {
        return $this->modify;
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
     * Get all/key-matching packages
     *
     * @param   null|string|array   $key
     * @return  string
     */
    public function getPackages( $keys = null )
    {
        $packages = array();

        if ( empty( $keys ) )
        {
            foreach ( $this as $subPackages )
            {
                $packages = array_merge(
                    $packages,
                    array_values( $subPackages )
                );
            }
        }
        else if ( is_array( $keys ) )
        {
            foreach ( $keys as $key )
            {
                $key = (string) $key;

                if ( ! empty( $this[$key] ) )
                {
                    $packages = array_merge(
                        $packages,
                        array_values( $this[$key] )
                    );
                }
            }
        }
        else
        {
            $packages = $this[(string) $keys];
        }

        return array_unique( array_map( 'strtolower', $packages ) );
    }

    /**
     * Is package (by name) enabled in listings?
     *
     * @param   string              $packageName
     * @param   null|string|array   $keys
     * @return  bool
     */
    public function isEnabled( $packageName, $keys = null )
    {
        return in_array(
            strtolower( $packageName ),
            $this->getPackages( $keys )
        );
    }

    /**
     * Get all packages
     *
     * @return  array
     */
    public function getKeys()
    {
        return array_keys( $this->getArrayCopy() );
    }

    /**
     * Get package count
     *
     * @return  int
     */
    public function getPackageCount()
    {
        $count = 0;

        foreach ( $this as $packages )
        {
            $count += count( $packages );
        }

        return $count;
    }

}
