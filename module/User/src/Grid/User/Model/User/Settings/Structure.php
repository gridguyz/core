<?php

namespace Grid\User\Model\User\Settings;

use Zend\Stdlib\ArrayUtils;
use Zork\Model\Structure\MapperAwareAbstract;

/**
 * Structure
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Structure extends MapperAwareAbstract
{

    /**
     * Field: userId
     *
     * @var int
     */
    protected $userId;

    /**
     * Field: section
     *
     * @var string
     */
    protected $section  = '';

    /**
     * Settings
     *
     * @var array
     */
    protected $settings = array();

    /**
     * Set settings
     *
     * @param array|\Traversable $settings
     * @return \User\Model\User\Settings\Structure
     */
    public function setSettings( $settings )
    {
        $this->settings = ArrayUtils::iteratorToArray( $settings );
        return $this;
    }

    /**
     * Add (merge with existing) settings
     *
     * @param array|\Traversable $settings
     * @return \User\Model\User\Settings\Structure
     */
    public function addSettings( $settings )
    {
        $this->settings = ArrayUtils::merge(
            $this->settings,
            ArrayUtils::iteratorToArray( $settings )
        );

        return $this;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasSetting( $name )
    {
        return isset( $this->settings[$name] );
    }

    /**
     * @param string $name
     * @param mixed $default
     * @return mixed|null
     */
    public function getSetting( $name, $default = null )
    {
        return isset( $this->settings[$name] )
             ? $this->settings[$name]
             : $default;
    }

    /**
     * @param string $name
     * @param mixed|null $value
     * @return \User\Model\User\Settings\Structure
     */
    public function setSetting( $name, $value )
    {
        if ( null === $value )
        {
            unset( $this->settings[$name] );
        }
        else
        {
            $this->settings[$name] = $value;
        }

        return $this;
    }

}
