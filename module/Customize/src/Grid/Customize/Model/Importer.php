<?php

namespace Grid\Customize\Model;

use ZipArchive;
use DOMElement;
use DOMDocument;
use Zork\Db\SiteInfo;
use Zork\Stdlib\DateTime;
use Zork\Libxml\ErrorHandler;
use Grid\Customize\Model\Sheet\Model as SheetModel;
use Grid\User\Model\Permissions\Model as PermissionsModel;
use Grid\Paragraph\Model\Paragraph\Model as ParagraphModel;

/**
 * Importer model
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Importer extends AbstractImportExport
{

    /**
     * @var PermissionsModel
     */
    protected $permissionsModel;

    /**
     * @return  PermissionsModel
     */
    public function getPermissionsModel()
    {
        return $this->permissionsModel;
    }

    /**
     * @param   PermissionsModel    $permissionsModel
     * @return  Importer
     */
    public function setPermissionsModel(PermissionsModel $permissionsModel)
    {
        $this->permissionsModel = $permissionsModel;
        return $this;
    }

    /**
     * Constructor
     *
     * @param   SheetModel          $sheetModel
     * @param   ParagraphModel      $paragraphModel
     * @param   SiteInfo            $siteInfo
     * @param   PermissionsModel    $permissionsModel
     */
    public function __construct( SheetModel         $sheetModel,
                                 ParagraphModel     $paragraphModel,
                                 SiteInfo           $siteInfo,
                                 PermissionsModel   $permissionsModel )
    {
        parent::__construct( $sheetModel, $paragraphModel, $siteInfo );
        $this->setPermissionsModel( $permissionsModel );
    }

    /**
     * Import paragraph & customize from a zip file
     *
     * @param   string|ZipArchive   $file
     * @param   string|null         $basename
     * @return  ImportResult
     */
    public function import( $file, $basename = null )
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
            $zip = $file;

            if ( $basename )
            {
                $file = $basename;
            }
            else
            {
                $basename = $file = '$object.zip';
            }
        }
        else
        {
            if ( ! $basename )
            {
                $basename = basename( $file );
            }

            if ( ! is_file( $file ) )
            {
                return new ImportResult(
                    ImportResult::FILE_NOT_EXISTS,
                    sprintf(
                        '"%s" is not a file',
                        $basename
                    )
                );
            }

            $zip    = new ZipArchive();
            $open   = $zip->open( $file, ZipArchive::CHECKCONS );

            if ( $open !== true )
            {
                return new ImportResult(
                    ImportResult::FILE_NOT_ZIP,
                    sprintf(
                        '"%s" cannot be opened as a zip file (error #%d)',
                        $basename,
                        $open
                    )
                );
            }
        }

        $stats = $zip->statName( 'paragraph.xml', ZipArchive::FL_NOCASE );

        if ( empty( $stats ) ||
             empty( $stats['size'] ) ||
             ! isset( $stats['index'] ) )
        {
            return new ImportResult(
                ImportResult::STRUCTURE_XML_NOT_FOUND,
                sprintf(
                    '"paragraph.xml" is not found in "%s"',
                    $file
                )
            );
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

                return new ImportResult(
                    ImportResult::STRUCTURE_XML_DOCTYPE_MISMATCH,
                    sprintf(
                        'DOCTYPE "%s" in "%s#paragraph.xml" does not match "%s"',
                        $doctype->name,
                        $basename,
                        static::GPML_ROOT
                    )
                );
            }

            if ( ! in_array( $doctype->systemId, $validSystemIds ) )
            {
                ErrorHandler::stop();

                return new ImportResult(
                    ImportResult::STRUCTURE_XML_DOCTYPE_MISMATCH,
                    sprintf(
                        'SYSTEM ID "%s" in "%s#paragraph.xml"\'s ' .
                            'DOCTYPE does not match one of "%s"',
                        $doctype->systemId,
                        $basename,
                        implode( '", "', $validSystemIds )
                    )
                );
            }

            if ( ! $document->validate() )
            {
                $error = ErrorHandler::stop();

                return new ImportResult(
                    ImportResult::STRUCTURE_XML_NOT_VALID,
                    $error ? $error->getMessage() : null
                );
            }
        }

        if ( ! $document->schemaValidate( realpath( $validSchema ) ) )
        {
            $error = ErrorHandler::stop();

            return new ImportResult(
                ImportResult::STRUCTURE_XML_NOT_VALID,
                $error ? $error->getMessage() : null
            );
        }

        $rootParagraphId    = null;
        $domains            = array();
        $paragraphIdMap     = array();
        $gpml               = $document->documentElement;
        $version            = $gpml->getAttribute( 'version' );
        $dbSchema           = $gpml->getAttribute( 'db-schema' );

        if ( version_compare( $version, '1.0', '<' ) )
        {
            ErrorHandler::stop();

            return new ImportResult(
                ImportResult::STRUCTURE_XML_UNKNOWN_VERSION,
                sprintf(
                    'unknown version "%s" in "%s#paragraph.xml"',
                    $version,
                    $basename
                )
            );
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
                        $type           = $child->getAttribute( 'type' );
                        $permissions    = $this->getPermissionsModel();

                        if ( ! $permissions->isAllowed( 'paragraph.' . $type, 'create' ) )
                        {
                            return new ImportResult(
                                ImportResult::STRUCTURE_TYPE_NOT_ALLOWED,
                                sprintf(
                                    'not allowed to create paragraph ' .
                                    '(type: "%s") from "%s#paragraph.xml"',
                                    $type,
                                    $basename
                                )
                            );
                        }

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

        $error = ErrorHandler::stop();

        if ( $error )
        {
            return new ImportResult(
                ImportResult::UNKNOWN_ERROR,
                $error->getMessage()
            );
        }

        return new ImportResult(
            ImportResult::SUCCESS,
            $rootParagraphId
        );
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
                            $zip,
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
        $paragraph  = $model->saveRawData( array(
            'type'      => $structure['type'],
            'name'      => $structure['name'],
            'left'      => $structure['left'],
            'right'     => $structure['right'],
            'rootId'    => $rootId,
        ) );

        if ( empty( $paragraph ) )
        {
            return null;
        }

        $paragraphIdMap[$structure['id']] = $paragraph;
        $model->saveRawProperties( $paragraph, $structure['properties'] );

        foreach ( $structure['children'] as $child )
        {
            $this->saveParagraphStructure(
                $child,
                $paragraphIdMap,
                $rootId ? $rootId : $paragraph
            );
        }

        return $paragraph;
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
                    $value = str_replace( $original, '/' . $url, $value );
                }
            }
        }

        foreach ( $domains as $domain )
        {
            if ( $domain === $siteInfo->getDomain() )
            {
                continue;
            }

            $value = preg_replace_callback(
                '((https?://)([^/:]+?\.)?'
                    . preg_quote( $domain )
                    . '(:\d+)?/?)i',
                function ( $matches ) use ( $siteInfo )
                {
                    return $siteInfo->getSubdomainUrl(
                        empty( $matches[2] ) ? '' : $matches[2],
                        true
                    );
                },
                $value
            );
        }

        return $value;
    }

}
