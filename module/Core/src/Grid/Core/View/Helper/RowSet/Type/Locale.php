<?php

namespace Grid\Core\View\Helper\RowSet\Type;

/**
 * Locale
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Locale extends Translate
{

    /**
     * @var string
     */
    protected $translatePrefix      = 'locale.sub';

    /**
     * @var string
     */
    protected $translatePostfix     = '';

    /**
     * @var string
     */
    protected $translateTextDomain  = 'locale';

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

}
