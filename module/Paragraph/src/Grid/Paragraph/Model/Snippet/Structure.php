<?php

namespace Grid\Paragraph\Model\Snippet;

use Zork\Model\Structure\MapperAwareAbstract;

/**
 * \Paragraph\Model\Dashboard\Structure
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Structure extends MapperAwareAbstract
{

    /**
     * @var string
     */
    public $name = null;

    /**
     * @var string
     */
    public $code = '';

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return substr( strrchr( $this->name, '.' ), 1 );
    }

}
