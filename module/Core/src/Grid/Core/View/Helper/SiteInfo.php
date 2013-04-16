<?php

namespace Grid\Core\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zork\Db\SiteInfo as SiteInfoModel;

/**
 * Grid\Core\View\Helper\SiteInfo
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class SiteInfo extends AbstractHelper
{

    /**
     * @var \Zork\Db\SiteInfo
     */
    protected $model;

    /**
     * Constructor
     *
     * @param \Zork\Db\SiteInfo $siteInfoModel
     */
    public function __construct( SiteInfoModel $siteInfoModel )
    {
        $this->model = $siteInfoModel;
    }

    /**
     * Get model
     *
     * @return \Zork\Db\SiteInfo
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Invokable helper
     *
     * @return \Zork\Db\SiteInfo
     */
    public function __invoke()
    {
        return $this->model;
    }

}
