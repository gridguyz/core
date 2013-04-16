<?php

namespace Grid\Mail\Model\Template;

use Traversable;
use Zork\Db\SiteInfo;
use Zork\Stdlib\String;
use Zork\Model\Exception;
use Zend\Stdlib\ArrayUtils;
use Zork\Db\SiteInfoAwareTrait;
use Zork\Db\SiteInfoAwareInterface;
use Zork\Mail\Service as MailService;

/**
 * Sendable
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Sendable implements SiteInfoAwareInterface
{

    use SiteInfoAwareTrait;

    /**
     * @var \Zork\Mail\Service
     */
    protected $service;

    /**
     * @var array
     */
    protected $options  = array();

    /**
     * @var string
     */
    protected $templateHtml = '';

    /**
     * @var string
     */
    protected $templateText = '';

    /**
     * @return \Zork\Mail\Service
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @param \Zork\Mail\Service $service
     * @return \Mail\Model\Template\Sendable
     */
    public function setService( $service )
    {
        $this->service = $service;
        return $this;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param array $options
     * @return \Mail\Model\Template\Sendable
     */
    public function setOptions( array $options )
    {
        $this->options = $options;
        return $this;
    }

    /**
     * @return string
     */
    public function getTemplateHtml()
    {
        return $this->templateHtml;
    }

    /**
     * @param string $templateHtml
     * @return \Mail\Model\Template\Sendable
     */
    public function setTemplateHtml( $templateHtml )
    {
        $this->templateHtml = (string) $templateHtml;
        return $this;
    }

    /**
     * @return string
     */
    public function getTemplateText()
    {
        return $this->templateText;
    }

    /**
     * @param string $templateText
     * @return \Mail\Model\Template\Sendable
     */
    public function setTemplateText( $templateText )
    {
        $this->templateText = (string) $templateText;
        return $this;
    }

    /**
     * Construct sendable
     *
     * @param \Zork\Mail\Service $mailService
     */
    public function __construct( MailService $mailService,
                                 SiteInfo $siteInfo,
                                 array $options,
                                 $templateHtml,
                                 $templateText )
    {
        $this->setService( $mailService )
             ->setSiteInfo( $siteInfo )
             ->setOptions( $options )
             ->setTemplateHtml( $templateHtml )
             ->setTemplateText( $templateText );
    }

    /**
     * @param array|\Traversable $variables
     * @param array|\Traversable|string $to
     * @param null|array|\Traversable|string $cc
     * @param null|array|\Traversable|string $bcc
     * @throws Exception\InvalidArgumentException
     * @return void|mixed based on the transport's response
     */
    public function send( $variables, $to, $cc = null, $bcc = null )
    {
        if ( $variables instanceof Traversable )
        {
            $variables = ArrayUtils::iteratorToArray( $variables );
        }

        if ( ! is_array( $variables ) )
        {
            throw new Exception\InvalidArgumentException(
                '$variables need to be an array ' .
                '(or instance of \Traversable) in ' . __METHOD__
            );
        }

        $html    = $this->getTemplateHtml();
        $text    = $this->getTemplateText();
        $options = $this->getOptions();
        $options = array_combine(
            array_keys( $options ),
            array_values( $options )
        );

        if ( empty( $variables['site_domain'] ) )
        {
            $variables['site_domain'] = $this->getSiteInfo()
                                             ->getDomain();
        }

        if ( empty( $variables['site_url'] ) )
        {
            $variables['site_url'] = 'http://' . $variables['site_domain'];
        }

        foreach ( $variables as $key => & $value )
        {
            $value = (string) $value;

            if ( $value[0] === '/' &&
                 strtolower( substr( $key, -4 ) ) === '_url' )
            {
                $value = $variables['site_url'] . $value;
            }
        }

        $options['body'] = array(
            'text/html' => String::template( $html, $variables )
        );

        if ( ! empty( $text ) )
        {
            $options['body']['text/plain'] = String::template( $text, $variables );
        }

        $options['to'] = $to;

        if ( null !== $cc )
        {
            $options['cc'] = $cc;
        }

        if ( null !== $bcc )
        {
            $options['bcc'] = $bcc;
        }

        return $this->getService()
                    ->send( $options );
    }

}
