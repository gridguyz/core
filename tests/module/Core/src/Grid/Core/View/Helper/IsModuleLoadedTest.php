<?php

namespace Grid\Core\View\Helper;

use ArrayIterator;
use Zend\ModuleManager\ModuleManager;
use Zork\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

/**
 * IsModuleLoadedTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class IsModuleLoadedTest extends AbstractHttpControllerTestCase
{

    /**
     * @var \Zend\ModuleManager\ModuleManagerInterface
     */
    protected $moduleManager;

    /**
     * @var IsModuleLoaded
     */
    protected $isModuleLoaded;

    /**
     * Setup singlesite instance
     */
    public function setUp()
    {
        parent::setUp();

        $this->moduleManager    = $this->getService( 'ModuleManager' );
        $this->isModuleLoaded   = new IsModuleLoaded( $this->moduleManager );
    }

    /**
     * Test getter & setter
     */
    public function testGetterAndSetter()
    {
        $helper = $this->isModuleLoaded;
        $this->assertSame( $this->moduleManager, $helper->getModuleManager() );
        $moduleManager = new ModuleManager( array() );
        $helper->setModuleManager( $moduleManager );
        $this->assertSame( $moduleManager, $helper->getModuleManager() );
    }

    /**
     * Test methods
     */
    public function testMethods()
    {
        $helper = $this->isModuleLoaded;
        $this->assertTrue( $helper->isModuleLoaded( 'Grid\Core' ) );
        $this->assertTrue( $helper->isModuleLoaded( 'Grid\User' ) );
        $this->assertFalse( $helper->isModuleLoaded( 'NonLoadedExample' ) );
        $this->assertTrue( $helper( new ArrayIterator( array( 'Grid\Core', 'Grid\User' ) ) ) );
        $this->assertTrue( $helper( 'Grid\Core&Grid\User' ) );
        $this->assertTrue( $helper( 'Grid\Core|NonLoadedExample' ) );
        $this->assertTrue( $helper( 'Grid\Core&Grid\User|NonLoadedExample' ) );
        $this->assertTrue( $helper( 'Grid\Core&NonLoadedExample|Grid\User' ) );
        $this->assertFalse( $helper( 'Grid\Core&NonLoadedExample' ) );
    }

}
