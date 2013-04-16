<?php

namespace Grid\Mail\Model\Template;

use Traversable;
use Zork\Db\SiteInfo;
use Zork\Model\Exception;
use Zend\Stdlib\ArrayUtils;
use Zork\Db\SiteInfoAwareTrait;
use Zork\Db\SiteInfoAwareInterface;
use Zork\Mail\Service as MailService;

/**
 * Sender
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Sender implements SiteInfoAwareInterface
{

    use SiteInfoAwareTrait;

    /**
     * @var \Mail\Model\Template\Model
     */
    protected $model;

    /**
     * @var \Zork\Mail\Service
     */
    protected $service;

    /**
     * @return \Mail\Model\Template\Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param \Mail\Model\Template\Model $model
     * @return \Mail\Model\Template\Sender
     */
    public function setModel( Model $model )
    {
        $this->model = $model;
        return $this;
    }

    /**
     * @return \Zork\Mail\Service
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @param \Zork\Mail\Service $service
     * @return \Mail\Model\Template\Sender
     */
    public function setService( $service )
    {
        $this->service = $service;
        return $this;
    }

    /**
     * Construct sender
     *
     * @param \Mail\Model\Template\Model $mailTemplateModel
     * @param \Zork\Mail\Service $mailService
     */
    public function __construct( Model $mailTemplateModel,
                                 MailService $mailService,
                                 SiteInfo $siteInfo )
    {
        $this->setModel( $mailTemplateModel )
             ->setService( $mailService )
             ->setSiteInfo( $siteInfo );
    }

    /**
     * @param array|\Traversable $options
     * @throws Exception\InvalidArgumentException
     * @return \Mail\Model\Template\Sendable
     */
    public function prepare( $options )
    {
        if ( $options instanceof Traversable )
        {
            $options = ArrayUtils::iteratorToArray( $options );
        }

        if ( ! is_array( $options ) )
        {
            throw new Exception\InvalidArgumentException(
                '$options need to be an array ' .
                '(or instance of \Traversable) in ' . __METHOD__
            );
        }

        if ( empty( $options['template'] ) )
        {
            throw new Exception\InvalidArgumentException(
                '$options[template] is a required option in ' . __METHOD__
            );
        }

        $name = (string) $options['template'];
        unset( $options['template'] );

        if ( empty( $options['locale'] ) )
        {
            $locale = null;
        }
        else
        {
            $locale = (string) $options['locale'];
            unset( $options['locale'] );
        }

        $template = $this->getModel()
                         ->findByName( $name, $locale );

        if ( empty( $template ) )
        {
            throw new Exception\LogicException(
                '"' . $name . '" named template is not found in ' . __METHOD__
            );
        }

        if ( ! empty( $template->subject ) )
        {
            $options['subject'] = (string) $template->subject;
        }

        if ( ! empty( $template->fromAddress ) )
        {
            $options['from'] = array(
                $template->fromAddress => empty( $template->fromName )
                    ? $template->fromAddress : $template->fromName
            );
        }

        return new Sendable(
            $this->getService(),
            $this->getSiteInfo(),
            $options,
            $template->bodyHtml,
            $template->bodyText
        );
    }

}
