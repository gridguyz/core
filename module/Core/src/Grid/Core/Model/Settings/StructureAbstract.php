<?php

namespace Grid\Core\Model\Settings;

use Traversable;
use Zend\Stdlib\ArrayUtils;
use Zork\Factory\AdapterInterface;
use Zork\Model\Structure\MapperAwareAbstract;

/**
 * Structure
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
abstract class StructureAbstract extends MapperAwareAbstract
                              implements AdapterInterface
{

    /**
     * @const string
     * @abstract
     */
    const ACCEPTS_SECTION = '';

    /**
     * Field: section
     *
     * @var int
     */
    protected $section;

    /**
     * Field: settings
     *
     * @var string
     */
    protected $settings = array();

    /**
     * Constructor
     *
     * @param array $data
     */
    public function __construct( $data = array() )
    {
        if ( ! empty( $data['settings'] ) )
        {
            foreach ( (array) $data['settings'] as $name => $value )
            {
                $this->settings[$name] = $value;
            }

            unset( $data['settings'] );
        }

        parent::__construct( $data );
    }

    /**
     * Set settings
     *
     * @param array|\Traversable $settings
     * @return \Core\Model\Settings\Structure
     */
    public function setSettings( $settings )
    {
        if ( ! empty( $settings ) )
        {
            foreach ( $settings as $name => $value )
            {
                $this->setSetting( $name, $value );
            }
        }

        return $this;
    }

    /**
     * Get a setting by name
     *
     * @param string $name
     * @param string|null $default
     * @return string|null
     */
    public function getSetting( $name, $default = null )
    {
        if ( ! isset( $this->settings[$name] ) )
        {
            return $default;
        }

        return $this->settings[$name];
    }

    /**
     * Set a setting by name
     *
     * @param string $name
     * @param string|\StructureAbstract $value
     * @return \Core\Model\Settings\Structure
     */
    public function setSetting( $name, $value )
    {
        $old = isset( $this->settings[$name] )
            ? $this->settings[$name]
            : null;

        if ( $old instanceof self || $value instanceof self )
        {
            if ( $old instanceof self && $value instanceof self )
            {
                $old->setSettings( $value->settings );
            }
            else if ( $old instanceof self )
            {
                if ( $value instanceof Traversable )
                {
                    $value = ArrayUtils::iteratorToArray( $value );
                }

                $old->setSettings( (array) $value );
            }
            else
            {
                $this->settings[$name] = $value;
            }
        }
        else
        {
            $method = array( $this, 'update' . ucfirst( $name ) );

            if ( is_callable( $method ) &&
                 ( ( isset( $value )    && isset( $this->settings[$name] )
                                        && $value != $this->settings[$name] ) ||
                   ( isset( $value )    && ! isset( $this->settings[$name] ) ) ||
                   ( ! isset( $value )  && isset( $this->settings[$name] ) ) ) )
            {
                $value = $method( $value, $old );
            }

            if ( null === $value )
            {
                unset( $this->settings[$name] );
            }
            else
            {
                $this->settings[$name] = $value;
            }
        }

        return $this;
    }

    /**
     * Return true if and only if $options accepted by this adapter
     * If returns float as likelyhood the max of these will be used as adapter
     *
     * @param array $options;
     * @return float
     */
    public static function acceptsOptions( array $options )
    {
        return isset( $options['section'] ) &&
               $options['section'] == static::ACCEPTS_SECTION;
    }

    /**
     * Return a new instance of the adapter by $options
     *
     * @param array $options;
     * @return Zork\Factory\AdapterInterface
     */
    public static function factory( array $options = null )
    {
        return new static( $options );
    }

}
