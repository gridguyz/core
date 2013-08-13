<?php

namespace Grid\Core\Form;

/**
 * TransformValues
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
interface TransformValues
{

    /**
     * Transform form values
     *
     * @param   array   $values
     * @return  array
     */
    public function transformValues( array $values );

}
