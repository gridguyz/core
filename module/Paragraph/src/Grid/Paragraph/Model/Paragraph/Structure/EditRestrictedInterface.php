<?php

namespace Grid\Paragraph\Model\Paragraph\Structure;

/**
 * EditRestrictedInterface
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
interface EditRestrictedInterface
{

    /**
     * Is editable for the logged-in user
     *
     * @return  bool
     */
    public function isEditable();

}
