<?php

namespace Grid\Customize\Model;

/**
 * CssParser
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class CssParser
{

    /**
     * White-space characters
     *
     * @var string
     */
    const WHITE_SPACE = " \t\n\r\0\x0B";

    /**
     * Media state
     *
     * @var array
     */
    protected $media = array();

    /**
     * Buffer state
     *
     * @var string
     */
    protected $buffer = '';

    /**
     * Parse a css file
     *
     * @param string $file
     * @return \Customize\Model\Sheet\Structure
     */
    public function parse( $file )
    {
        if ( ! is_file( $file ) || ! is_readable( $file ) )
        {
            return null;
        }

        $sheet  = new Sheet\Structure();
        $this->buffer = @ file_get_contents( $file );
        $this->acceptBom();

        /* remove comments like this */
        $this->buffer = preg_replace( '#/\\*.*?\\*/#s', '', $this->buffer );

        while ( ! empty( $this->buffer ) )
        {
            $this->acceptEntry( $sheet );
        }

        return $sheet;
    }

    /**
     * Accept BOM characters
     *
     * @return void
     */
    protected function acceptBom()
    {
        $this->buffer = ltrim( $this->buffer, "\xEF\xBB\xBF" );
    }

    /**
     * Accept Unknown brackets
     *
     * @return void
     */
    protected function acceptUnknowBrackets()
    {
        $level = 0;

        do
        {
            switch ( $this->buffer[0] )
            {
                case '{':
                    $level++;
                    $this->buffer = substr( $this->buffer, 1 );
                    break;

                case '}':
                    $level--;
                    $this->buffer = substr( $this->buffer, 1 );
                    break;

                default:
                    $this->buffer = preg_replace(
                        '/^[^\{\}]+/', '',
                        $this->buffer
                    );
                    break;
            }
        }
        while ( $level > 0 );
    }

    /**
     * Accept string
     *
     * @param string $until
     * @return string
     */
    protected function acceptString( $until = ';' )
    {
        if ( '"' == $this->buffer[0] )
        {
            $matches        = array();
            $this->buffer   = substr( $this->buffer, 1 );

            if ( preg_match( '/^(.*?[^\\\\])"/', $this->buffer, $matches ) )
            {
                $result         = $matches[1];
                $this->buffer   = substr( $this->buffer, strlen( $matches[0] ) );
            }
            else
            {
                $result         = $this->buffer;
                $this->buffer   = '';
            }

            $result = str_replace(
                '\\\\', '\\',
                preg_replace_callback(
                    '/\\\\([0-9A-F]{0,6})/', function ( $matches ) {
                        return hex2bin( $matches[1] );
                    }, str_replace( '\\"', '"', $result )
                )
            );

            $this->buffer = preg_replace(
                '/^[^' . preg_quote( $until ) . ']+/',
                '', $this->buffer
            );
        }
        else
        {
            list( $result, $this->buffer ) = preg_split(
                '/[' . preg_quote( $until ) . ']/',
                $this->buffer, 2
            );
        }

        return $result;
    }

    /**
     * Accept url
     *
     * @param string $until
     * @return string
     */
    protected function acceptUrl( $until = ';' )
    {
        if ( 'url' == strtolower( substr( $this->buffer, 0, 3 ) ) )
        {
            $this->buffer = ltrim( substr( $this->buffer, 3 ), self::WHITE_SPACE );

            if ( '(' == $this->buffer[0] )
            {
                $this->buffer = ltrim( substr( $this->buffer, 1 ), self::WHITE_SPACE );
                $result = strtolower( $this->acceptString( ')' ) );

                if ( ')' == $this->buffer[0] )
                {
                    $this->buffer = ltrim( substr( $this->buffer, 1 ), self::WHITE_SPACE );
                }

                return $result;
            }
        }

        return $this->acceptString( $until );
    }

    /**
     * Accept safe value
     *
     * @param string $until
     * @return string
     */
    protected function acceptSafeValue( $until = '};' )
    {
        $result  = '';

        while ( ! empty( $this->buffer ) &&
                false === strpos( $until, $first = $this->buffer[0] ) )
        {
            $matches = array();

            if ( '"' == $first )
            {
                $result        .= '"';
                $this->buffer  = substr( $this->buffer, 1 );

                if ( preg_match( '/^.*?[^\\\\]"/', $this->buffer, $matches ) )
                {
                    $result        .= $matches[0];
                    $this->buffer   = substr( $this->buffer, strlen( $matches[0] ) );
                }
                else
                {
                    $result        .= $this->buffer;
                    $this->buffer   = '';
                    break;
                }
            }
            else
            {
                if ( preg_match( '/^[^' . preg_quote( $until ) .
                        '"]+/', $this->buffer, $matches ) )
                {
                    $result        .= preg_replace( '/\s+/', ' ', $matches[0] );
                    $this->buffer   = substr( $this->buffer, strlen( $matches[0] ) );
                }
                else
                {
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * Accept property
     *
     * @param \Customize\Model\Rule\Structure $rule
     * @return void
     */
    protected function acceptProperty( Rule\Structure & $rule )
    {
        @ list( $name, $this->buffer ) = explode( ':', $this->buffer, 2 );
        $this->buffer   = ltrim( $this->buffer, self::WHITE_SPACE );
        $name           = rtrim( $name, self::WHITE_SPACE );
        $value          = $this->acceptSafeValue();
        $matches        = array();

        if ( preg_match( '/!([a-z]+)$/', $value, $matches ) )
        {
            $priority = $matches[1];
            $value    = rtrim(
                substr( $value, 0, - strlen( $matches[0] ) ),
                self::WHITE_SPACE
            );
        }
        else
        {
            $priority = null;
        }

        $rule->setRawProperty( $name, $value, $priority );
    }

    /**
     * Accept entry
     *
     * @param \Customize\Model\Sheet\Structure $sheet
     * @return void
     */
    protected function acceptEntry( Sheet\Structure & $sheet )
    {
        $this->buffer = ltrim( $this->buffer, self::WHITE_SPACE . ';' );

        if ( ! empty( $this->buffer ) )
        {
            switch ( $this->buffer[0] )
            {
                case '@':
                    if ( '@charset' == strtolower( substr( $this->buffer, 0, 8 ) ) )
                    {
                        $this->buffer   = ltrim( substr( $this->buffer, 8 ), self::WHITE_SPACE );
                        $charset        = $this->acceptString();

                        if ( 'utf-8' != $charset )
                        {
                            if ( function_exists( 'iconv' ) )
                            {
                                $conv = @ iconv( $charset, 'utf-8', $this->buffer );
                            }
                            else
                            {
                                $conv = @ mb_convert_encoding( $this->buffer, 'utf-8', $charset );
                            }

                            if ( ! empty( $conv ) )
                            {
                                $this->buffer = $conv;
                            }
                        }
                    }
                    else if ( '@import' == strtolower( substr( $this->buffer, 0, 7 ) ) )
                    {
                        $this->buffer = ltrim( substr( $this->buffer, 7 ), self::WHITE_SPACE );
                        $sheet->addImport( $this->acceptUrl() );
                    }
                    else if ( '@media' == strtolower( substr( $this->buffer, 0, 6 ) ) )
                    {
                        $this->buffer = ltrim( substr( $this->buffer, 6 ), self::WHITE_SPACE );

                        list( $media,
                              $this->buffer ) = explode( '{', $this->buffer, 2 );

                        $this->buffer = ltrim( $this->buffer, self::WHITE_SPACE );
                        $media        = preg_replace(
                            '/\s+/', ' ',
                            rtrim( $media, self::WHITE_SPACE )
                        );

                        if ( ! empty( $this->media ) )
                        {
                            $media = end( $this->media ) . ' and ' . $media;
                        }

                        $this->media[] = $media;
                    }
                    else
                    {
                        $this->buffer = preg_replace( '/^[^\{\;]+/', '', $this->buffer );
                    }

                    break;

                case '}':
                    $this->buffer = ltrim( substr( $this->buffer, 1 ), self::WHITE_SPACE );

                    if ( ! empty( $this->media ) )
                    {
                        array_pop( $this->media );
                    }
                    break;

                case '{':
                    $this->acceptUnknowBrackets();
                    break;

                default:
                    list( $selector,
                          $this->buffer ) = explode( '{', $this->buffer, 2 );

                    $this->buffer   = ltrim( $this->buffer, self::WHITE_SPACE );
                    $rule           = new Rule\Structure( array(
                        'media'     => end( $this->media ),
                        'selector'  => preg_replace(
                            '/\s+/', ' ',
                            rtrim( $selector, self::WHITE_SPACE )
                        ),
                    ) );

                    while ( true )
                    {
                        $this->buffer = ltrim( $this->buffer, self::WHITE_SPACE . ';' );

                        if ( empty( $this->buffer ) )
                        {
                            break;
                        }

                        if ( '}' == $this->buffer[0] )
                        {
                            $this->buffer = substr( $this->buffer, 1 );
                            break;
                        }

                        $this->acceptProperty( $rule );
                    }

                    $sheet->addRule( $rule );
                    break;
            }
        }
    }

}
