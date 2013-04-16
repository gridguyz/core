<?php

namespace Grid\Paragraph\Model\Paragraph\Structure;

use Zork\Model\Structure\MapperAwareAbstract;
use Grid\Paragraph\Model\Paragraph\StructureInterface;

/**
 * Structure
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class ProxyBase extends MapperAwareAbstract
             implements StructureInterface
{

    /**
     * ID of the paragraph
     *
     * @val int|null
     */
    protected $id;

    /**
     * Type of the paragraph
     *
     * @var string|null
     */
    public $type;

    /**
     * Name of the paragraph
     *
     * @var string|null
     */
    public $name;

    /**
     * Root-ID of the paragraph
     *
     * @var int|null
     */
    public $rootId;

    /**
     * Tagging
     *
     * @var array
     */
    public $tags;

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
     * Get ID of the paragraph
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get type of the paragraph
     *
     * @return string|null
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get name of the paragraph
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get root-ID of the paragraph
     *
     * @return int|null
     */
    public function getRootId()
    {
        return $this->rootId;
    }

    /**
     * Get tags of the paragraph
     *
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
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
