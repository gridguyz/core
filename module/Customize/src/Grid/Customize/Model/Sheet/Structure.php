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
     * Render rules to a css-file
     *
     * @param string $filePath
     * @return \Customize\Model\Sheet\Structure
     */
    public function render( $filePath )
    {
        static $escape = array( '"' => '\\"' );
        $handle = @ fopen( $filePath, 'w' );

        if ( $handle )
        {
            fwrite(
                $handle,
                '@charset "' . strtr( self::RENDER_CHARSET, $escape ) . '";' .
                self::RENDER_EOL
            );

            foreach ( $this->imports as $import )
            {
                fwrite(
                    $handle,
                    '@import url("' . strtr( $import, $escape ) . '");' .
                    self::RENDER_EOL
                );
            }

            fwrite( $handle, self::RENDER_EOL );

            $media = '';
            foreach ( $this->rules as $rule )
            {
                $newMedia = (string) $rule->media;

                if ( $media != $newMedia )
                {
                    if ( $media )
                    {
                        fwrite(
                            $handle,
                            '}' . self::RENDER_EOL .
                            self::RENDER_EOL
                        );
                    }

                    $media = $newMedia;

                    fwrite(
                        $handle,
                        '@media ' . $media .
                        self::RENDER_EOL . '{' .
                        self::RENDER_EOL
                    );
                }

                $rawPropertyNames = $rule->getRawPropertyNames();

                if ( ! empty( $rawPropertyNames ) )
                {
                    fwrite(
                        $handle,
                        ( $media ? "\t" : '' ) .
                        $rule->selector .
                        self::RENDER_EOL .
                        ( $media ? "\t" : '' ) . '{' .
                        self::RENDER_EOL
                    );

                    foreach ( $rawPropertyNames as $propery )
                    {
                        fwrite(
                            $handle,
                            ( $media ? "\t" : '' ) .
                            "\t" . $propery . ': ' .
                            $rule->getRawPropertyValue( $propery ) .
                            $rule->getRawPropertyPostfix( $propery ) . ';' .
                            self::RENDER_EOL
                        );
                    }

                    fwrite(
                        $handle,
                        ( $media ? "\t" : '' ) .
                        '}' . self::RENDER_EOL .
                        self::RENDER_EOL
                    );
                }
            }

            if ( $media )
            {
                fwrite(
                    $handle,
                    '}' . self::RENDER_EOL .
                    self::RENDER_EOL
                );
            }

            fclose( $handle );
        }

        return $this;
    }

    /**
     * Render rules to a css-file
     *
     * @param string $schema
     * @return \Customize\Model\Sheet\Structure
     */
    public function _toSql( $schema )
    {
        $sql    = '';
        $schema = $schema ?: '_central';

        foreach ( $this->imports as $import )
        {
            $sql .= '-- import ' . $import . self::RENDER_EOL;
        }

        foreach ( $this->rules as $rule )
        {
            $sql .= 'INSERT INTO "' . strtr( $schema, array( '"' => '""' ) ) . '"."customize_rule" ("paragraphId", "selector", "media")' . self::RENDER_EOL;
            $sql .= '     VALUES (' . ( empty( $rule->paragraphId ) ? 'NULL' : (int) $rule->paragraphId ) . ', \'' . strtr( $rule->selector, array( '\'' => '\'\'' ) ) . '\', \'' . strtr( $rule->media, array( '\'' => '\'\'' ) ) . '\');' . self::RENDER_EOL . self::RENDER_EOL;

            $rawPropertyNames = $rule->getRawPropertyNames();

            if ( ! empty( $rawPropertyNames ) )
            {
                $sql .= 'INSERT INTO "_central"."customize_property" ("ruleId", "name", "value", "priority")' . self::RENDER_EOL;
                $sql .= '     VALUES ';
                $first = true;

                foreach ( $rawPropertyNames as $propery )
                {
                    if ( $first )
                    {
                        $first = false;
                    }
                    else
                    {
                        $sql .= ',' . self::RENDER_EOL . '            ';
                    }

                    $value = $rule->getRawPropertyValue( $propery );
                    $prio  = $rule->getRawPropertyPriority( $propery );
                    $sql .= '(currval(\'' . strtr( $schema, array( '"' => '""' ) ) . '.customize_rule_id_seq\'), \'' .
                            strtr( $propery, array( '\'' => '\'\'' ) ) . '\', \'' .
                            strtr( $value, array( '\'' => '\'\'' ) ) . '\', ' .
                            ( empty( $prio ) ? 'NULL' : '\'' . strtr( $prio, array( '\'' => '\'\'' ) ) . '\'' ) . ')';
                }

                $sql .= ';' . self::RENDER_EOL . self::RENDER_EOL . self::RENDER_EOL;
            }
        }

        return $sql;
    }

}