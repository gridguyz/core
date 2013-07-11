<?php

namespace Grid\Paragraph\Model\Paragraph;

/**
 * Grid\Paragraph\Model\Paragraph\StructureInterface
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
interface StructureInterface
{

    /**
     * Get ID of the paragraph
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get type of the paragraph
     *
     * @return string|null
     */
    public function getType();

    /**
     * Get name of the paragraph
     *
     * @return string|null
     */
    public function getName();

    /**
     * Get root-ID of the paragraph
     *
     * @return int|null
     */
    public function getRootId();

}
