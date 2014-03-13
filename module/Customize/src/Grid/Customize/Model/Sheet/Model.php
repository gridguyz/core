<?php

namespace Grid\Customize\Model\Sheet;

use Zork\Model\MapperAwareTrait;
use Zork\Model\MapperAwareInterface;
use Grid\Customize\Model\Extra\Structure as ExtraStructure;

/**
 * Customize-rule
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Model implements MapperAwareInterface
{

    use MapperAwareTrait;

    /**
     * Construct model
     *
     * @param   Mapper  $customizeSheetMapper
     */
    public function __construct( Mapper $customizeSheetMapper )
    {
        $this->setMapper( $customizeSheetMapper );
    }

    /**
     * Get the complete structure
     *
     * @deprecated
     * @return  Structure
     */
    public function findComplete()
    {
        return $this->getMapper()
                    ->findComplete();
    }

    /**
     * Get sub-structure by root-id
     *
     * @param   int|null    $rootId
     * @return  Structure
     */
    public function find( $rootId = null )
    {
        return $this->getMapper()
                    ->find( $rootId );
    }

    /**
     * Find a structure by its extra structure
     *
     * @param   ExtraStructure  $extra
     * @return  Structure
     */
    public function findByExtra( ExtraStructure $extra )
    {
        return $this->getMapper()
                    ->findByExtra( $extra );
    }

    /**
     * Get paginator for listing (roots only)
     *
     * @return  \Zend\Paginator\Paginator
     */
    public function getPaginator()
    {
        return $this->getMapper()
                    ->getPaginator();
    }

    /**
     * Save a structure
     *
     * @param   Structure $structure
     * @return  int
     */
    public function save( &$structure )
    {
        return $this->getMapper()
                    ->save( $structure );
    }

    /**
     * Delete rules by root-id
     *
     * @param   int|null    $rootId
     * @return  int
     */
    public function delete( $rootId = null )
    {
        return $this->getMapper()
                    ->delete( $rootId );
    }

    /**
     * Delete rules by root-id
     *
     * @param   int|null    $rootId
     * @return  Structure
     */
    public function createEmpty( $rootId = null )
    {
        return $this->getMapper()
                    ->create( array(
                        'rootId' => ( (int) $rootId ) ?: null,
                    ) );
    }

}
