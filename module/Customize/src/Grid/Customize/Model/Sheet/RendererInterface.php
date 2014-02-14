<?php

namespace Grid\Customize\Model\Sheet;

/**
 * RendererInterface
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
interface RendererInterface
{

    /**
     * @const string
     */
    const DEFAULT_EOL = PHP_EOL;

    /**
     * @return  string
     */
    public function getEol();

    /**
     * @param   string  $eol
     * @return  RendererInterface
     */
    public function setEol( $eol );

    /**
     * @param   string  $line
     * @return  RendererInterface
     */
    public function writeLine( $line );

}
