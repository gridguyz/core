<?php

namespace Grid\Customize\Model\Sheet;

use Grid\Customize\Model\Rule\Structure as RuleStructure;

/**
 * Parser
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Parser implements ParserInterface
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
     * Extra states
     *
     * @var array
     */
    protected $extras = array();

    /**
     * Buffer state
     *
     * @var string
     */
    protected $buffer = '';

    /**
     * Sheet state
     *
     * @var Structure
     */
    protected $sheet;

    /**
     * @param   Structure       $sheet
     * @param   string|resource $file
     * @return  ParserInterface
     */
    public function parseFile( Structure $sheet, $file )
    {
        $this->sheet = $sheet;

        if ( is_resource( $file ) )
        {
            $this->buffer = stream_get_contents( $file );
        }
        else if ( ! is_file( $file ) || ! is_readable( $file ) )
        {
            throw new Exception\InvalidArgumentException( sprintf(
                '%s: $file "%s" is not readable, or does not exists',
                __METHOD__,
                $file
            ) );
        }
        else
        {
            $this->buffer = file_get_contents( $file );
        }

        $this->parse();
        return $this;
    }

    /**
     * @param   Structure       $sheet
     * @param   string          $data
     * @return  ParserInterface
     */
    public function parseString( Structure $sheet, $data )
    {
        $this->sheet    = $sheet;
        $this->buffer   = (string) $data;
        $this->parse();
        return $this;
    }

    /**
     * Parse css content
     *
     * @return  void
     */
    protected function parse()
    {
        $this->media    = array();
        $this->extras   = array();
        $this->sheet->resetContents();
        $this->acceptBom();

        while ( ! empty( $this->buffer ) )
        {
            $this->acceptEntry();
        }

        $extraContent = '';

        foreach ( $this->extras as $media => $extra )
        {
            if ( empty( $media ) )
            {
                $extraContent .= $extra;
            }
            else
            {
                $extraContent .= "@media $media\n{\n$extra\n}\n";
            }

            $extraContent .= "\n";
        }

        $this->sheet->setExtraContent( $extraContent );
    }

    /**
     * Append extra data
     *
     * @param   string  $data
     * @return  void
     */
    protected function appendExtra( $data )
    {
        $media = end( $this->media );

        if ( ! isset( $this->extras[$media] ) )
        {
            $this->extras[$media] = '';
        }

        $this->extras[$media] .= $data;
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
     * Accept white-space & comments
     *
     * @param   string  $additional
     * @return  void
     */
    public function acceptWhiteSpace( $additional = '' )
    {
        $this->buffer = ltrim( $this->buffer, self::WHITE_SPACE . $additional );

        while ( substr( $this->buffer, 0, 2 ) == '/*' )
        {
            $this->buffer = ltrim(
                preg_replace( '#/\*.*?\*/#s', '', $this->buffer ),
                self::WHITE_SPACE . $additional
            );
        }
    }

    /**
     * Accept Unknown brackets
     *
     * @return  string
     */
    protected function acceptUnknowBrackets()
    {
        $level  = 0;
        $result = '';

        do
        {
            switch ( $this->buffer[0] )
            {
                case '{':
                    $level++;
                    $result .= '{';
                    $this->buffer = substr( $this->buffer, 1 );
                    break;

                case '}':
                    $level--;
                    $result .= '}';
                    $this->buffer = substr( $this->buffer, 1 );
                    break;

                default:
                    $newBuffer = preg_replace(
                        '/^[^\{\}]+/', '',
                        $this->buffer
                    );

                    $result .= substr(
                        $this->buffer,
                        0,
                        strlen( $this->buffer ) - strlen( $newBuffer )
                    );

                    $this->buffer = $newBuffer;
                    break;
            }
        }
        while ( $level > 0 );

        return $result;
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
            $this->buffer = substr( $this->buffer, 3 );
            $this->acceptWhiteSpace();

            if ( '(' == $this->buffer[0] )
            {
                $this->buffer = substr( $this->buffer, 1 );
                $this->acceptWhiteSpace();
                $result = $this->acceptString( ')' );

                if ( ')' == $this->buffer[0] )
                {
                    $this->buffer = substr( $this->buffer, 1 );
                    $this->acceptWhiteSpace();
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

                if ( $this->buffer && '"' == $this->buffer[0] )
                {
                    $result        .= '"';
                    $this->buffer   = substr( $this->buffer, 1 );
                }
                else if ( preg_match( '/^.*?[^\\\\]"/', $this->buffer, $matches ) )
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
     * @param   RuleStructure $rule
     * @return  void
     */
    protected function acceptProperty( RuleStructure &$rule )
    {
        @ list( $name, $this->buffer ) = explode( ':', $this->buffer, 2 );
        $this->acceptWhiteSpace();

        $name       = rtrim( $name, self::WHITE_SPACE );
        $value      = $this->acceptSafeValue();
        $matches    = array();

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
     * @return  void
     */
    protected function acceptEntry()
    {
        $this->acceptWhiteSpace( ';' );

        if ( ! empty( $this->buffer ) )
        {
            switch ( $this->buffer[0] )
            {
                case '@':
                    if ( '@charset' == strtolower( substr( $this->buffer, 0, 8 ) ) )
                    {
                        $this->buffer = substr( $this->buffer, 8 );
                        $this->acceptWhiteSpace();
                        $charset = $this->acceptString();

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
                        $oldBuffer = $this->buffer;
                        $this->buffer = substr( $this->buffer, 7 );
                        $this->acceptWhiteSpace();
                        $this->sheet->addImport( $this->acceptUrl() );
                        $this->acceptSafeValue();

                        $this->appendExtra( substr(
                            $oldBuffer,
                            0,
                            strlen( $oldBuffer ) - strlen( $this->buffer )
                        ) . ";\n" );

                        $oldBuffer = null;
                    }
                    else if ( '@media' == strtolower( substr( $this->buffer, 0, 6 ) ) )
                    {
                        $this->buffer = substr( $this->buffer, 6 );
                        $this->acceptWhiteSpace();

                        list( $media,
                              $this->buffer ) = explode( '{', $this->buffer, 2 );

                        $this->acceptWhiteSpace();
                        $media = preg_replace(
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
                        $newBuffer = preg_replace( '/^[^\{\;]+/', '', $this->buffer );

                        $this->appendExtra( substr(
                            $this->buffer,
                            0,
                            strlen( $this->buffer ) - strlen( $newBuffer )
                        ) );

                        $this->buffer = $newBuffer;
                    }

                    break;

                case '}':
                    $this->buffer = substr( $this->buffer, 1 );
                    $this->acceptWhiteSpace();

                    if ( ! empty( $this->media ) )
                    {
                        array_pop( $this->media );
                    }
                    break;

                case '{':
                    $this->appendExtra( $this->acceptUnknowBrackets() );
                    break;

                default:
                    list( $selector,
                          $this->buffer ) = explode( '{', $this->buffer, 2 );

                    $this->acceptWhiteSpace();
                    $rule = array(
                        'media'     => end( $this->media ),
                        'selector'  => preg_replace(
                            '/\s+/', ' ',
                            rtrim( $selector, self::WHITE_SPACE )
                        ),
                    );

                    if ( ( $mapper = $this->sheet->getMapper() ) &&
                         ( $ruleMapper = $mapper->getRuleMapper() ) )
                    {
                        $rule = $ruleMapper->create( $rule );
                    }
                    else
                    {
                        $rule = new RuleStructure( $rule );
                    }

                    while ( true )
                    {
                        $this->acceptWhiteSpace( ';' );

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

                    $this->sheet->addRule( $rule );
                    break;
            }
        }
    }

}
