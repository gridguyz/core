<?php

namespace Grid\Core\Model\Module;

use Zork\Model\MapperAwareTrait;
use Zork\Model\MapperAwareInterface;

/**
 * Model
 *
 * @author Sipi
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
     * Find module elements by configurations
     *
     * @return \Core\Model\Module\Structure
     *      *
     */
    public function find( $filter = null )
    {
        return $this->getMapper()
                    ->find( $filter );
    }

}
