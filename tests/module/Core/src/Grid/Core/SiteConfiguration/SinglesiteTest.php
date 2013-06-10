<?php

namespace Grid\Core\SiteConfiguration;

use Zork\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

/**
 * SinglesiteTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class SinglesiteTest extends AbstractHttpControllerTestCase
{

    /**
     * Application config override
     *
     * @var array
     */
    protected $applicationConfigOverride = array(
        'db' => array(
            'defaultDomain' => 'fallback.example.com',
        ),
        'service_manager'   => array(
            'invokables'    => array(
                'SiteConfiguration' => 'Grid\Core\SiteConfiguration\Singlesite',
            ),
        ),
    );

    /**
     * @var Singlesite
     */
    protected $singlesite;

    /**
     * Setup singlesite instance
     */
    public function setUp()
    {
        parent::setUp();

        /* @var $sm \Zend\ServiceManager\ServiceManager */
        $sm = $this->getApplicationServiceLocator();

        if ( ! $sm->getAllowOverride() )
        {
            $allowOverride = false;
            $sm->setAllowOverride( true );
        }
        else
        {
            $allowOverride = true;
        }

        $sm->setService( 'SiteInfo', null );
        $sm->setService( 'RedirectToDomain', null );
        $sm->setAllowOverride( $allowOverride );

        $this->singlesite = new Singlesite;
        $this->singlesite->setServiceLocator( $sm );
    }

    /**
     * Unset singlesite instance
     */
    public function tearDown()
    {
        $this->singlesite = null;
        parent::tearDown();
    }

    /**
     * Test configure default subdomain
     */
    public function testConfigureDefaultSubdomain()
    {
        $db = $this->getService( 'Zend\Db\Adapter\Adapter' );
        $this->singlesite->setDomain( 'example.com' )->configure( $db );
        $siteInfo = $this->getService( 'SiteInfo' );
        $this->assertInstanceOf( 'Zork\Db\SiteInfo', $siteInfo );
        $this->assertEquals( '', $siteInfo->getSubdomain() );
        $this->assertEquals( 'example.com', $siteInfo->getDomain() );
        $this->assertEquals( 'example.com', $siteInfo->getFulldomain() );
    }

    /**
     * Test configure non-existent subdomain
     */
    public function testConfigureNonExistentSubdomain()
    {
        $db = $this->getService( 'Zend\Db\Adapter\Adapter' );
        $this->singlesite->setDomain( 'www.example.com' )->configure( $db );
        $redirectToDomain = $this->getService( 'RedirectToDomain' );
        $this->assertInstanceOf( 'Zork\Db\SiteConfiguration\RedirectionService', $redirectToDomain );
        $this->assertEquals( 'example.com', $redirectToDomain->getDomain() );
    }

    /**
     * Test configure localhost as domain
     */
    public function testConfigureLocalhost()
    {
        $db = $this->getService( 'Zend\Db\Adapter\Adapter' );
        $this->singlesite->setDomain( 'localhost' )->configure( $db );
        $siteInfo = $this->getService( 'SiteInfo' );
        $this->assertInstanceOf( 'Zork\Db\SiteInfo', $siteInfo );
        $this->assertEquals( '', $siteInfo->getSubdomain() );
        $this->assertEquals( 'localhost', $siteInfo->getDomain() );
        $this->assertEquals( 'localhost', $siteInfo->getFulldomain() );
    }

}
