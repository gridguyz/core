<?php

namespace Grid\Customize\Model\Sheet;

use DateTime;
use Zork\Model\Structure\MapperAwareAbstract;
use Grid\Customize\Model\Rule\Structure as RuleStructure;
use Grid\Customize\Model\Extra\Structure as ExtraStructure;

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
     * Root paragraph id
     *
     * @var int|null
     */
    protected $rootId;

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
     * @param   int $rootId
     * @return  \Grid\Customize\Model\Sheet\Structure
     */
    public function setRootId( $rootId )
    {
        $this->rootId = ( (int) $rootId ) ?: null;
        return $this;
    }

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
     * @return ExtraStructure
     */
    public function getExtra()
    {
        if ( null === $this->extra )
        {
            if ( ( $mapper      = $this->getMapper() ) &&
                 ( $extraMapper = $mapper->getExtraMapper() ) )
            {
                if ( $this->rootId )
                {
                    $this->extra = $extraMapper->find( $this->rootId );
                }

                if ( ! $this->extra )
                {
                    $this->extra = $extraMapper->create( array() );
                }
            }
            else
            {
                $this->extra = new ExtraStructure( array(
                    'id' => $this->rootId,
                ) );
            }
        }

        return $this->extra;
    }

    /**
     * @param   ExtraStructure|null $extra
     * @return  \Grid\Customize\Model\Sheet\Structure
     */
    public function setExtra( ExtraStructure $extra = null )
    {
        $this->extra = $extra;
        return $this;
    }

    /**
     * @return string
     */
    public function getExtraContent()
    {
        return $this->getExtra()->extra;
    }

    /**
     * @param   string|null $extraContent
     * @return  \Grid\Customize\Model\Sheet\Structure
     */
    public function setExtraContent( $extraContent )
    {
        $this->getExtra()->extra = trim( $extraContent ) ?: null;
        return $this;
    }

    /**
     * @return  bool
     */
    public function hasExtraContent()
    {
        return isset( $this->extra ) &&
               ! empty( $this->extra->extra );
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
     * Reset all contents
     *
     * @return  Structure
     */
    public function resetContents()
    {
        $this->setImports( array() )
             ->setRules( array() );

        if ( $this->hasExtraContent() )
        {
            $this->setExtraContent( null );
        }

        return $this;
    }

    /**
     * Render rules to a css-file
     *
     * @param   RendererInterface|resource|string|null  $file
     * @param   string|null                             $eol
     * @return  RendererInterface
     */
    public function render( $file = null, $eol = null )
    {
        static $escape = array( '"' => '\\"' );
        $renderer   = AbstractRenderer::factory( $file, $eol );
        $eol        = $renderer->getEol();

        $renderer->writeLine(
            '@charset "' . strtr( static::RENDER_CHARSET, $escape ) . '";'
        );

        if ( ! $this->comment && $this->extra && $this->extra->updated )
        {
            $this->comment = $this->extra
                                  ->updated
                                  ->format( DateTime::ISO8601 );
        }

        if ( $this->comment )
        {
            $commentLines = array_map(
                function ( $line ) {
                    return ' * ' . $line;
                },
                preg_split( '/\s*[\n\r]+\s*/', $this->comment )
            );

            array_unshift( $commentLines, $eol . '/**' );
            array_push( $commentLines, ' */' );

            foreach ( $commentLines as $comment )
            {
                $renderer->writeLine( $comment );
            }
        }

        foreach ( $this->imports as $import )
        {
            $renderer->writeLine(
                '@import url("' . strtr( $import, $escape ) . '");' . $eol
            );
        }

        $media = '';
        foreach ( $this->rules as $rule )
        {
            $newMedia = (string) $rule->media;

            if ( $media != $newMedia )
            {
                if ( $media )
                {
                    $renderer->writeLine( '}' . $eol );
                }

                $media = $newMedia;
                $renderer->writeLine( '@media ' . $media . $eol . '{' . $eol );
            }

            $rawPropertyNames = $rule->getRawPropertyNames();

            if ( ! empty( $rawPropertyNames ) )
            {
                $renderer->writeLine(
                    ( $media ? "\t" : '' ) .
                    $rule->selector . $eol .
                    ( $media ? "\t" : '' ) . '{'
                );

                foreach ( $rawPropertyNames as $propery )
                {
                    $renderer->writeLine(
                        ( $media ? "\t" : '' ) .
                        "\t" . $propery . ': ' .
                        $rule->getRawPropertyValue( $propery ) .
                        $rule->getRawPropertyPostfix( $propery ) . ';'
                    );
                }

                $renderer->writeLine( ( $media ? "\t" : '' ) . '}' . $eol );
            }
        }

        if ( $media )
        {
            $renderer->writeLine( '}' );
        }

        if ( $this->hasExtraContent() )
        {
            $renderer->writeLine( $eol . $this->getExtraContent() );
        }

        return $renderer;
    }

    /**
     * @param   string|resource $file
     * @return  ParserInterface
     */
    public function parseFile( $file )
    {
        $parser = new Parser;
        return $parser->parseFile( $this, $file );
    }

    /**
     * @param   string  $data
     * @return  ParserInterface
     */
    public function parseString( $data )
    {
        $parser = new Parser;
        return $parser->parseString( $this, $data );
    }

}
