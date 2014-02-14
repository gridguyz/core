<?php

namespace Grid\Customize\Model\Sheet;

/**
 * ParserInterface
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
interface ParserInterface
{

    /**
     * @param   Structure       $sheet
     * @param   string|resource $file
     * @return  ParserInterface
     */
    public function parseFile( Structure $sheet, $file );

    /**
     * @param   Structure       $sheet
     * @param   string          $data
     * @return  ParserInterface
     */
    public function parseString( Structure $sheet, $data );

}
