<?php

namespace Grid\Customize\Model\Sheet;

use DateTime;
use Zend\Db\Sql;
use Zork\Model\MapperAwareTrait;
use Zork\Model\MapperAwareInterface;
use Grid\Customize\Model\Rule\Mapper as RuleMapper;

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
    public function __construct( RuleMapper $customizeRuleMapper )
    {
        $this->setMapper( $customizeRuleMapper );
    }

    /**
     * Get the complete structure
     *
     * @return \Customize\Model\Sheet\Structure
     */
    public function findComplete()
    {
        return new Structure( array(
            'mapper'    => $this->getMapper(),
            'rules'     => $this->getMapper()
                                ->findAll( array(), array(
                                    new Sql\Expression( 'CHAR_LENGTH( media ) ASC' ),
                                    new Sql\Expression( 'media DESC' ),
                                    new Sql\Expression( 'CHAR_LENGTH( selector ) ASC' ),
                                    new Sql\Expression( 'selector ASC' ),
                                ) )
        ) );
    }

    /**
     * Get sub-structure by root-id
     *
     * @param int|null $rootId
     * @return \Customize\Model\Sheet\Structure
     */
    public function findByRoot( $rootId = null )
    {
        $mapper = $this->getMapper();
        $extra  = $mapper->findExtraByRoot( $rootId );
        $head   = $extra ? $extra->updated->format( DateTime::ISO8601 ) : null;

        return new Structure( array(
            'mapper'    => $mapper,
            'comment'   => $head,
            'extra'     => $extra ? $extra->extra : null,
            'rules'     => $mapper->findAllByRoot( $rootId, array(
                new Sql\Expression( 'CHAR_LENGTH( media ) ASC' ),
                new Sql\Expression( 'media DESC' ),
                new Sql\Expression( 'CHAR_LENGTH( selector ) ASC' ),
                new Sql\Expression( 'selector ASC' ),
            ) )
        ) );
    }

    /**
     * Delete rules by root-id
     *
     * @param int|null $rootId
     * @return int
     */
    public function deleteByRoot( $rootId = null )
    {
        return $this->getMapper()
                    ->deleteByRoot( $rootId );
    }

}
