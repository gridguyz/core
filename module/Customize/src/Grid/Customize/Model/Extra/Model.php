<?php

namespace Grid\Customize\Model\Extra;

use Zork\Model\MapperAwareTrait;
use Zork\Model\MapperAwareInterface;

/**
 * Extra css model
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Model implements MapperAwareInterface
{

    use MapperAwareTrait;

    /**
     * Construct model
     *
     * @param \Customize\Model\Rule\Mapper $customizeExtraMapper
     */
    public function __construct( Mapper $customizeExtraMapper )
    {
        $this->setMapper( $customizeExtraMapper );
    }

    /**
     * Find (the only) structure
     *
     * @return  Structure
     */
    public function find()
    {
        return $this->getMapper()
                    ->find();
    }

    /**
     * Save (the only) structure
     *
     * @param   Structure|array|string  $structureOrCss
     * @return  int
     */
    public function save( $structureOrCss )
    {
        return $this->getMapper()
                    ->save( $structureOrCss );
    }

}
