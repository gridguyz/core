<?php

namespace Grid\Paragraph\Model\Paragraph\Structure;

/**
 * AccessRestrictedInterface
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
interface AccessRestrictedInterface
{

    /**
     * Is accessible for the logged-in user
     *
     * @return  bool
     */
    public function isAccessible();

}
