<?php

namespace Grid\Paragraph\Model\Paragraph\Structure;

/**
 * Grid\Paragraph\Model\Paragraph\Structure\AbstractLeaf
 * 
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
abstract class AbstractLeaf extends ProxyAbstract
{
    
    /**
     * This paragraph can be only parent of nothing
     * 
     * @var string
     */
    protected static $onlyParentOf = null;
    
}
