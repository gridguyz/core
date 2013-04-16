<?php

namespace Grid\Paragraph\Model\Snippet;

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
     * @param \Paragraph\Model\Snippet\Mapper $snippetMapper
     */
    public function __construct( Mapper $snippetMapper )
    {
        $this->setMapper( $snippetMapper );
    }

    /**
     * Find a structure is available
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
                            $name .
                            ( empty( $fields->type ) ? '' : '.' . $fields->type )
                        );
    }

}
