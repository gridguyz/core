<?php

namespace Grid\Tag\Model\Tag;

use Zork\Rpc\CallableTrait;
use Zork\Rpc\CallableInterface;
use Zork\Model\MapperAwareTrait;
use Zork\Model\MapperAwareInterface;

/**
 * Rpc
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Rpc implements CallableInterface,
                     MapperAwareInterface
{

    use CallableTrait,
        MapperAwareTrait;

    /**
     * Construct rpc
     *
     * @param \Tag\Model\Tag\Mapper $userMapper
     */
    public function __construct( Mapper $userMapper )
    {
        $this->setMapper( $userMapper );
    }

    /**
     * Is tag-name available?
     *
     * @param   string          $name
     * @param   array|object    $fields [optional]
     * @return  bool
     */
    public function isNameAvailable( $name, $fields = array() )
    {
        $fields = (object) $fields;

        return ! $this->getMapper()
                      ->isNameExists(
                            $name,
                            empty( $fields->id ) ? null : $fields->id
                        );
    }

}
