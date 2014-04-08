<?php

namespace Grid\Paragraph\Model\Paragraph\Structure;

/**
 * ReferencedParagraphInterface
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
interface ReferencedParagraphInterface
{

    /**
     * Get referenced render list
     *
     * @return  array|null
     */
    public function getReferencedRenderList();

}
