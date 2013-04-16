<?php

namespace Grid\Paragraph\Model\Snippet;

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
     * @param \Paragraph\Model\Snippet\Mapper $snippetMapper
     */
    public function __construct( Mapper $snippetMapper )
    {
        $this->setMapper( $snippetMapper );
    }

    /**
     * Create a structure
     *
     * @param   array $data
     * @return  \Paragraph\Model\Snippet\Structure
     */
    public function create( $data = array() )
    {
        return $this->getMapper()
                    ->create( $data );
    }

    /**
     * Find a structure
     *
     * @param   string $name
     * @return  \Paragraph\Model\Snippet\Structure
     */
    public function find( $name )
    {
        return $this->getMapper()
                    ->find( $name );
    }

    /**
     * Find options for listing
     *
     * @return array
     */
    public function findOptions()
    {
        return $this->getMapper()
                    ->findOptions();
    }

    /**
     * Find a structure is available
     *
     * @param   string $name
     * @return  bool
     */
    public function isNameAvailable( $name )
    {
        return ! $this->getMapper()
                      ->isNameExists( $name );
    }

    /**
     * Get paginator for listing
     *
     * @return  \Zend\Paginator\Paginator
     */
    public function getPaginator()
    {
        return $this->getMapper()
                    ->getPaginator();
    }

}
