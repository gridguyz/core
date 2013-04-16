<?php

namespace Grid\Core\Model\SubDomain;

use Zork\Model\LocaleAwareTrait;
use Zork\Model\LocaleAwareInterface;
use Zork\Model\Structure\MapperAwareAbstract;

/**
 * Structure
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Structure extends MapperAwareAbstract
             implements LocaleAwareInterface
{

    use LocaleAwareTrait;

    /**
     * @var string
     */
    const DEFAULT_SUBDOMAIN = '';

    /**
     * Field: id
     *
     * @var int
     */
    protected $id               = null;

    /**
     * Field: subdomain
     *
     * @var string
     */
    protected $subdomain        = self::DEFAULT_SUBDOMAIN;

    /**
     * Field: defaultLayoutId
     *
     * @var int
     */
    public $defaultLayoutId     = null;

    /**
     * Field: defaultContentId
     *
     * @var int
     */
    public $defaultContentId    = null;

    /**
     * Trim & strip sub-domains for setting
     *
     * @param string $subdomain
     * @return string
     */
    public static function trimSubdomain( $subdomain )
    {
        mb_internal_encoding( 'UTF-8' );

        return trim( preg_replace(
            array( '/\s+/u', '/[^\s\pL\pN_-]/u' ),
            array( '-', '' ),
            mb_strtolower( trim( $subdomain ), 'UTF-8' )
        ), '-' );
    }

    /**
     * Set subdomain
     *
     * @param string $subdomain
     * @return CoreSubDomain_Model_Structure_SubDomain
     */
    public function setSubdomain( $subdomain )
    {
        $this->subdomain = static::trimSubdomain( $subdomain );
        return $this;
    }

}
