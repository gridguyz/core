<?php

namespace Grid\Paragraph\Model\Paragraph\Structure;

/**
 * LayoutAwareInterface
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
interface LayoutAwareInterface
{

    /**
     * Get layout
     *
     * @return \Paragraph\Model\Paragraph\Structure\Layout
     */
    public function getLayout();

    /**
     * Get layout-ID
     *
     * @return int
     */
    public function getLayoutId();

}
