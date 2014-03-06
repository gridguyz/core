<?php

namespace Grid\Customize\Model;

use ZipArchive;
use DOMElement;
use DOMImplementation;
use Zork\Db\SiteInfo;
use Zork\Iterator\DepthList;
use Grid\Customize\Model\Sheet\Model as SheetModel;
use Grid\Paragraph\Model\Paragraph\Model as ParagraphModel;

/**
 * Exporter model
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Exporter extends AbstractImportExport
{

    /**
     * @var DomainList
     */
    private $domainList = null;

    /**
     * @var string
     */
    private $urlPattern = null;

    /**
     * Get the domain-list
     *
     * @return  DomainList
     */
    protected function getDomainList()
    {
        return $this->domainList;
    }

    /**
     * Set the domain-list
     *
     * @param   DomainList  $domainList
     * @return  Exporter
     */
    protected function setDomainList( DomainList $domainList )
    {
        $this->urlPattern = null;
        $this->domainList = $domainList;
        return $this;
    }

    /**
     * Get url pattern
     *
     * @return  string
     */
    protected function getUrlPattern()
    {
        if ( $this->urlPattern )
        {
            return $this->urlPattern;
        }

        $domains = iterator_to_array( $this->getDomainList() );

        if ( empty( $domains ) )
        {
            $domains = array( 'localhost' );
        }

        return $this->urlPattern = '(|(https?:)?//([^/]+\.)?('
             . implode( '|', array_map( 'preg_quote', $domains ) )
             . ')(:\d+)?)';
    }

    /**
     * Constructor
     *
     * @param   DomainList      $domainList
     * @param   SheetModel      $sheetModel
     * @param   ParagraphModel  $paragraphModel
     * @param   SiteInfo        $siteInfo
     */
    public function __construct( DomainList     $domainList,
                                 SheetModel     $sheetModel,
                                 ParagraphModel $paragraphModel,
                                 SiteInfo       $siteInfo )
    {
        parent::__construct( $sheetModel, $paragraphModel, $siteInfo );
        $this->setDomainList( $domainList );
    }

    /**
     * Set property-node(s)' value
     *
     * @param   ZipArchive  $zip
     * @param   DOMElement  $element
     * @param   string|null $value
     * @param   boolean     $css
     * @return  string|null
     */
    protected function setPropertyNodeValue( ZipArchive $zip,
                                             DOMElement $element,
                                             $value,
                                             $css = false )
    {
        if ( empty( $value ) )
        {
            return $value;
        }

        $matches    = array();
        $substits   = array();
        $public     = static::PUBLIC_DIR;
        $uploads    = static::UPLOADS_DIR;
        $value      = (string) $value;
        $pattprefix = $this->getUrlPattern();
        $pattern    = '(^' . $pattprefix . '(/'
                    . preg_quote( $uploads ) . '.*)$)i';

        if ( preg_match( $pattern, $value, $matches ) )
        {
            $path = $matches[6];
            $file = rtrim( $public, '/' ) . '/' . ltrim( $path, '/' );

            if ( file_exists( $file ) )
            {
                $value = $path;
                $substits[$path] = $file;
            }
        }

        $pattern = '(([\'"])(' . $pattprefix . '(/'
                 . preg_quote( $uploads ) . '.*?))(?<!\\\\)(\\1))i';

        if ( empty( $substits ) )
        {
            $replace = function ( $matches ) use ( &$substits, $css, $public ) {
                $path = $matches[8];

                if ( ! $css )
                {
                    $path = html_entity_decode( $path, ENT_QUOTES | ENT_HTML5 );
                }

                $path = rawurldecode( $path );
                $file = rtrim( $public, '/' ) . '/' . ltrim( $path, '/' );

                if ( file_exists( $file ) )
                {
                    $substits[$matches[8]] = $file;
                    return $matches[1] . $matches[8] . $matches[9];
                }

                return $matches[0];
            };

            $value = preg_replace_callback( $pattern, $replace, $value );

            if ( $css )
            {
                $pattern = '((url\\()(' . $pattprefix . '(/'
                         . preg_quote( $uploads ) . '.*?))(?<!\\\\)(\\)))i';
                $value   = preg_replace_callback( $pattern, $replace, $value );
            }
        }

        if ( ! empty( $substits ) )
        {
            $document = $element->ownerDocument;

            foreach ( $substits as $path => $file )
            {
                if ( $zip->addFile( $file, $file ) )
                {
                    $substitution = $document->createElement( 'substitution' );
                    $substitution->setAttribute( 'original', $path );
                    $substitution->setAttribute( 'file', $file );
                    $element->appendChild( $substitution );
                }
            }
        }

        return $value;
    }

    /**
     * Export paragraph's customize into a zip file
     *
     * @param   int                     $rootId
     * @return  ZipArchive|string|null  $zip
     * @return  ZipArchive
     */
    public function export( $rootId, $zip = null )
    {
        $rootId         = (int) $rootId;
        $info           = $this->getSiteInfo();
        $domainList     = $this->getDomainList();
        $sheetModel     = $this->getSheetModel();
        $paragraphModel = $this->getParagraphModel();
        $dom            = new DOMImplementation;
        $document       = $dom->createDocument(
            static::GPML_NAMESPACE,
            static::GPML_ROOT,
            $dom->createDocumentType(
                static::GPML_ROOT,
                static::GPML_PUBLIC,
                static::GPML_SYSTEM
            )
        );

        if ( null === $zip )
        {
            $suffix  = '';
            $zipPath = static::PUBLIC_DIR
                     . static::TEMP_DIR
                     . 'paragraph-export-'
                     . $rootId;

            while ( is_file( $zipPath . $suffix . '.zip' ) )
            {
                $suffix--;
            }

            $zip = $zipPath . $suffix . '.zip';
        }

        if ( ! $zip instanceof ZipArchive )
        {
            $zipPath = (string) $zip;
            $zip     = new ZipArchive;
            $open    = $zip->open(
                $zipPath,
                ZipArchive::CREATE | ZipArchive::OVERWRITE
            );

            if ( true !== $open )
            {
                throw new \RuntimeException( sprintf(
                    '%s: "%s" cannot be created as a zip file (errno #%d)',
                    __METHOD__,
                    $zipPath,
                    $open
                ) );
            }
        }

        $gpml = $document->documentElement;
     // $gpml->setAttribute( 'xmlns', 'http://gridguyz.com/#gpml' );
        $gpml->setAttribute( 'version', '1.0' );
        $gpml->setAttribute( 'db-schema', $info->getSchema() );

        foreach ( $domainList as $domain )
        {
            $domainTextNode = $document->createTextNode( (string) $domain );
            $domainElement  = $document->createElement( 'domain' );
            $domainElement->appendChild( $domainTextNode );
            $gpml->appendChild( $domainElement );
        }

        $nodeStack  = array( $gpml );
        $paragraphs = new DepthList(
            $paragraphModel->getMapper()
                           ->findRenderListData( $rootId )
        );

        $paragraphs->runin( function ( $paragraph ) use (
            &$nodeStack,
            $document,
            $zip
        ) {
            $nodeStack[] = $node = $document->createElement( 'paragraph' );
            $node->setAttribute( 'id', $paragraph['id'] );
            $node->setAttribute( 'type', $paragraph['type'] );

            if ( isset( $paragraph['name'] ) )
            {
                $node->setAttribute( 'name', $paragraph['name'] );
            }

            if ( ! empty( $paragraph['proxyData'] ) )
            {
                foreach ( $paragraph['proxyData'] as $property )
                {
                    $propNode = $document->createElement( 'paragraph-property' );

                    $propNode->setAttribute(
                        'locale',
                        empty( $property['locale'] )
                            ? '*'
                            : $property['locale']
                    );

                    $propNode->setAttribute( 'name', $property['name'] );

                    if ( isset( $property['value'] ) )
                    {
                        $propNode->setAttribute(
                            'value',
                            $this->setPropertyNodeValue(
                                $zip,
                                $propNode,
                                $property['value']
                            )
                        );
                    }

                    $node->appendChild( $propNode );
                }
            }
        }, function () use ( &$nodeStack ) {
            $append = array_pop( $nodeStack );
            end( $nodeStack )->appendChild( $append );
        } );

        /* @var $sheet \Grid\Customize\Model\Sheet\Structure */
        $sheet = $sheetModel->find( $rootId );

        foreach ( $sheet->rules as $rule )
        {
            /* @var $rule \Grid\Customize\Model\Rule\Structure */
            $ruleNode = $document->createElement( 'customize-rule' );
            $ruleNode->setAttribute( 'selector', $rule->selector );
            $ruleNode->setAttribute( 'media', $rule->media );

            foreach ( $rule->getRawPropertyNames() as $property )
            {
                $propNode = $document->createElement( 'customize-property' );
                $priority = $rule->getRawPropertyPriority( $property );
                $propNode->setAttribute( 'name', $property );

                $propNode->setAttribute(
                    'value',
                    $this->setPropertyNodeValue(
                        $zip,
                        $propNode,
                        $rule->getRawPropertyValue( $property ),
                        true
                    )
                );

                if ( $priority )
                {
                    $propNode->setAttribute( 'priority', $priority );
                }
            }

            $gpml->appendChild( $ruleNode );
        }

        $extra = trim( $sheet->getExtraContent() );

        if ( ! empty( $extra ) )
        {
            $extraText = $document->createTextNode( "\n$extra\n" );
            $extraNode = $document->createElement( 'customize-extra' );
            $extraNode->appendChild( $extraText );
            $gpml->appendChild( $extraNode );
        }

        $zip->addFromString( 'paragraph.xml', $document->saveXML() );
        return $zip;
    }

}
