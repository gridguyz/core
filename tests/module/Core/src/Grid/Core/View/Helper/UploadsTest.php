<?php

namespace Grid\Core\View\Helper;

use PHPUnit_Framework_TestCase as TestCase;

/**
 * UploadsTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class UploadsTest extends TestCase
{

    /**
     * @var Uploads
     */
    protected $uploads;

    /**
     * Setup singlesite instance
     */
    public function setUp()
    {
        parent::setUp();

        $this->uploads = new Uploads( '_central' );
    }

    /**
     * Test getter & setter
     */
    public function testGetterSetter()
    {
        $this->assertEquals( '_central', $this->uploads->getSchema() );
        $this->uploads->setSchema( 'custom_schema' );
        $this->assertEquals( 'custom_schema', $this->uploads->getSchema() );
    }

    /**
     * Test invoke helper
     */
    public function testInvoke()
    {
        $uploads = $this->uploads;
        $this->assertSame( '/uploads/_central/path/to/file.ext', $uploads( '/path/to/file.ext' ) );
        $this->uploads->setSchema( '' );
        $this->assertNull( $uploads( '/path/to/file.ext' ) );
    }

}
