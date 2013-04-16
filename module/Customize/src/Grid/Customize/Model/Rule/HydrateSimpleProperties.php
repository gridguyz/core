<?php

namespace Grid\Customize\Model\Rule;

use Zork\Stdlib\String;
use Zork\Model\MapperAwareTrait;
use Zork\Model\MapperAwareInterface;
use Zend\Stdlib\Hydrator\HydratorInterface;

/**
 * SimpleHydrateProperties
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class HydrateSimpleProperties implements HydratorInterface,
                                         MapperAwareInterface
{

    use MapperAwareTrait;

    /**
     * Construct
     *
     * @param \Customize\Model\Rule\Mapper $customizeRuleMapper
     */
    public function __construct( Mapper $customizeRuleMapper )
    {
        $this->setMapper( $customizeRuleMapper );
    }

    /**
     * Extract values from a structure
     *
     * @param  object $object
     * @return array
     */
    public function extract( $object )
    {
        $data = $this->getMapper()
                     ->extract( $object );

        if ( isset( $data['properties'] ) &&
             is_array( $data['properties'] ) )
        {
            foreach ( $data['properties'] as $key => $value )
            {
                $newKey = String::camelize( $key );

                if ( is_array( $value ) &&
                     array_key_exists( 'value', $value ) )
                {
                    $data['properties'][$key] = $value['value'];
                }

                if ( $newKey != $key )
                {
                    $data['properties'][$newKey] = $data['properties'][$key];
                    unset( $data['properties'][$key] );
                }
            }
        }

        return $data;
    }

    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array  $data
     * @param  object $object
     * @return object
     */
    public function hydrate( array $data, $object )
    {
        if ( isset( $data['properties'] ) &&
             is_array( $data['properties'] ) )
        {
            foreach ( $data['properties'] as $key => $value )
            {
                $newKey = String::decamelize( $key );

                if ( $newKey != $key )
                {
                    $data['properties'][$newKey] = $value;
                    unset( $data['properties'][$key] );
                }
            }
        }

        return $this->getMapper()
                    ->hydrate( $data, $object );
    }

}
