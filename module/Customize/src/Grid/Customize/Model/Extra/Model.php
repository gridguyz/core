<?php

namespace Grid\Customize\Model\Extra;

use Zork\Model\MapperAwareTrait;
use Zork\Model\MapperAwareInterface;

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
     * @param \Customize\Model\Extra\Mapper $customizeExtraMapper
     */
    public function __construct( Mapper $customizeExtraMapper )
    {
        $this->setMapper( $customizeExtraMapper );
    }

    /**
     * Create a rule
     *
     * @param array|\Traversable $data
     * @return \Customize\Model\Extra\Structure
     */
    public function create( $data )
    {
        return $this->getMapper()
                    ->create( $data );
    }

    /**
     * Get customize extra by id
     *
     * @param int $id
     * @return \Customize\Model\Extra\Structure
     */
    public function find( $id )
    {
        return $this->getMapper()
                    ->find( $id );
    }

    /**
     * Get customize extra by selector & media
     *
     * @param int|null $rootParagraphId
     * @return \Customize\Model\Extra\Structure
     */
    public function findByRoot( $rootParagraphId )
    {
        $root  = ( (int) $rootParagraphId ) ?: null;
        $extra = $this->getMapper()
                      ->findByRoot( $root );

        if ( empty( $extra ) )
        {
            $extra = $this->getMapper()
                          ->create( array(
                              'rootParagraphId' => $root,
                          ) );
        }

        return $extra;
    }

    /**
     * Find updated times
     *
     * @param   array|int   $rootParagraphIds
     * @param   bool|null   $global
     * @return  \Zork\Stdlib\DateTime[]
     */
    public function findUpdated( $rootParagraphIds, $global = null )
    {
        return $this->getMapper()
                    ->findUpdated( $rootParagraphIds, $global );
    }

    /**
     * Save customize extra
     *
     * @param \Customize\Model\Extra\Structure $extra
     * @return int
     */
    public function save( Structure $extra )
    {
        return $this->getMapper()
                    ->save( $extra );
    }

    /**
     * Delete customize extra
     *
     * @param \Customize\Model\Extra\Structure|int $extra object or id
     * @return int
     */
    public function delete( $extra )
    {
        return $this->getMapper()
                    ->delete( $extra );
    }

    /**
     * Get paginator for listing
     *
     * @return \Zend\Paginator\Paginator
     */
    public function getPaginator()
    {
        return $this->getMapper()
                    ->getPaginator();
    }

}
