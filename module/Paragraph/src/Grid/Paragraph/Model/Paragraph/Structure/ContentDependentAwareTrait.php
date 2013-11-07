<?php

namespace Grid\Paragraph\Model\Paragraph\Structure;

/**
 * ContentDependentAwareTrait
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
trait ContentDependentAwareTrait
{

    /**
     * Content (like) paragraph, which depends on this paragraph
     *
     * @var AbstractRoot
     */
    protected $_dependentContent;

    /**
     * Get content (like) paragraph, which depends on this paragraph
     *
     * @return  AbstractRoot
     */
    public function getDependentContent()
    {
        if ( null === $this->_dependentContent )
        {
            $root = $this->getRootParagraph();

            if ( $root instanceof Content || ( $root instanceof MetaContent &&
                 $this instanceof MetaContentDependentAwareInterface ) )
            {
                $this->_dependentContent = $root;
            }
        }

        return $this->_dependentContent;
    }

    /**
     * Set content (like) paragraph, which depends on this paragraph
     *
     * @param   AbstractRoot    $content
     * @return  ContentDependentAwareInterface
     */
    public function setDependentContent( AbstractRoot $content = null )
    {
        $this->_dependentContent = $content;
        return $this;
    }

    /**
     * Get dependent structures
     *
     * @return \Zork\Model\Structure\MapperAwareAbstract[]
     */
    public function getDependentStructures()
    {
        $dependents         = parent::getDependentStructures();
        $dependentContent   = $this->getDependentContent();

        if ( $dependentContent )
        {
            $dependents[] = $dependentContent;
        }

        return $dependents;
    }

}
