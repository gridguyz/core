<?php

namespace Grid\Mail\Model\Settings\Structure;

use Grid\Core\Model\Settings\StructureAbstract;

/**
 * Mail
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Mail extends StructureAbstract
{

    /**
     * @const string
     */
    const ACCEPTS_SECTION   = 'mail';

    /**
     * Field: section
     *
     * @var int
     */
    protected $section      = self::ACCEPTS_SECTION;

}
