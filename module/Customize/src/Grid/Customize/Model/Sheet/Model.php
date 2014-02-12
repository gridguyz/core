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
     * @return array
     */
    protected function getRuleOrder()
    {
        return array(
            new Sql\Expression(
                'CHAR_LENGTH( ? ) ASC',
                array( 'media' ),
                array( Sql\Expression::TYPE_IDENTIFIER )
            ),
            new Sql\Expression(
                '? DESC',
                array( 'media' ),
                array( Sql\Expression::TYPE_IDENTIFIER )
            ),
            new Sql\Expression(
                'CHAR_LENGTH( ? ) ASC',
                array( 'selector' ),
                array( Sql\Expression::TYPE_IDENTIFIER )
            ),
            new Sql\Expression(
                '? ASC',
                array( 'selector' ),
                array( Sql\Expression::TYPE_IDENTIFIER )
            ),
        );
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
                                ->findAll( array(), $this->getRuleOrder() )
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
        $mapper     = $this->getMapper();
        $extra      = $mapper->findExtraByRoot( $rootId );
        $comment    = $extra
                    ? $extra->updated->format( DateTime::ISO8601 )
                    : null;

        return new Structure( array(
            'mapper'    => $mapper,
            'comment'   => $comment,
            'extra'     => $extra ? $extra->extra : null,
            'rules'     => $mapper->findAllByRoot(
                $rootId,
                $this->getRuleOrder()
            )
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
