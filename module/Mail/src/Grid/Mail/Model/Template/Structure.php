<?php

namespace Grid\Mail\Model\Template;

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

    use LocaleAwareTrait;

    /**
     * @var string
     */
    const DEFAULT_LOCALE    = 'en';

    /**
     * Field: id
     *
     * @var int
     */
    protected $id           = null;

    /**
     * Mail template's name
     *
     * @var string
     */
    public $name            = '';

    /**
     * Mail template's sender address
     *
     * @var string
     */
    public $fromAddress     = null;

    /**
     * Mail template's sender name
     *
     * @var string
     */
    public $fromName        = null;

    /**
     * Mail template's subject
     *
     * @var string
     */
    public $subject         = '';

    /**
     * Mail template's content (html)
     *
     * @var string
     */
    public $bodyHtml        = '';

    /**
     * Mail template's content (text)
     *
     * @var string
     */
    public $bodyText        = null;

    /**
     * Clone this structure to a specific locale
     *
     * @param string $locale
     * @return \Mail\Model\Template\Structure
     */
    public function cloneToLocale( $locale )
    {
        $clone = clone $this;
        $clone->id = null;
        $clone->setLocale( $locale );
        return $clone;
    }

}
