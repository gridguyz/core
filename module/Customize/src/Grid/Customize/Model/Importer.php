<?php

namespace Grid\Customize\Model;

use ZipArchive;
use DOMElement;
use DOMDocument;
use Zork\Db\SiteInfo;
use Zork\Stdlib\DateTime;
use Zork\Libxml\ErrorHandler;
use Grid\Customize\Model\Sheet\Model as SheetModel;
use Grid\Paragraph\Model\Paragraph\Model as ParagraphModel;

/**
 * Importer model
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Importer extends AbstractImportExport
{

    /**
     * Constructor
     *
     * @param   SheetModel      $sheetModel
     * @param   ParagraphModel  $paragraphModel
     * @param   SiteInfo        $siteInfo
     */
    public function __construct( SheetModel     $sheetModel,
                                 ParagraphModel $paragraphModel,
                                 SiteInfo       $siteInfo )
    {
        parent::__construct( $sheetModel, $paragraphModel, $siteInfo );
    }

    /**
     * Import paragraph & customize from a zip file
     *
     * @param   string|ZipArchive   $file
     * @param   boolean             $throw
     * @return  int|\ErrorException
     */
    public function import( $file, $throw = true )
    {
        static $validSchema     = 'vendor/gridguyz/core/module/Core/public/styles/schemas/paragraph/1.0.xsd';
        static $validSystemIds  = array(
            'http://gridguyz.com/styles/schemas/paragraph/1.0.dtd',
            'public/styles/schemas/paragraph/1.0.dtd',
            './public/styles/schemas/paragraph/1.0.dtd',
            'vendor/gridguyz/core/module/Core/public/styles/schemas/paragraph/1.0.dtd',
            './vendor/gridguyz/core/module/Core/public/styles/schemas/paragraph/1.0.dtd',
        );

        if ( $file instanceof ZipArchive )
        {
            $zip    = $file;
            $file   = '$object.zip';
        }
        else
        {
            if ( ! is_file( $file ) )
            {
                throw new \InvalidArgumentException( sprintf(
                    '%s: "%s" is not a file',
                    __METHOD__,
                    $file
                ) );
            }

            $zip    = new ZipArchive();
            $open   = $zip->open( $file, ZipArchive::CHECKCONS );

            if ( $open !== true )
            {
                throw new \RuntimeException( sprintf(
                    '%s: "%s" cannot be opened as a zip file (errno #%d)',
                    __METHOD__,
                    $file,
                    $open
                ) );
            }
        }

        $stats = $zip->statName( 'paragraph.xml', ZipArchive::FL_NOCASE );

        if ( empty( $stats ) ||
             empty( $stats['size'] ) ||
             ! isset( $stats['index'] ) )
        {
            throw new \InvalidArgumentException( sprintf(
                '%s: "paragraph.xml" is not found in "%s"',
                __METHOD__,
                $file
            ) );
        }

        $document = new DOMDocument();
        $document->loadXML( $zip->getFromIndex( $stats['index'] ) );
        $document->documentURI  = 'paragraph.xml';
        $document->normalizeDocument();
        ErrorHandler::start();

        if ( $document->doctype )
        {
            $doctype = $document->doctype;

            if ( static::GPML_ROOT != strtolower( $doctype->name ) )
            {
                ErrorHandler::stop();
                throw new \InvalidArgumentException( sprintf(
                    '%s: DOCTYPE "%s" in "%s#paragraph.xml" does not match "%s"',
                    __METHOD__,
                    $doctype->name,
                    $file,
                    static::GPML_ROOT
                ) );
            }

            if ( ! in_array( $doctype->systemId, $validSystemIds ) )
            {
                ErrorHandler::stop();
                throw new \InvalidArgumentException( sprintf(
                    '%s: SYSTEM ID "%s" in "%s#paragraph.xml"\'s ' .
                        'DOCTYPE does not match one of "%s"',
                    __METHOD__,
                    $doctype->systemId,
                    $file,
                    implode( '", "', $validSystemIds )
                ) );
            }

            if ( ! $document->validate() )
            {
                return ErrorHandler::stop( $throw );
            }
        }

        if ( ! $document->schemaValidate( realpath( $validSchema ) ) )
        {
            return ErrorHandler::stop( $throw );
        }

        $domains        = array();
        $paragraphIdMap = array();
        $gpml           = $document->documentElement;
        $version        = $gpml->getAttribute( 'version' );
        $dbSchema       = $gpml->getAttribute( 'db-schema' );

        if ( version_compare( $version, '1.0', '<' ) )
        {
            ErrorHandler::stop();
            throw new \InvalidArgumentException( sprintf(
                '%s: unknown version "%s" in "%s#paragraph.xml"',
                __METHOD__,
                $version,
                $file
            ) );
        }

        foreach ( $gpml->childNodes as $child )
        {
            if ( $child instanceof DOMElement )
            {
                switch ( $child->tagName )
                {
                    case 'domain':
                        $domains[] = trim( $child->textContent );
                        break;

                    case 'paragraph':
                        $rootParagraphId = $this->importRootParagraph(
                            $child,
                            $zip,
                            $paragraphIdMap,
                            $domains,
                            $dbSchema
                        );
                        break;

                    case 'customize-rule':
                        $this->importCustomizeRule(
                            $rootParagraphId,
                            $child,
                            $zip,
                            $paragraphIdMap,
                            $domains,
                            $dbSchema
                        );
                        break;

                    case 'customize-extra':
                        $this->importCustomizeExtra(
                            $rootParagraphId,
                            trim( $child->textContent )
                        );
                        break;
                }
            }
        }

        $error = ErrorHandler::stop( false );

        if ( $error )
        {
            if ( $throw )
            {
                throw $error;
            }
            else
            {
                return $error;
            }
        }

        return $rootParagraphId;
    }

    /**
     * Import root-paragraph from its node
     *
     * @param   DOMElement  $paragraphNode
     * @param   ZipArchive  $zip
     * @param   array       $paragraphIdMap
     * @param   array       $domains
     * @param   string      $dbSchema
     * @return  int
     */
    protected function importRootParagraph( DOMElement $paragraphNode,
                                            ZipArchive $zip,
                                            array &$paragraphIdMap,
                                            array $domains,
                                            $dbSchema )
    {
        return $this->saveParagraphStructure(
            $this->loadParagraphStructure(
                $paragraphNode,
                $zip,
                $domains,
                $dbSchema
            ),
            $paragraphIdMap
        );
    }

    /**
     * Load paragraph structure from its node
     *
     * @param   DOMElement  $paragraphNode
     * @param   ZipArchive  $zip
     * @param   array       $domains
     * @param   string      $dbSchema
     * @param   int         $offset
     * @return  array
     */
    protected function loadParagraphStructure( DOMElement $paragraphNode,
                                               ZipArchive $zip,
                                               array $domains,
                                               $dbSchema,
                                               &$offset = 1)
    {
        $offset     = ( (int) $offset ) ?: 1;
        $structure  = array(
            'id'            => $paragraphNode->getAttribute( 'id' ),
            'type'          => $paragraphNode->getAttribute( 'type' ),
            'name'          => $paragraphNode->hasAttribute( 'name' )
                             ? $paragraphNode->getAttribute( 'name' )
                             : null,
            'left'          => $offset,
            'right'         => ++$offset,
            'properties'    => array(),
            'children'      => array(),
        );

        foreach ( $paragraphNode->childNodes as $child )
        {
            if ( $child instanceof DOMElement )
            {
                switch ( $child->tagName )
                {
                    case 'paragraph':
                        $structure['children'][] = $this->loadParagraphStructure(
                            $child,
                            $domains,
                            $dbSchema,
                            $offset
                        );

                        $structure['right'] = ++$offset;
                        break;

                    case 'paragraph-property':
                        $substitutions = array();

                        foreach ( $child->childNodes as $substitutionNode )
                        {
                            if ( $substitutionNode instanceof DOMElement &&
                                 $substitutionNode->tagName === 'substitution' )
                            {
                                $name   = $substitutionNode->getAttribute( 'original' );
                                $value  = $substitutionNode->getAttribute( 'file' );
                                $substitutions[$name] = $value;
                            }
                        }

                        $value = $this->processValue(
                            $child->getAttribute( 'value' ),
                            $zip,
                            $substitutions,
                            $domains,
                            $dbSchema
                        );

                        $structure['properties'][] = array(
                            'name'      => $child->getAttribute( 'name' ),
                            'locale'    => ( $child->getAttribute( 'locale' ) ?: '*' ),
                            'value'     => $value,
                        );
                        break;
                }
            }
        }

        return $structure;
    }

    /**
     * Save paragraph structure to database
     *
     * @param   array   $structure
     * @param   array   $paragraphIdMap
     * @return  int
     */
    protected function saveParagraphStructure( array $structure,
                                               array &$paragraphIdMap,
                                               $rootId = null )
    {
        $model      = $this->getParagraphModel();
        $paragraph  = $model->create( array(
            'type'      => $structure['type'],
            'name'      => $structure['name'],
            'left'      => $structure['left'],
            'right'     => $structure['right'],
            'rootId'    => $rootId,
        ) );

        if ( $paragraph->save() && isset( $paragraph->id ) )
        {
            $id = $paragraph->id;
        }
        else
        {
            return null;
        }

        $paragraphIdMap[$structure['id']] = $id;
        $model->saveRawProperties( $id, $structure['properties'] );

        foreach ( $structure['children'] as $child )
        {
            $this->saveParagraphStructure( $child, $paragraphIdMap, $id );
        }

        return $id;
    }

    /**
     * Import customize-rule from its node
     *
     * @param   int         $rootParagraphId
     * @param   DOMElement  $ruleNode
     * @param   ZipArchive  $zip
     * @param   array       $paragraphIdMap
     * @param   array       $domains
     * @param   string      $dbSchema
     * @return  int
     */
    protected function importCustomizeRule( $rootParagraphId,
                                            DOMElement $ruleNode,
                                            ZipArchive $zip,
                                            array $paragraphIdMap,
                                            array $domains,
                                            $dbSchema )
    {
        /* @var $mapper \Grid\Customize\Model\Rule\Mapper */
        $properties = array();
        $model      = $this->getSheetModel();
        $mapper     = $model->getMapper()->getRuleMapper();
        $media      = $ruleNode->getAttribute( 'media' ) ?: '';
        $selector   = $this->processCustomizeRuleSelector(
            $ruleNode->getAttribute( 'selector' ),
            $paragraphIdMap
        );

        foreach ( $ruleNode->childNodes as $child )
        {
            if ( $child instanceof DOMElement &&
                 $child->tagName === 'customize-property' )
            {
                $substitutions = array();

                foreach ( $child->childNodes as $substitutionNode )
                {
                    if ( $substitutionNode instanceof DOMElement &&
                         $substitutionNode->tagName === 'substitution' )
                    {
                        $name   = $substitutionNode->getAttribute( 'original' );
                        $value  = $substitutionNode->getAttribute( 'file' );
                        $substitutions[$name] = $value;
                    }
                }

                $value = $this->processValue(
                    $child->getAttribute( 'value' ),
                    $zip,
                    $substitutions,
                    $domains,
                    $dbSchema,
                    $paragraphIdMap
                );

                $properties[] = array(
                    'name'      => $child->getAttribute( 'name' ),
                    'value'     => $value,
                    'priority'  => $child->hasAttribute( 'priority' )
                                 ? $child->getAttribute( 'priority' )
                                 : null,
                );
            }
        }

        $rule = $mapper->create( array(
            'selector'          => $selector,
            'media'             => $media,
            'rawProperties'     => $properties,
            'rootParagraphId'   => $rootParagraphId,
        ) );

        return $rule->save();
    }

    /**
     * Import customize-extra from its content
     *
     * @param   int     $rootParagraphId
     * @param   string  $extra
     * @return  int
     */
    protected function importCustomizeExtra( $rootParagraphId, $extra )
    {
        /* @var $mapper \Grid\Customize\Model\Extra\Mapper */
        $model  = $this->getSheetModel();
        $mapper = $model->getMapper()->getExtraMapper();
        $extra  = $mapper->create( array(
            'rootParagraphId'   => $rootParagraphId,
            'extra'             => $extra,
        ) );

        return $extra->save();
    }

    /**
     * Process a customize-rule's selector
     *
     * @param   string  $selector
     * @param   array   $paragraphIdMap
     * @return  string
     */
    public function processCustomizeRuleSelector( $selector,
                                                  array $paragraphIdMap )
    {
        return preg_replace_callback(
            '/#paragraph-(-?\d+)/',
            function ( $matches ) use ( $paragraphIdMap )
            {
                $id = (string) (int) $matches[1];

                if ( isset( $paragraphIdMap[$id] ) )
                {
                    $id = $paragraphIdMap[$id];
                }

                return '#paragraph-' . $id;
            },
            $selector
        );
    }

    /**
     * Process a single value
     *
     * @param   string      $value
     * @param   ZipArchive  $zip
     * @param   array       $substitutions
     * @param   array       $domains
     * @param   string      $dbSchema
     * @param   array|null  $paragraphIdMap
     * @return  string
     */
    public function processValue( $value,
                                  ZipArchive $zip,
                                  array $substitutions,
                                  array $domains,
                                  $dbSchema,
                                  array $paragraphIdMap = null)
    {
        if ( empty( $substitutions ) )
        {
            return $value;
        }

        $base       = static::PUBLIC_DIR . static::UPLOADS_DIR;
        $siteInfo   = $this->getSiteInfo();
        $importDir  = null;

        foreach ( $substitutions as $original => $file )
        {
            $stats = $zip->statName( $file, ZipArchive::FL_NOCASE );

            if ( ! empty( $stats ) && isset( $stats['index'] ) )
            {
                $contents   = null;
                $file       = trim( $stats['name'], '/' );

                if ( $siteInfo->getSchema() == $dbSchema &&
                     substr( $file, 0, strlen( $base ) ) == $base &&
                     is_file( $file ) &&
                     filesize( $file ) == $stats['size']  )
                {
                    $contents = $zip->getFromIndex( $stats['index'] );

                    if ( file_get_contents( $file ) == $contents )
                    {
                        $contents = null;
                        continue;
                    }
                }

                if ( null === $importDir )
                {
                    $importDir = static::UPLOADS_DIR
                               . $siteInfo->getSchema()
                               . '/pages/imports/'
                               . (new DateTime)->toHash() . '/';

                    if ( ! is_dir( static::PUBLIC_DIR . $importDir ) )
                    {
                        @mkdir( static::PUBLIC_DIR . $importDir, 0777, true );
                    }
                }

                $info   = pathinfo( $file );
                $name   = str_replace( '%', '.', rawurlencode( $info['filename'] ) );
                $ext    = '';
                $suffix = '';

                if ( isset( $info['extension'] ) )
                {
                    $ext = '.' . str_replace( '%', '.', rawurlencode( $info['extension'] ) );
                }

                while ( is_file( static::PUBLIC_DIR . $importDir . $name . $suffix . $ext ) )
                {
                    --$suffix;
                }

                $url    = $importDir . $name . $suffix . $ext;
                $path   = static::PUBLIC_DIR . $url;

                if ( $contents )
                {
                    $write = false !== file_put_contents( $path, $contents );
                }
                else if ( is_resource( $source = $zip->getStream( $stats['name'] ) ) )
                {
                    $destination = @fopen( $path, 'w' );

                    if ( is_resource( $destination ) )
                    {
                        $write = stream_copy_to_stream( $source, $destination );
                        fclose( $destination );
                    }
                    else
                    {
                        $write = false;
                    }

                    fclose( $source );
                }
                else
                {
                    $write = false;
                }

                if ( $write )
                {
                    $value = str_replace( $original, $url, $value );
                }
            }
        }

        foreach ( $domains as $domain )
        {
            $value = preg_replace(
                '((https?://)([^/:]+?\.)?'
                    . preg_quote( $domain )
                    . '(:\d+)?/?)i',
                function ( $matches ) use ( $siteInfo )
                {
                    return $siteInfo->getSubdomainUrl(
                        empty( $matches[2] ) ? '' : $matches[2],
                        true
                    );
                }
            );
        }

        return $value;
    }

}
