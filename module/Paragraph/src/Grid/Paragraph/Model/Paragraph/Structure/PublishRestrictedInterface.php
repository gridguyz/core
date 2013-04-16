<?php

namespace Grid\Paragraph\Model\Paragraph\Structure;

/**
 * PublishRestrictedInterface
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
interface PublishRestrictedInterface
{

    /**
     * Is published at a given time point
     *
     * @param   int|string|\DateTime $now default: null
     * @return  bool
     */
    public function isPublished( $now = null );

}
