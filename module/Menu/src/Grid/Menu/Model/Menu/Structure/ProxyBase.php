<?php

namespace Grid\Menu\Model\Menu\Structure;

use Zork\Model\Structure\MapperAwareAbstract;
use Grid\Menu\Model\Menu\StructureInterface;

/**
 * Structure
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class ProxyBase extends MapperAwareAbstract
             implements StructureInterface
{

    /**
     * ID of the menu
     *
     * @val int|null
     */
    protected $id;

    /**
     * Type of the menu
     *
     * @var string|null
     */
    public $type;

    /**
     * Label of the menu
     *
     * @var string|null
     */
    public $label;

    /**
     * Left for ordering / hierarchy
     *
     * @var int|null
     */
    public $left;

    /**
     * Right for ordering / hierarchy
     *
     * @var int|null
     */
    public $right;

    /**
     * Target of the menu
     *
     * @var string|null
     */
    public $target;

    /**
     * Get ID of the menu
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get type of the menu
     *
     * @return string|null
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get label of the menu
     *
     * @return string|null
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Get target of the menu
     *
     * @return string|null
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Get service locator
     *
     * @return \Zend\ServiceManager\ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->getMapper()
                    ->getServiceLocator();
    }

}
