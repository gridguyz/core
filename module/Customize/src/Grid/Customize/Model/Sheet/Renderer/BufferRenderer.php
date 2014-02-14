<?php

namespace Grid\Customize\Model\Sheet\Renderer;

use Grid\Customize\Model\Sheet\AbstractRenderer;

/**
 * BufferRenderer
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class BufferRenderer extends AbstractRenderer
{

    /**
     * @var string
     */
    protected $buffer = '';

    /**
     * @return  string
     */
    public function getBuffer()
    {
        return $this->buffer;
    }

    /**
     * @param   string  $line
     * @return  BufferRenderer
     */
    public function writeLine( $line )
    {
        $this->buffer .= $line . $this->getEol();
        return $this;
    }

}
