<?php

namespace Grid\Core\Model\Module;

use Zork\Model\Structure\MapperAwareAbstract;

/**
 * Structure
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Structure extends MapperAwareAbstract
{

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $module;

    /**
     * @var bool
     */
    protected $enabled;

    /**
     * Set module name
     *
     * @param   string  $name
     * @return  \Grid\Core\Model\Module\Structure
     */
    public function setModule( $name )
    {
        $this->module = (string) $name;
        return $this;
    }

    /**
     * Set enabed flag
     *
     * @param   bool    $flag
     * @return  \Grid\Core\Model\Module\Structure
     */
    public function setEnabled( $flag = true )
    {
        $this->enabled = (bool) $flag;
        return $this;
    }

}
