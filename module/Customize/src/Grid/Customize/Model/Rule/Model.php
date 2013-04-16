<?php

namespace Grid\Customize\Model\Rule;

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
     * @param \Customize\Model\Rule\Mapper $customizeRuleMapper
     */
    public function __construct( Mapper $customizeRuleMapper )
    {
        $this->setMapper( $customizeRuleMapper );
    }

    /**
     * Create a rule
     *
     * @param array|\Traversable $data
     * @return \Customize\Model\Rule\Structure
     */
    public function create( $data )
    {
        return $this->getMapper()
                    ->create( $data );
    }

    /**
     * Get rule by id
     *
     * @param int $id
     * @return \Customize\Model\Rule\Structure
     */
    public function find( $id )
    {
        return $this->getMapper()
                    ->find( $id );
    }

    /**
     * Get rule by selector & media
     *
     * @param string $selector
     * @param string $media [optional]
     * @return \Customize\Model\Rule\Structure
     */
    public function findBySelector( $selector, $media = '' )
    {
        $rule = $this->getMapper()
                     ->findBySelector( $selector, $media );

        if ( empty( $rule ) )
        {
            $rule = $this->getMapper()
                         ->create( array(
                             'media'    => $media,
                             'selector' => $selector,
                         ) );
        }

        return $rule;
    }

    /**
     * Save a rule
     *
     * @param \Customize\Model\Rule\Structure $rule
     * @param array $update
     * @return int
     */
    public function save( Structure $rule )
    {
        return $this->getMapper()
                    ->save( $rule );
    }

    /**
     * Delete a rule
     *
     * @param \Customize\Model\Rule\Structure|int $rule object or id
     * @return int
     */
    public function delete( $rule )
    {
        return $this->getMapper()
                    ->delete( $rule );
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
