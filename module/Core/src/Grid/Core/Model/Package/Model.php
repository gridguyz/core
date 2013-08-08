<?php

namespace Grid\Core\Model\Package;

use Zork\Model\MapperAwareTrait;
use Zork\Model\MapperAwareInterface;

/**
 * Model
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Model implements MapperAwareInterface
{

    use MapperAwareTrait;

    /**
     * Construct model
     *
     * @param   \Grid\Core\Model\Package\Mapper $packageMapper
     */
    public function __construct( Mapper $packageMapper )
    {
        $this->setMapper( $packageMapper );
    }

    /**
     * Find package element by name
     *
     * @param   string  $name
     * @return  \Grid\Core\Model\Package\Structure
     */
    public function find( $name )
    {
        return $this->getMapper()
                    ->find( $name );
    }

    /**
     * Find package element by name
     *
     * @param   string|null $where
     * @param   bool|null   $order
     * @return  \Grid\Core\Model\Package\Structure
     */
    public function getPaginator( $where = null, $order = null )
    {
        return $this->getMapper()
                    ->getPaginator( $where, $order );
    }

}
