<?php

namespace Grid\Customize\Model;

use Zork\Rpc\CallableTrait;
use Zork\Rpc\CallableInterface;
use Grid\Paragraph\Model\Paragraph;

/**
 * Rpc
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Rpc implements CallableInterface
{

    use CallableTrait;

    /**
     * @var string
     */
    const SUCCESS = '';

    /**
     * @var string
     */
    const SELECTOR_TAKEN = 'customize.selector.taken';

    /**
     * @var string
     */
    const SELECTOR_PARAGRAPH_NOT_EXISTS = 'customize.selector.paragraphNotExists';

    /**
     * @var Rule\Mapper
     */
    protected $ruleMapper;

    /**
     * @var Paragraph\Mapper
     */
    protected $paragraphMapper;

    /**
     * Get rule mapper
     *
     * @return  Rule\Mapper
     */
    public function getRuleMapper()
    {
        return $this->ruleMapper;
    }

    /**
     * Get paragraph mapper
     *
     * @return  Paragraph\Mapper
     */
    public function getParagraphMapper()
    {
        return $this->paragraphMapper;
    }

    /**
     * Set rule-mapper
     *
     * @param   Rule\Mapper $customizeRuleMapper
     * @return  Rpc
     */
    public function setRuleMapper( Rule\Mapper $customizeRuleMapper )
    {
        $this->ruleMapper = $customizeRuleMapper;
        return $this;
    }

    /**
     * Set paragraph-mapper
     *
     * @param   Paragraph\Mapper    $paragraphMapper
     * @return  Rpc
     */
    public function setParagraphMapper( Paragraph\Mapper $paragraphMapper )
    {
        $this->paragraphMapper = $paragraphMapper;
        return $this;
    }

    /**
     * Construct rpc
     *
     * @param Rule\Mapper       $customizeRuleMapper
     * @param Paragraph\Mapper  $paragraphMapper
     */
    public function __construct( Rule\Mapper $customizeRuleMapper,
                                 Paragraph\Mapper $paragraphMapper )
    {
        $this->setRuleMapper( $customizeRuleMapper )
             ->setParagraphMapper( $paragraphMapper );
    }

    /**
     * Is selector available
     *
     * @param   string $email
     * @param   array|object|string $fields [optional]
     * @param   int|null $rootId [optional]
     * @return  string
     */
    public function isSelectorAvailable( $selector,
                                         $fields = array(),
                                         $rootId = null )
    {
        if ( is_scalar( $fields ) )
        {
            $media = (string) $fields;
            $id    = null;
        }
        else
        {
            $fields = (object) $fields;
            $media  = empty( $fields->media )           ? ''    : $fields->media;
            $rootId = empty( $fields->rootParagraphId ) ? null  : (int) $fields->rootParagraphId;
            $id     = empty( $fields->id )              ? null  : (int) $fields->id;
        }

        if ( $this->getRuleMapper()
                  ->isSelectorExists( $selector, $media, $rootId, $id ) )
        {
            return static::SELECTOR_TAKEN;
        }

        $matches = array();

        if ( preg_match( '/#paragraph-(\\d+)/', $selector, $matches ) )
        {
            if ( ! $this->getParagraphMapper()
                        ->isParagraphIdExists( $matches[1] ) )
            {
                return static::SELECTOR_PARAGRAPH_NOT_EXISTS;
            }
        }

        return static::SUCCESS;
    }

}
