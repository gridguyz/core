<?php

namespace Grid\Paragraph\Model\Paragraph\Structure;

/**
 * ContentDependentAwareInterface
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
interface ContentDependentAwareInterface
{

    /**
     * Get content (like) paragraph, which depends on this paragraph
     *
     * @return  AbstractRoot
     */
    public function getDependentContent();

    /**
     * Set content (like) paragraph, which depends on this paragraph
     *
     * @param   AbstractRoot    $content
     * @return  ContentDependentAwareInterface
     */
    public function setDependentContent( AbstractRoot $content = null );

}
