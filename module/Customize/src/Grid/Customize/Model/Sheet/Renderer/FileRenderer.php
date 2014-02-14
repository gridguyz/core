<?php

namespace Grid\Customize\Model\Sheet\Renderer;

use Grid\Customize\Model\Sheet\Exception;
use Grid\Customize\Model\Sheet\AbstractRenderer;

/**
 * FileRenderer
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class FileRenderer extends AbstractRenderer
{

    /**
     * @const int
     */
    const MAX_ATTEMPTS = 4;

    /**
     * @const int
     */
    const SLEEP_BETWEEN_ATTEMPTS = 10000;

    /**
     * @var string|null
     */
    protected $path;

    /**
     * @var resource
     */
    protected $handle;

    /**
     * Constructor
     *
     * @param   resource|string $file
     * @throws  InvalidArgumentException
     */
    public function __construct( $file )
    {
        if ( is_resource( $file ) )
        {
            $this->handle = $file;
        }
        else
        {
            $this->path     = (string) $file;
            $this->handle   = @ fopen( $this->path, 'wb' );

            if ( ! $this->handle )
            {
                throw new Exception\InvalidArgumentException( sprintf(
                    '%s: "%s" cannot be opened for wrinting',
                    __METHOD__,
                    $this->path
                ) );
            }
        }
    }

    /**
     * Destructor
     *
     * Closes the resource to the opened file, if still exists
     */
    public function __destruct()
    {
        if ( is_resource( $this->handle ) )
        {
            fclose( $this->handle );
        }
    }

    /**
     * @param   string  $line
     * @return  BufferRenderer
     */
    public function writeLine( $line )
    {
        $data       = $line . $this->getEol();
        $length     = strlen( $data );
        $fwrite     = 0;
        $attempt    = 1;

        for ( $written = 0; $written < $length; $written += $fwrite )
        {
            $fwrite = fwrite( $this->handle, substr( $data, $written ) );

            if ( $fwrite === 0 )
            {
                $attempt++;
                usleep( static::SLEEP_BETWEEN_ATTEMPTS );
            }
            else
            {
                $attempt = 1;
            }

            if ( $fwrite === false || $attempt > static::MAX_ATTEMPTS )
            {
                fclose( $this->handle );
                $this->handle = null;

                if ( $this->path )
                {
                    @ unlink( $this->path );
                }

                throw new Exception\RuntimeException( sprintf(
                    '%s: write error at %s (attempted %d times)',
                    __METHOD__,
                    $this->path
                        ? '"' . $this->path . '"'
                        : 'custom resource',
                    $attempt
                ) );
            }
        }

        return $this;
    }

}
