<?php

namespace Grid\Core\Model\Module;

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
     * @param \Core\Model\Module\Mapper $moduleMapper
     */
    public function __construct( Mapper $moduleMapper )
    {
        $this->setMapper( $moduleMapper );
    }

    /**
     * Create a module
     *
     * @param   array   $data
     * @return  \Grid\Core\Model\SubDomain\Structure
     */
    public function create( $data )
    {
        return $this->getMapper()
                    ->create( $data );
    }

    /**
     * Find a module by id
     *
     * @param   int     $id
     * @return  \Grid\Core\Model\SubDomain\Structure
     */
    public function find( $id )
    {
        return $this->getMapper()
                    ->find( $id );
    }

    /**
     * Find a module by name
     *
     * @param   string  $name
     * @return  \Grid\Core\Model\SubDomain\Structure
     */
    public function findByName( $name )
    {
        return $this->getMapper()
                    ->findByName( $name );
    }

}
