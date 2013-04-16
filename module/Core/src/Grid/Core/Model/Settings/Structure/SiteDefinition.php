<?php

namespace Grid\Core\Model\Settings\Structure;

use Zork\Db\FileTrait;
use Zork\Db\SiteInfoAwareInterface;
use Grid\Core\Model\Settings\StructureAbstract;

/**
 * SiteDefinition
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class SiteDefinition extends StructureAbstract
                  implements SiteInfoAwareInterface
{

    use FileTrait;

    /**
     * @const string
     */
    const ACCEPTS_SECTION   = 'site-definition';

    /**
     * Field: section
     *
     * @var int
     */
    protected $section      = self::ACCEPTS_SECTION;

    /**
     * @param string $file
     * @return string
     */
    protected function getMime( $file )
    {
        static $finfo = null;
        $mime = null;

        if ( empty( $file ) )
        {
            return null;
        }

        $full = './public/' . ltrim( $file, '/' );

        if ( null === $mime &&
             function_exists( 'mime_content_type' ) &&
             ini_get( 'mime_magic.magicfile' ) )
        {
            $mime = mime_content_type( $full );
        }

        if ( null === $mime &&
             class_exists( 'finfo', false ) )
        {
            if ( empty( $finfo ) )
            {
                $finfo = @ finfo_open(
                    defined( 'FILEINFO_MIME_TYPE' )
                        ? FILEINFO_MIME_TYPE
                        : FILEINFO_MIME
                );
            }

            if ( ! empty( $finfo ) )
            {
                $mime = finfo_file( $finfo, $full );
            }
        }

        return $mime;
    }

    /**
     * Update favicon setting
     *
     * @param string|null $new
     * @param string|null $old
     * @return string|null
     */
    protected function updateFavicon( $new, $old )
    {
        $this->removeFile( $old );
        $new = $this->addFile( $new, 'settings/favicon.%2$s' );
        $this->setSetting( 'faviconType', $this->getMime( $new ) );
        return $new;
    }

    /**
     * Update logo setting
     *
     * @param string|null $new
     * @param string|null $old
     * @return string|null
     */
    protected function updateLogo( $new, $old )
    {
        $this->removeFile( $old );
        $new = $this->addFile( $new, 'settings/logo.%2$s' );
        $this->setSetting( 'logoType', $this->getMime( $new ) );
        return $new;
    }

}
