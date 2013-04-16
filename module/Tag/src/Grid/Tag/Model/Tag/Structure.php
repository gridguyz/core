<?php

namespace Grid\Tag\Model\Tag;

use Zork\Model\LocaleAwareTrait;
use Zork\Model\LocaleAwareInterface;
use Zork\Model\Structure\MapperAwareAbstract;

/**
 * Structure
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Structure extends MapperAwareAbstract
             implements LocaleAwareInterface
{

    use LocaleAwareTrait
    {
        LocaleAwareTrait::getLocale as getLocaleFallback;
    }

    /**
     * Field: id
     *
     * @var int
     */
    protected $id;

    /**
     * Field: name
     *
     * @var string
     */
    public $name = '';

    /**
     * Get locale parameter for structure
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

}
