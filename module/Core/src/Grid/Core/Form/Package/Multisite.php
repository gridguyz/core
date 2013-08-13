<?php

namespace Grid\Core\Form\Package;

use Zork\Form\Form;
use Grid\Core\Form\TransformValues;

/**
 * Multisite
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Multisite extends Form implements TransformValues
{

    /**
     * Transform form values
     *
     * @param   array   $values
     * @return  array
     */
    public function transformValues( array $values )
    {
        if ( ! empty( $values['gridguyz-multisite']['defaultDomain'] ) )
        {
            $values['gridguyz-multisite']['defaultDomain'] = trim(
                $values['gridguyz-multisite']['defaultDomain'],
                '.'
            );
        }

        if ( ! empty( $values['gridguyz-multisite']['domainPostfix'] ) )
        {
            $values['gridguyz-multisite']['domainPostfix'] = '.' . trim(
                $values['gridguyz-multisite']['domainPostfix'],
                '.'
            );
        }

        return $values;
    }

}
