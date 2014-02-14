<?php

namespace Grid\Customize\Model\Sheet;

/**
 * AbstractRenderer
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
abstract class AbstractRenderer implements RendererInterface
{

    /**
     * @var string
     */
    protected $eol = self::DEFAULT_EOL;

    /**
     * @return string
     */
    public function getEol()
    {
        return $this->eol;
    }

    /**
     * @param   string  $eol
     * @return  AbstractRenderer
     */
    public function setEol( $eol )
    {
        $this->eol = ( (string) $eol ) ?: static::DEFAULT_EOL;
        return $this;
    }

    /**
     * @param   RendererInterface|resource|string|null  $file
     * @param   string|null                             $eol
     * @return  RendererInterface
     */
    public static function factory( $file = null, $eol = null )
    {
        if ( $file instanceof RendererInterface )
        {
            $renderer = $file;
        }
        else if ( is_resource( $file ) || ! empty( $file ) )
        {
            $renderer = new Renderer\FileRenderer( $file );
        }
        else
        {
            $renderer = new Renderer\BufferRenderer;
        }

        if ( null !== $eol )
        {
            $renderer->setEol( $eol );
        }

        return $renderer;
    }

}
