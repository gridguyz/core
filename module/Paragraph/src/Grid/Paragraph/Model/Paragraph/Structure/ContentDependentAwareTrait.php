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
    private $dependentContent;

    /**
     * Get content (like) paragraph, which depends on this paragraph
     *
     * @return  AbstractRoot
     */
    public function getDependentContent()
    {
        if ( null === $this->dependentContent )
        {
            $root = $this->getRootParagraph();

            if ( $root instanceof Content || ( $root instanceof MetaContent &&
                 $this instanceof MetaContentDependentAwareInterface ) )
            {
                $this->dependentContent = $root;
            }
        }

        return $this->dependentContent;
    }

    /**
     * Set content (like) paragraph, which depends on this paragraph
     *
     * @param   AbstractRoot    $content
     * @return  ContentDependentAwareInterface
     */
    public function setDependentContent( AbstractRoot $content )
    {
        $this->dependentContent = $content;
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
