<?php

namespace Grid\Customize\Model;

use ZipArchive;
use Zork\Db\SiteInfo;
use Zork\Stdlib\String;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Zork\Db\SiteInfoAwareTrait;
use Zork\Db\SiteInfoAwareInterface;
use Zend\Http\Client as HttpClient;
use Zend\Http\Request as HttpRequest;
use Grid\Customize\Model\Sheet\Model as SheetModel;

/**
 * Exporter model
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Exporter implements SiteInfoAwareInterface
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
     * @const string
     */
    const EXPORT_DIR    = 'tmp/';

    /**
     * @var \Customize\Model\Sheet\Model
     */
    private $sheetModel = null;

    /**
     * @var \Zend\Http\Client
     */
    private $httpClient = null;

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
     * Get the http-client
     *
     * @return \Zend\Http\Client
     */
    protected function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * Set the http-client
     *
     * @param \Zend\Http\Client $client
     * @return \Customize\Model\Exporter
     */
    protected function setHttpClient( HttpClient $client )
    {
        $this->httpClient = $client;
        return $this;
    }

    /**
     * Constructor
     *
     * @param \Customize\Model\Sheet\Model $sheet
     * @param \Zend\Http\Client $client
     * @param \Zork\Db\SiteInfo $siteInfo
     */
    public function __construct( SheetModel $sheet,
                                 HttpClient $client,
                                 SiteInfo $siteInfo )
    {
        $this->setSheetModel( $sheet )
             ->setHttpClient( $client )
             ->setSiteInfo( $siteInfo );
    }

    /**
     * Generate a random export-name
     *
     * @return string
     */
    protected function generateExportName( $suffix )
    {
        do
        {
            $name = 'export-' . String::generateRandom();
            $path = static::PUBLIC_DIR . static::EXPORT_DIR . $name . $suffix;
        }
        while ( is_file( $path ) || is_dir( $path ) );

        return $name . $suffix;
    }

    /**
     * HTTP get an uri (at the actual domain)
     *
     * @param string $domain
     * @param string $path
     * @param string $schema
     * @return string
     */
    protected function httpGet( $domain, $path, $schema = 'http://' )
    {
        $request = new HttpRequest;
        $request->setMethod( HttpRequest::METHOD_GET )
                ->setUri( $schema . $domain . '/' . ltrim( $path, '/' ) )
                ->getHeaders()
                ->addHeaderLine( 'Host', $domain );

        return $this->getHttpClient()
                    ->send( $request )
                    ->getContent();
    }

    /**
     * Add a css file by root-id to the zip-archive
     *
     * @param ZipArchive $zip
     * @param type $name
     * @param type $rootId
     * @param bool $convertUrls
     */
    protected function addCssByRoot( ZipArchive $zip, $name, $rootId, $convertUrls = true )
    {
        $tmpp   = static::PUBLIC_DIR . static::EXPORT_DIR . $this->generateExportName( '.css' );
        $added  = false;

        $this->getSheetModel()
             ->findByRoot( $rootId )
             ->render( $tmpp );

        $tmp = file_get_contents( $tmpp );

        if ( $convertUrls )
        {
            $tmp = preg_replace_callback(
                '#url\("?(/uploads/[^/]+/([^/]+)/.*?)"?\)#',
                function ( $match ) use ( $zip, $added )
                {
                    $file = static::PUBLIC_DIR . $match[1];

                    if ( $match[2] != 'customize' && is_file( $file ) )
                    {
                        $prefix = 'resources/uploads/';
                        $suffix = strrchr( $file, '.' );
                        $base = basename( $file, $suffix );
                        $index = '';

                        while ( false !== $zip->locateName( $prefix .
                                $base . $index . $suffix ) )
                        {
                            --$index;
                        }

                        if ( $zip->addFile( $file, $prefix .
                                $base . $index . $suffix ) )
                        {
                            $added = true;
                            return 'url("./' . $prefix .
                                    $base . $index . $suffix . '")';
                        }
                    }

                    return $match[0];
                },
                $tmp
            );
        }

        $zip->addFromString( $name, $tmp );
        @ unlink( $tmpp );
        return $added;
    }

    /**
     * Export paragraph's customize into a zip file
     *
     * @param string $url
     * @param int $paragraphId
     * @param int $contentId
     * @return string
     */
    public function export( $url, $paragraphId, $contentId = null )
    {
        $info   = $this->getSiteInfo();
        $domain = $info->getFulldomain();
        $zipp   = static::PUBLIC_DIR . static::EXPORT_DIR . $this->generateExportName( '.zip' );
        $upl    = static::PUBLIC_DIR . static::UPLOADS_DIR . $info->getSchema() . '/customize';
        $zip    = new ZipArchive();
        $added  = false;

        if ( $zip->open( $zipp,
                         ZipArchive::CREATE | ZipArchive::OVERWRITE ) !== true )
        {
            throw new \RuntimeException( sprintf(
                '%s: Zip file "%s" cannot be created',
                __METHOD__,
                $zipp
            ) );
        }

        if ( is_dir( $upl . '/resources' ) )
        {
            $iterator = new RecursiveDirectoryIterator(
                $upl . '/resources',
                RecursiveDirectoryIterator::KEY_AS_PATHNAME |
                RecursiveDirectoryIterator::CURRENT_AS_SELF |
                RecursiveDirectoryIterator::SKIP_DOTS
            );

            $iterator = new RecursiveIteratorIterator(
                $iterator,
                RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ( $iterator as $path => $self )
            {
                $zip->addFile( $path, 'resources/' . ltrim(
                    str_replace( '\\', '/', $self->getSubPathname() ), '/'
                ) );

                $resAdded = true;
            }
        }

        $added = $this->addCssByRoot( $zip, 'general.css', null ) || $added;
        $added = $this->addCssByRoot( $zip, 'layout.css', $paragraphId ) || $added;

        if ( ! empty( $contentId ) )
        {
            $added = $this->addCssByRoot( $zip, 'content.css', $contentId, false ) || $added;
        }

        if ( ! $added )
        {
            $zip->addEmptyDir( 'resources' );
        }

        if ( is_file( $upl . '/extra.css' ) )
        {
            $zip->addFile( $upl . '/extra.css', 'extra.css' );
        }
        else
        {
            $zip->addFromString( 'extra.css', '@charset "utf-8";' . PHP_EOL . PHP_EOL );
        }

        $zip->addFromString(
            'custom.css',
            '@charset "utf-8";' . PHP_EOL . PHP_EOL .
            '@import url("./extra.css");' . PHP_EOL .
            '@import url("./general.css");' . PHP_EOL .
            '@import url("./layout.css");' . PHP_EOL .
            ( empty( $contentId ) ? '' : '@import url("./content.css");' . PHP_EOL )
        );

        $zip->addFromString(
            'index.html',
            preg_replace(
                array(
                    '#<head(\s[^>]*)?>#',
                    '#\s*<link\s[^>]*/customize/custom.[A-Za-z0-9]+.css[^>]*>#',
                ),
                array(
                    '\\0' . PHP_EOL .
                    '    <link href="./custom.css" type="text/css" ' .
                              'rel="stylesheet" />' . PHP_EOL .
                    '    <base href="http://' . $domain . '/" />',
                    '',
                ),
                $this->httpGet( $domain, $url )
            )
        );

        $zip->close();
        return $zipp;
    }

}
