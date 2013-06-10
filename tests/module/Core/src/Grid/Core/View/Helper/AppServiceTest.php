<?php

namespace Grid\Core\View\Helper;

use PHPUnit_Framework_TestCase as TestCase;

/**
 * AppServiceTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class AppServiceTest extends TestCase
{

    /**
     * @var AppService
     */
    protected $appService;

    /**
     * @var \Zend\ServiceManager\ServiceLocatorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $serviceLocatorMock;

    /**
     * Setup singlesite instance
     */
    public function setUp()
    {
        parent::setUp();

        $this->serviceLocatorMock   = $this->getMock( 'Zend\ServiceManager\ServiceLocatorInterface' );
        $this->appService           = new AppService( $this->serviceLocatorMock );
    }

    /**
     * Test methods
     */
    public function testMethods()
    {
        $helper = $this->appService;
        $this->assertSame( $this->serviceLocatorMock, $helper->getServiceLocator() );
        $this->assertSame( $this->serviceLocatorMock, $helper() );

        $this->serviceLocatorMock
             ->expects( $this->once() )
             ->method( 'get' )
             ->with( 'ExampleService' )
             ->will( $this->returnValue( new \stdClass ) );

        $this->assertInstanceOf( 'stdClass', $helper( 'ExampleService' ) );
    }

}
