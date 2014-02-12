<?php

namespace Grid\Customize\Model\Sheet;

use Zork\Model\Structure\MapperAwareAbstract;
use Grid\Customize\Model\Rule\Structure as RuleStructure;

/**
 * Rule structure
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Structure extends MapperAwareAbstract
{

    /**
     * @var string
     */
    const RENDER_CHARSET    = 'utf-8';

    /**
     * @var string
     */
    const RENDER_EOL        = PHP_EOL;

    /**
     * Imports
     *
     * @var array
     */
    protected $imports = array();

    /**
     * Rules
     *
     * @var \Customize\Model\Rule\Structure[]
     */
    protected $rules = array();

    /**
     * Extra css
     *
     * @var string|null
     */
    protected $extra;

    /**
     * Comment
     *
     * @var string|null
     */
    protected $comment;

    /**
     * Get all imports
     *
     * @param array $imports
     * @return \Customize\Model\Sheet\Structure
     */
    public function setImports( $imports )
    {
        $this->imports = (array) $imports;
        return $this;
    }

    /**
     * Add import
     *
     * @param string $import
     * @return \Customize\Model\Sheet\Structure
     */
    public function addImport( $import )
    {
        $this->imports[] = $import;
        return $this;
    }

    /**
     * Set all rules
     *
     * @param \Customize\Model\Rule\Structure[] $rules
     * @return \Customize\Model\Sheet\Structure
     */
    public function setRules( $rules )
    {
        $this->rules = array();

        foreach ( $rules as $rule )
        {
            $this->addRule( $rule );
        }

        return $this;
    }

    /**
     * Add a rule
     *
     * @param array|\Customize\Model\Rule\Structure $rule
     * @return \Customize\Model\Sheet\Structure
     */
    public function addRule( $rule )
    {
        if ( ! ( $rule instanceof RuleStructure ) )
        {
            $rule = new RuleStructure( $rule );
        }

        $this->rules[] = $rule;
        return $this;
    }

    /**
     * @param   string  $extra
     * @return  \Grid\Customize\Model\Sheet\Structure
     */
    public function setExtra( $extra )
    {
        $this->extra = trim( $extra ) ?: null;
        return $this;
    }

    /**
     * @param   string  $comment
     * @return  \Grid\Customize\Model\Sheet\Structure
     */
    public function setComment( $comment )
    {
        $this->comment = trim( $comment ) ?: null;
        return $this;
    }

    /**
     * Render line
     *
     * @param   null|resource   $handle
     * @param   string          $line
     * @param   string|bool     $result
     * @return  bool
     */
    protected function renderLine( $handle, $line, $result )
    {
        $write = $line . static::RENDER_EOL;

        if ( $handle )
        {
            if ( false === fwrite( $handle, $write ) )
            {
                $result = false;
                return true;
            }
        }
        else
        {
            $result .= $write;
        }

        return false;
    }

    /**
     * Render rules to a css-file
     *
     * @param   string|resource|null    $file
     * @return  bool|string
     */
    public function render( $file = null )
    {
        static $escape = array( '"' => '\\"' );

        if ( empty( $file ) )
        {
            $path   = null;
            $handle = null;
            $result = '';
        }
        else if ( is_resource( $file ) )
        {
            $path   = null;
            $handle = $file;
            $result = true;
        }
        else
        {
            $path   = (string) $file;
            $handle = @ fopen( $path, 'w' );
            $result = true;

            if ( ! $handle )
            {
                return false;
            }
        }

        if ( $this->renderLine( $handle,
                                '@charset "' . strtr( static::RENDER_CHARSET,
                                                      $escape ) . '";',
                                $result ) )
        {
            if ( $handle )
            {
                @ fclose( $handle );

                if ( $path )
                {
                    @ unlink( $path );
                }
            }

            return $result;
        }

        if ( $this->comment )
        {
            $commentLines = array_map(
                function ( $line ) {
                    return ' * ' . $line;
                },
                preg_split( '/\s*[\n\r]+\s*/', $this->comment )
            );

            array_unshift( $commentLines, static::RENDER_EOL . '/**' );
            array_push( $commentLines, ' */' );

            foreach ( $commentLines as $comment )
            {
                if ( $this->renderLine( $handle, $comment, $result ) )
                {
                    if ( $handle )
                    {
                        @ fclose( $handle );

                        if ( $path )
                        {
                            @ unlink( $path );
                        }
                    }

                    return $result;
                }
            }
        }

        foreach ( $this->imports as $import )
        {
            if ( $this->renderLine( $handle,
                                    '@import url("' . strtr( $import,
                                                             $escape ) . '");',
                                    $result ) )
            {
                if ( $handle )
                {
                    @ fclose( $handle );

                    if ( $path )
                    {
                        @ unlink( $path );
                    }
                }

                return $result;
            }
        }

        if ( $this->renderLine( $handle, '', $result ) )
        {
            if ( $handle )
            {
                @ fclose( $handle );

                if ( $path )
                {
                    @ unlink( $path );
                }
            }

            return $result;
        }

        $media = '';
        foreach ( $this->rules as $rule )
        {
            $newMedia = (string) $rule->media;

            if ( $media != $newMedia )
            {
                if ( $media )
                {
                    if ( $this->renderLine( $handle,
                                            '}' . static::RENDER_EOL,
                                            $result ) )
                    {
                        if ( $handle )
                        {
                            @ fclose( $handle );

                            if ( $path )
                            {
                                @ unlink( $path );
                            }
                        }

                        return $result;
                    }
                }

                $media = $newMedia;

                if ( $this->renderLine( $handle,
                                        '@media ' . $media . static::RENDER_EOL
                                            . '{' . static::RENDER_EOL,
                                        $result ) )
                {
                    if ( $handle )
                    {
                        @ fclose( $handle );

                        if ( $path )
                        {
                            @ unlink( $path );
                        }
                    }

                    return $result;
                }
            }

            $rawPropertyNames = $rule->getRawPropertyNames();

            if ( ! empty( $rawPropertyNames ) )
            {
                if ( $this->renderLine( $handle,
                                        ( $media ? "\t" : '' ) .
                                        $rule->selector .
                                        static::RENDER_EOL .
                                        ( $media ? "\t" : '' ) . '{',
                                        $result ) )
                {
                    if ( $handle )
                    {
                        @ fclose( $handle );

                        if ( $path )
                        {
                            @ unlink( $path );
                        }
                    }

                    return $result;
                }

                foreach ( $rawPropertyNames as $propery )
                {
                    if ( $this->renderLine( $handle,
                                            ( $media ? "\t" : '' ) .
                                            "\t" . $propery . ': ' .
                                            $rule->getRawPropertyValue( $propery ) .
                                            $rule->getRawPropertyPostfix( $propery ) . ';',
                                            $result ) )
                    {
                        if ( $handle )
                        {
                            @ fclose( $handle );

                            if ( $path )
                            {
                                @ unlink( $path );
                            }
                        }

                        return $result;
                    }
                }

                if ( $this->renderLine( $handle,
                                        ( $media ? "\t" : '' ) .
                                        '}' . static::RENDER_EOL,
                                        $result ) )
                {
                    if ( $handle )
                    {
                        @ fclose( $handle );

                        if ( $path )
                        {
                            @ unlink( $path );
                        }
                    }

                    return $result;
                }
            }
        }

        if ( $media )
        {
            if ( $this->renderLine( $handle, '}', $result ) )
            {
                if ( $handle )
                {
                    @ fclose( $handle );

                    if ( $path )
                    {
                        @ unlink( $path );
                    }
                }

                return $result;
            }
        }

        if ( $this->extra )
        {
            $extra = static::RENDER_EOL . $this->extra;

            if ( $this->renderLine( $handle, $extra, $result ) )
            {
                if ( $handle )
                {
                    @ fclose( $handle );

                    if ( $path )
                    {
                        @ unlink( $path );
                    }
                }

                return $result;
            }
        }

        if ( $handle )
        {
            @ fclose( $handle );
        }

        return $result;
    }

}