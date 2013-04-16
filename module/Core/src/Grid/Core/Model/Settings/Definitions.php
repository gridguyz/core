<?php

namespace Grid\Core\Model\Settings;

use Traversable;
use Zend\Stdlib\ArrayUtils;

/**
 * Definitions
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Definitions
{

    /**
     * @var array
     */
    protected $definitions;

    /**
     * @param array|\Traversable $definitions
     */
    public function __construct( $definitions )
    {
        if ( $definitions instanceof Traversable )
        {
            $definitions = ArrayUtils::iteratorToArray( $definitions );
        }

        $this->definitions = (array) $definitions;
    }

    /**
     * Get text-domain
     *
     * @return string
     */
    public function getTextDomain( $section, $default = 'default' )
    {
        return isset( $this->definitions[$section]['textDomain'] )
                    ? $this->definitions[$section]['textDomain']
                    : $default;
    }

    /**
     * @param string $section
     * @return array|null
     */
    public function getElements( $section )
    {
        $section = (string) $section;

        if ( empty( $this->definitions[$section]['elements'] ) )
        {
            return null;
        }

        return (array) $this->definitions[$section]['elements'];
    }

    /**
     * @param string $section
     * @return array|null
     */
    public function getFieldsets( $section )
    {
        $section = (string) $section;

        if ( empty( $this->definitions[$section]['fieldsets'] ) )
        {
            return null;
        }

        return (array) $this->definitions[$section]['fieldsets'];
    }

    /**
     * @param string $section
     * @return array
     */
    public function getKeys( $section )
    {
        $section = (string) $section;

        if ( empty( $this->definitions[$section]['elements'] ) )
        {
            return array();
        }

        $keys = array();

        foreach ( $this->definitions[$section]['elements'] as $spec )
        {
            if ( ! empty( $spec['key'] ) )
            {
                $keys[] = (string) $spec['key'];
            }
        }

        return $keys;
    }

    /**
     * @param string $section
     * @return array
     */
    public function getNames( $section )
    {
        $section = (string) $section;

        if ( empty( $this->definitions[$section]['elements'] ) )
        {
            return array();
        }

        return array_keys( $this->definitions[$section]['elements'] );
    }

    /**
     * @param string $section
     * @param string $type
     * @return array
     */
    public function getKeyNames( $section )
    {
        $section = (string) $section;

        if ( empty( $this->definitions[$section]['elements'] ) )
        {
            return array();
        }

        $result = array();

        foreach ( $this->definitions[$section]['elements'] as $name => $spec )
        {
            if ( ! empty( $spec['key'] ) )
            {
                $result[ (string) $spec['key'] ] = $name;
            }
        }

        return $result;
    }

    /**
     * @param string $section
     * @return array
     */
    public function getNameKeys( $section )
    {
        $section = (string) $section;

        if ( empty( $this->definitions[$section]['elements'] ) )
        {
            return array();
        }

        $result = array();

        foreach ( $this->definitions[$section]['elements'] as $name => $spec )
        {
            if ( ! empty( $spec['key'] ) )
            {
                $result[$name] = (string) $spec['key'];
            }
        }

        return $result;
    }

}
