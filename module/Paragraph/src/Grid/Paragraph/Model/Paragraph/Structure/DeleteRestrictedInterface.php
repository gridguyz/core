<?php

namespace Grid\Paragraph\Model\Paragraph\Structure;

/**
 * DeleteRestrictedInterface
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
interface DeleteRestrictedInterface
{

    /**
     * Is deletable for the logged-in user
     *
     * @return  bool
     */
    public function isDeletable();

}
