<?php

namespace Grid\Customize\Model;

use ZipArchive;
use Zork\Db\SiteInfo;
use Zork\Stdlib\String;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Zork\Db\SiteInfoAwareTrait;
use Zork\Db\SiteInfoAwareInterface;
use Grid\Customize\Model\Sheet\Model as SheetModel;

/**
 * Importer model
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Importer implements SiteInfoAwareInterface
{

    use SiteInfoAwareTrait;

    /**
     * @const string
     */
    const PUBLIC_DIR    = './public/';

    /**
     * @const string
     */
    const UPLOADS_DIR   = 'uploads/';

    /**
     * @var string
     */
    const IMPORT_DIR    = 'tmp/';

    /**
     * @var \Customize\Model\Sheet\Model
     */
    private $sheetModel = null;

    /**
     * @var \Customize\Model\CssParser
     */
    private $cssParser = null;

    /**
     * Get the customize-sheet model
     *
     * @return \Customize\Model\Sheet\Model
     */
    protected function getSheetModel()
    {
        return $this->sheetModel;
    }

    /**
     * Set the customize-sheet model
     *
     * @param \Customize\Model\Sheet\Model $sheet
     * @return \Customize\Model\Exporter
     */
    protected function setSheetModel( SheetModel $sheet )
    {
        $this->sheetModel = $sheet;
        return $this;
    }

    /**
     * Get the css-parser
     *
     * @return \Customize\Model\CssParser
     */
    protected function getCssParser()
    {
        return $this->cssParser;
    }

    /**
     * Set the css-parser
     *
     * @param \Customize\Model\CssParser $parser
     * @return \Customize\Model\Exporter
     */
    protected function setCssParser( CssParser $parser )
    {
        $this->cssParser = $parser;
        return $this;
    }

    /**
     * Constructor
     *
     * @param \Customize\Model\Sheet\Model $sheet
     * @param \Customize\Model\CssParser $cssParser
     * @param \Zork\Db\SiteInfo $siteInfo
     */
    public function __construct( SheetModel $sheet,
                                 CssParser $cssParser,
                                 SiteInfo $siteInfo )
    {
        $this->setSheetModel( $sheet )
             ->setCssParser( $cssParser )
             ->setSiteInfo( $siteInfo );
    }

    /**
     * Generate a random import-name
     *
     * @return string
     */
    protected function generateImportName()
    {
        do
        {
            $name = 'import-' . String::generateRandom();
            $path = static::PUBLIC_DIR . static::IMPORT_DIR . '/' . $name;
        }
        while ( is_file( $path ) || is_dir( $path ) );

        return $name;
    }

    /**
     * Parse css-file into rules
     *
     * @param string $file
     * @param array $rules
     */
    protected function parseCss( $file, array & $rules )
    {
        $dir   = dirname( $file );
        $sheet = $this->getCssParser()
                      ->parse( $file );

        if ( ! empty( $sheet ) )
        {
            foreach ( $sheet->imports as $import )
            {
                $this->parseCss( $dir . '/' . $import, $rules );
            }

            $rules = array_merge( $rules, $sheet->rules );
        }
    }

    /**
     * Add a css file's content to the db (under rootId in hierarchy)
     *
     * @param string $dir
     * @param string $file
     * @param int|null $rootId
     */
    protected function addCssByRoot( $dir, $file, $rootId )
    {
        $rows = 0;
        $path = $dir . '/' . $file;

        if ( is_file( $path ) )
        {
            $this->getSheetModel()
                 ->deleteByRoot( $rootId );

            /* @var $rules Grid\Customize\Model\Rule\Structure[] */
            $rules = array();
            $this->parseCss( $path, $rules );

            $mapper = $this->getSheetModel()
                           ->getMapper();

            foreach ( $rules as $rule )
            {
                $rows += $mapper->save( $rule );
            }
        }

        return $rows;
    }

    /**
     * Remove contents
     *
     * @param string $path
     * @param bool $resursive
     */
    protected function removeContents( $path, $resursive = false )
    {
        if ( file_exists( $path ) )
        {
            if ( $resursive )
            {
                $iterator = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator(
                        $path,
                        RecursiveDirectoryIterator::KEY_AS_PATHNAME |
                        RecursiveDirectoryIterator::CURRENT_AS_SELF |
                        RecursiveDirectoryIterator::SKIP_DOTS
                    ),
                    RecursiveIteratorIterator::CHILD_FIRST
                );

                foreach ( $iterator as $key => $self )
                {
                    if ( $self->isDir() )
                    {
                        @ rmdir( $key );
                    }
                    else
                    {
                        @ unlink( $key );
                    }
                }
            }
            else
            {
                @ unlink( $path );
            }
        }
    }

    /**
     * Add file/dir contents to customize path
     *
     * @param string $dir
     * @param string $file
     * @param bool $resursive set to true on dirs
     */
    protected function moveContents( $dir, $file, $resursive = false )
    {
        static $schema = null;
        $path = $dir . '/' . $file;

        if ( file_exists( $path ) )
        {
            if ( null === $schema )
            {
                $info   = $this->getSiteInfo();
                $schema = $info->getSchema();
            }

            $to = static::PUBLIC_DIR . static::UPLOADS_DIR . $schema . '/customize/' . $file;
            $this->removeContents( $to, $resursive );

            if ( $resursive )
            {
                $iterator = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator(
                        $path,
                        RecursiveDirectoryIterator::KEY_AS_PATHNAME |
                        RecursiveDirectoryIterator::CURRENT_AS_SELF |
                        RecursiveDirectoryIterator::SKIP_DOTS
                    ),
                    RecursiveIteratorIterator::SELF_FIRST
                );

                foreach ( $iterator as $key => $self )
                {
                    if ( $self->isDir() )
                    {
                        @ mkdir( $key, 0777, true );
                    }
                    else
                    {
                        @ copy( $key, $to . '/' . $self->getSubPathname() );
                    }
                }
            }
            else
            {
                @ copy( $path, $to );
            }
        }
    }

    /**
     * Import paragraph's customize from a zip file
     *
     * @param string $file
     * @param int $paragraphId
     * @return string
     */
    public function import( $file, $paragraphId )
    {
        if ( ! is_file( $file ) )
        {
            throw new \InvalidArgumentException( sprintf(
                '%s: "%s" is not a file',
                __METHOD__,
                $file
            ) );
        }

        $zip = new ZipArchive();

        if ( $zip->open( $file, ZipArchive::CHECKCONS ) !== true )
        {
            throw new \RuntimeException( sprintf(
                '%s: "%s" cannot be opened as a zip file',
                __METHOD__,
                $file
            ) );
        }

        $dir = static::PUBLIC_DIR . static::IMPORT_DIR . $this->generateImportName();

        if ( ! @ mkdir( $dir, 0777, true ) )
        {
            throw new \RuntimeException( sprintf(
                '%s: Temp dir "%s" cannot be created',
                __METHOD__,
                $dir
            ) );
        }

        $extract = $zip->extractTo( $dir );
        $zip->close();

        if ( true !== $extract )
        {
            throw new \RuntimeException( sprintf(
                '%s: Zip file "%s" cannot be extracted',
                __METHOD__,
                $file
            ) );
        }

        $this->addCssByRoot( $dir, 'layout.css', $paragraphId );
        $this->addCssByRoot( $dir, 'general.css', null );
        $this->moveContents( $dir, 'extra.css', false );
        $this->moveContents( $dir, 'resources', true );
        $this->removeContents( $dir, true );

        return true;
    }

}
