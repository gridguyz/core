<?php

namespace Grid\Core\View\Helper;

use Zork\Db\SiteInfo as SiteInfoModel;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * SiteInfoTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class SiteInfoTest extends TestCase
{

    /**
     * @var SiteInfo
     */
    protected $siteInfo;

    /**
     * @var SiteInfoModel
     */
    protected $siteInfoModel;

    /**
     * Setup singlesite instance
     */
    public function setUp()
    {
        parent::setUp();

        $this->siteInfoModel    = new SiteInfoModel;
        $this->siteInfo         = new SiteInfo( $this->siteInfoModel );
    }

    /**
     * Test getter & setter
     */
    public function testMethods()
    {
        $helper = $this->siteInfo;
        $this->assertSame( $this->siteInfoModel, $helper() );
        $this->assertSame( $this->siteInfoModel, $helper->getModel() );
    }

}
