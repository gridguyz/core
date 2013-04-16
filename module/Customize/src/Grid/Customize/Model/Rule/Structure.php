<?php

namespace Grid\Customize\Model\Rule;

use Zork\Stdlib\String;
use Zork\Model\Structure\MapperAwareAbstract;

/**
 * Rule structure
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Structure extends MapperAwareAbstract
{

    /**
     * @var null
     */
    const PRIORITY_NORMAL       = null;

    /**
     * @var string
     */
    const PRIORITY_IMPORTANT    = 'important';

    /**
     * @var string
     */
    const SELECTOR_ALL          = '*';

    /**
     * @var string
     */
    const MEDIA_DEFAULT         = '';

    /**
     * Property aliases
     *
     * @var array
     */
    protected static $propertyAliases = array(
        'borderRadius' => array(
            'WebkitBorderRadius',
            'MozBorderRadius',
            'MsBorderRadius',
        ),
        'borderTopLeftRadius' => array(
            'WebkitBorderTopLeftRadius',
            'MozBorderRadiusTopleft',
            'MsBorderTopLeftRadius',
        ),
        'borderTopRightRadius' => array(
            'WebkitBorderTopRightRadius',
            'MozBorderRadiusTopright',
            'MsBorderTopRightRadius',
        ),
        'borderBottomLeftRadius' => array(
            'WebkitBorderBottomLeftRadius',
            'MozBorderRadiusBottomleft',
            'MsBorderBottomLeftRadius',
        ),
        'borderBottomRightRadius' => array(
            'WebkitBorderBottomRightRadius',
            'MozBorderRadiusBottomright',
            'MsBorderBottomRightRadius',
        ),
    );

    /**
     * Rule id
     *
     * @var int
     */
    protected $id = null;

    /**
     * Rule media
     *
     * @var string
     */
    protected $media = self::MEDIA_DEFAULT;

    /**
     * Rule selector
     *
     * @var string
     */
    protected $selector = self::SELECTOR_ALL;

    /**
     * Paragraph id, to which bounded
     *
     * @var int
     */
    protected $paragraphId = null;

    /**
     * Properties data
     *
     * @var array
     */
    private $properties = array();

    /**
     * Set media
     *
     * @param string $media
     * @return \Customize\Model\Rule\Structure
     */
    public function setMedia( $media )
    {
        $this->media = trim( (string) $media );
        return $this;
    }

    /**
     * Set selector
     *
     * @param string $selector
     * @return \Customize\Model\Rule\Structure
     */
    public function setSelector( $selector )
    {
        $this->selector = empty( $selector )
            ? self::SELECTOR_ALL
            : (string) $selector;

        $matches = array();

        if ( empty( $this->paragraphId ) &&
             preg_match( '/#paragraph-(\d+)/', $selector, $matches ) )
        {
            $this->setParagraphId( $matches[1] );
        }

        return $this;
    }

    /**
     * Set paragraph id, to which bounded
     *
     * @param int $id
     * @return \Customize\Model\Rule\Structure
     */
    public function setParagraphId( $id )
    {
        $this->paragraphId = ( (int) $id ) ?: null;
        return $this;
    }

    /**
     * Has raw property
     *
     * @param string $rawName
     * @return bool
     */
    public function hasRawProperty( $rawName )
    {
        return ! empty( $this->properties[$rawName] );
    }

    /**
     * Get raw property
     *
     * @param string $rawName
     * @return object (name, value, priority)
     */
    public function getRawProperty( $rawName )
    {
        if ( ! empty( $this->properties[$rawName] ) )
        {
            return (object) $this->properties[$rawName];
        }

        return null;
    }

    /**
     * Set raw property
     *
     * @param string $rawName
     * @param mixed $value
     * @param string $priority
     * @return \Customize\Model\Rule\Structure
     */
    public function setRawProperty( $rawName, $value,
                                    $priority = self::PRIORITY_NORMAL )
    {
        if ( is_array( $value ) )
        {
            $value = (object) $value;
        }

        if ( is_object( $value ) )
        {
            if ( isset( $value->priority ) )
            {
                $priority = $value->priority;
            }

            if ( isset( $value->value ) )
            {
                $value = $value->value;
            }
            else
            {
                $value = null;
            }
        }

        if ( empty( $value ) )
        {
            return $this->removeRawProperty( $rawName );
        }

        if ( empty( $priority ) )
        {
            $priority = self::PRIORITY_NORMAL;
        }

        $this->properties[$rawName] = array(
            'name'      => $rawName,
            'value'     => $value,
            'priority'  => $priority,
        );

        return $this;
    }

    /**
     * Remove raw property
     *
     * @param string $rawName
     * @return \Customize\Model\Rule\Structure
     */
    public function removeRawProperty( $rawName )
    {
        unset( $this->properties[$rawName] );
        return $this;
    }

    /**
     * Remove all raw property
     *
     * @return \Customize\Model\Rule\Structure
     */
    public function clearRawProperties()
    {
        $this->properties = array();
        return $this;
    }

    /**
     * Get raw property value
     *
     * @param string $rawName
     * @return mixed
     */
    public function getRawPropertyValue( $rawName )
    {
        if ( ! empty( $this->properties[$rawName] ) )
        {
            return $this->properties[$rawName]['value'];
        }

        return null;
    }

    /**
     * Get raw property priority
     *
     * @param string $rawName
     * @return mixed
     */
    public function getRawPropertyPriority( $rawName )
    {
        if ( ! empty( $this->properties[$rawName] ) )
        {
            return $this->properties[$rawName]['priority'];
        }

        return null;
    }

    /**
     * Get raw property postix (based on the priority)
     *
     * @param string $rawName
     * @return string
     */
    public function getRawPropertyPostfix( $rawName )
    {
        if ( empty( $this->properties[$rawName]['priority'] ) )
        {
            return '';
        }

        return ' !' . $this->properties[$rawName]['priority'];
    }

    /**
     * Get raw property names
     *
     * @return array
     */
    public function getRawPropertyNames()
    {
        return array_keys( $this->properties );
    }

    /**
     * Get raw properties
     *
     * @return array
     */
    public function getRawProperties()
    {
        return $this->properties;
    }

    /**
     * Set raw properties
     *
     * @param array $properties
     * @return \Customize\Model\Rule\Structure
     */
    public function setRawProperties( $properties )
    {
        if ( ! empty( $properties ) )
        {
            foreach ( $properties as $name => $value )
            {
                switch ( true )
                {
                    case is_array( $value ) && array_key_exists( 'value', $value ):
                        $value = (object) $value;

                    case is_object( $value ):
                        if ( empty( $value->value ) )
                        {
                            $value->value = null;
                        }

                        if ( empty( $value->priority ) )
                        {
                            $value->priority = null;
                        }

                        if ( empty( $value->name ) )
                        {
                            $value->name = $name;
                        }

                        $this->setRawProperty(
                            $value->name,
                            $value->value,
                            $value->priority
                        );
                        break;

                    case is_array( $value ):
                        if ( count( $value ) > 2 )
                        {
                            list( $rawName, $value, $priority ) = $value;

                            if ( empty( $rawName ) )
                            {
                                $rawName = $name;
                            }
                        }
                        else if ( count( $value ) > 1 )
                        {
                            $rawName = $name;
                            list( $value, $priority ) = $value;
                        }
                        else
                        {
                            $rawName    = $name;
                            $priority   = null;
                            $value      = empty( $value ) ? null : current( $value );
                        }

                        $this->setRawProperty( $rawName, $value, $priority );
                        break;

                    default:
                        $this->setRawProperty( $name, $value );
                        break;
                }
            }
        }

        return $this;
    }

    /**
     * Has property
     *
     * @param string $name
     * @return bool
     */
    public function hasProperty( $name )
    {
        return $this->hasRawProperty( String::decamelize( $name ) );
    }

    /**
     * Get property
     *
     * @param string $name
     * @return object (value, priority)
     */
    public function getProperty( $name )
    {
        return $this->getRawProperty( String::decamelize( $name ) );
    }

    /**
     * Set background-position-x
     *
     * @param string $value
     * @param string $priority
     * @return \Customize\Model\Rule\Structure
     */
    protected function setBackgroundPositionXProperty( $value, $priority = self::PRIORITY_NORMAL )
    {
        $y = $this->getRawProperty( 'background-position-y' );

        if ( ! empty( $y ) && ! empty( $y->value ) )
        {
            $xyValue    = $value . ' ' . $y->value;
            $xyPriority = empty( $priority ) ? $y->priority : $priority;
        }
        else if ( empty( $value ) )
        {
            $xyValue    = null;
            $xyPriority = null;
        }
        else
        {
            $xyValue    = $value . ' 0%';
            $xyPriority = $priority;
        }

        return $this->setRawProperty( 'background-position', $xyValue, $xyPriority )
                    ->setRawProperty( 'background-position-x', $value, $priority );
    }

    /**
     * Set background-position-y
     *
     * @param string $value
     * @param string $priority
     * @return \Customize\Model\Rule\Structure
     */
    protected function setBackgroundPositionYProperty( $value, $priority = self::PRIORITY_NORMAL )
    {
        $x = $this->getRawProperty( 'background-position-x' );

        if ( ! empty( $x ) && ! empty( $x->value ) )
        {
            $xyValue    = $x->value . ' ' . $value;
            $xyPriority = empty( $priority ) ? $x->priority : $priority;
        }
        else if ( empty( $value ) )
        {
            $xyValue    = null;
            $xyPriority = null;
        }
        else
        {
            $xyValue    = '0% ' . $value;
            $xyPriority = $priority;
        }

        return $this->setRawProperty( 'background-position', $xyValue, $xyPriority )
                    ->setRawProperty( 'background-position-y', $value, $priority );
    }

    /**
     * Set background-position
     *
     * @param string $value
     * @param string $priority
     * @return \Customize\Model\Rule\Structure
     */
    protected function setBackgroundPositionProperty( $value, $priority = self::PRIORITY_NORMAL )
    {
        list( $x, $y ) = preg_split( '/\s+/', trim( $value ), 2 );

        return $this->setRawProperty( 'background-position', $value, $priority )
                    ->setRawProperty( 'background-position-x', $x, $priority )
                    ->setRawProperty( 'background-position-y', $y, $priority );
    }

    /**
     * Set property
     *
     * @param string $name
     * @param mixed $value
     * @param string $priority
     * @return \Customize\Model\Rule\Structure
     */
    public function setProperty( $name, $value,
                                 $priority = self::PRIORITY_NORMAL )
    {
        if ( ! empty( static::$propertyAliases[$name] ) )
        {
            foreach ( static::$propertyAliases[$name] as $alias )
            {
                $this->setRawProperty(
                    String::decamelize( $alias ),
                    $value, $priority
                );
            }
        }

        $method = 'set' . ucfirst( $name ) . 'Property';

        if ( is_callable( array( $this, $method ) ) )
        {
            return $this->$method( $value, $priority );
        }
        else
        {
            return $this->setRawProperty(
                String::decamelize( $name ),
                $value, $priority
            );
        }
    }

    /**
     * Remove property
     *
     * @param string $name
     * @return \Customize\Model\Rule\Structure
     */
    public function removeProperty( $name )
    {
        return $this->removeRawProperty( String::decamelize( $name ) );
    }

    /**
     * Remove all property
     *
     * @return \Customize\Model\Rule\Structure
     */
    public function clearProperties()
    {
        $this->properties = array();
        return $this;
    }

    /**
     * Get property value
     *
     * @param string $name
     * @return mixed
     */
    public function getPropertyValue( $name )
    {
        return $this->getRawPropertyValue( String::decamelize( $name ) );
    }

    /**
     * Get property priority
     *
     * @param string $name
     * @return mixed
     */
    public function getPropertyPriority( $name )
    {
        return $this->getRawPropertyPriority( String::decamelize( $name ) );
    }

    /**
     * Get property names
     *
     * @return array
     */
    public function getPropertyNames()
    {
        $result = array();

        foreach ( $this->getRawPropertyNames() as $rawName )
        {
            $result[] = String::camelize( $rawName );
        }

        return $result;
    }

    /**
     * Get properties
     *
     * @return array
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * Set properties
     *
     * @param array $properties
     * @return \Customize\Model\Rule\Structure
     */
    public function setProperties( $properties )
    {
        if ( ! empty( $properties ) )
        {
            foreach ( $properties as $name => $value )
            {
                switch ( true )
                {
                    case is_array( $value ) &&
                         array_key_exists( 'value', $value ):
                        $value = (object) $value;

                    case is_object( $value ):
                        if ( empty( $value->value ) )
                        {
                            $value->value = null;
                        }

                        if ( empty( $value->priority ) )
                        {
                            $value->priority = null;
                        }

                        if ( empty( $value->name ) )
                        {
                            $value->name = $name;
                        }

                        $this->setProperty(
                            $value->name,
                            $value->value,
                            $value->priority
                        );
                        break;

                    case is_array( $value ):
                        if ( count( $value ) > 2 )
                        {
                            list( $tmpName, $value, $priority ) = $value;
                            $name = $tmpName ?: $name;
                        }
                        else if ( count( $value ) > 1 )
                        {
                            list( $value, $priority ) = $value;
                        }
                        else
                        {
                            $priority   = null;
                            $value      = empty( $value ) ? null : current( $value );
                        }

                        $this->setProperty( $name, $value, $priority );
                        break;

                    default:
                        $this->setProperty( $name, $value );
                        break;
                }
            }
        }

        return $this;
    }

}