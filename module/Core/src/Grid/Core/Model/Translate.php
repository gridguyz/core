<?php

namespace Grid\Core\Model;

use Zork\Rpc\CallableTrait;
use Zork\Rpc\CallableInterface;
use Zend\I18n\Translator\TextDomain;
use Zork\I18n\Translator\Translator;

/**
 * Translate
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Translate extends Translator
             implements CallableInterface
{

    use CallableTrait;

    /**
     * @var string
     */
    const DEFAULT_TEXT_DOMAIN = 'default';

    /**
     * @var array
     */
    protected static $onlyCallableMethods = array(
        'translate',
        'translatePlural',
        'textDomain',
    );

    /**
     * @var \Zend\I18n\Translator\Translator
     */
    protected $translator;

    /**
     * @return \Zend\I18n\Translator\Translator
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * @param \Zend\I18n\Translator\Translator $translator
     * @return \Core\Model\Translate
     */
    public function setTranslator( Translator $translator )
    {
        $this->translator = $translator;
        return $this;
    }

    /**
     * Constructor
     *
     * @param \Zend\I18n\Translator\Translator $translator
     */
    public function __construct( Translator $translator )
    {
        $this->setTranslator( $translator );
    }

    /**
     * Translate a message.
     *
     * @param  string $message
     * @param  string $textDomain
     * @param  string $locale
     * @return string
     */
    public function translate( $message,
                               $textDomain = self::DEFAULT_TEXT_DOMAIN,
                               $locale     = null )
    {
        return $this->getTranslator()
                    ->translate( $message, $textDomain, $locale );
    }

    /**
     * Translate a plural message.
     *
     * @param  string      $singular
     * @param  string      $plural
     * @param  int         $number
     * @param  string      $textDomain
     * @param  string|null $locale
     * @return string
     * @throws Exception\OutOfBoundsException
     */
    public function translatePlural( $singular,
                                     $plural,
                                     $number,
                                     $textDomain = self::DEFAULT_TEXT_DOMAIN,
                                     $locale     = null )
    {
        return $this->getTranslator()
                    ->translatePlural( $singular, $plural, $number,
                                       $textDomain, $locale );
    }

    /**
     * Get a whole text-domain
     *
     * @param string $textDomain
     * @param string|null $locale
     * @return array
     */
    public function textDomain( $textDomain = self::DEFAULT_TEXT_DOMAIN,
                                $locale     = null )
    {
        $translator = $this->getTranslator();
        $locale     = $locale ?: $translator->getLocale();

        if ( ! isset( $translator->myMessages[$textDomain][$locale] ) )
        {
            $translator->loadMyMessages( $textDomain, $locale );
        }

        if ( ! isset( $translator->messages[$textDomain][$locale] ) )
        {
            $translator->loadMessages( $textDomain, $locale );
        }

        $my     = $translator->myMessages[$textDomain][$locale];
        $global = $translator->messages[$textDomain][$locale];

        if ( $my instanceof TextDomain )
        {
            $my = $my->getArrayCopy();
        }
        else if ( empty( $my ) )
        {
            $my = array();
        }

        if ( $global instanceof TextDomain )
        {
            $global = $global->getArrayCopy();
        }
        else if ( empty( $global ) )
        {
            $global = array();
        }

        return array_replace( $global, $my );
    }

}
