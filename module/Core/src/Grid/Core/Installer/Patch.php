<?php

namespace Grid\Core\Installer;

use Grid\Installer\AbstractPatch;

/**
 * Patch
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Patch extends AbstractPatch
{

    /**
     * Run after patching
     *
     * @param   string  $from
     * @param   string  $to
     * @return  void
     */
    public function afterPatch( $from, $to )
    {
        if ( $this->isZeroVersion( $from ) )
        {
            $email = $this->getPatchData()
                          ->get(
                                'core-developer',
                                'email',
                                'Type the developer user\'s email'
                            );

            $passw = $this->getPatchData()
                          ->get(
                                'core-developer',
                                'password',
                                'Type the developer user\'s password'
                            );

            var_dump(array($email, $passw));
        }
    }

}
