<?php

namespace Grid\Core\View\Helper;

use Zork\Stdlib\OptionsTrait;
use Zend\View\Helper\AbstractHelper;
use dflydev\markdown\MarkdownParser;
use dflydev\markdown\MarkdownExtraParser;

/**
 * Markdown view helper
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Markdown extends AbstractHelper
{

    use OptionsTrait;

    /**
     * @const bool
     */
    const DEFAULT_EXTRA_ENABLED = true;

    /**
     * @const string
     */
    const DEFAULT_LINK_TARGET = '_blank';

    /**
     * @const bool
     */
    const DEFAULT_STRIP_SCRIPTS = true;

    /**
     * @var bool
     */
    protected $extraEnabled = self::DEFAULT_EXTRA_ENABLED;

    /**
     * @var string
     */
    protected $linkTarget = self::DEFAULT_LINK_TARGET;

    /**
     * @var bool
     */
    protected $stripScripts = self::DEFAULT_STRIP_SCRIPTS;

    /**
     * Constructor
     *
     * @param   array   $options
     */
    public function __construct( $options = null )
    {
        if ( null !== $options )
        {
            $this->setOptions( $options );
        }
    }

    /**
     * Transform text from markdown to html
     *
     * @param   string  $text
     * @param   array   $options
     * @return  string
     */
    public function transform( $text, $options = null )
    {
        if ( null !== $options )
        {
            $this->setOptions( $options );
        }

        $parser = $this->extraEnabled
                ? new MarkdownExtraParser
                : new MarkdownParser;
        $parsed = $parser->transformMarkdown( $text );

        if ( ! empty( $this->linkTarget ) )
        {
            $parsed = preg_replace(
                '#<(a|form)((\s[^>]*)?)>(.*?)</\\1>#',
                '<$1 target="' . htmlspecialchars( $this->linkTarget ) . '"$2>$4</$1>',
                $parsed
            );
        }

        if ( $this->stripScripts )
        {
            $parsed = preg_replace(
                '#<script(\s[^>]*)?>.*?</script>#',
                '',
                $parsed
            );
        }

        return $parsed;
    }

    /**
     * Invokable helper
     *
     * @param   string  $text
     * @param   array   $options
     * @return  string|\Grid\Core\View\Helper\Markdown
     */
    public function __invoke( $text = null, $options = null )
    {
        if ( null === $text )
        {
            return $this;
        }

        return $this->transform( $text, $options );
    }

}
