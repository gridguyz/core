<?php

namespace Grid\Core\View\Helper\RowSet\Type;

use PHPUnit_Framework_TestCase as TestCase;

/**
 * ReplaceTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class ReplaceTest extends TestCase
{

    public function testString()
    {
        $replace = new Replace( 'a', 'b', false );

        $this->assertSame( 'bbbb',  $replace->displayValue( 'abab' ) );
        $this->assertSame( 'bbbb',  $replace->displayValue( 'aaaa' ) );
        $this->assertSame( 'bbbb',  $replace->displayValue( 'bbbb' ) );
    }

    public function testRegexp()
    {
        $replace = new Replace( '/a{2}/', 'bb', true );

        $this->assertSame( 'abab',  $replace->displayValue( 'abab' ) );
        $this->assertSame( 'bbbb',  $replace->displayValue( 'aaaa' ) );
        $this->assertSame( 'bbbb',  $replace->displayValue( 'bbbb' ) );
    }

}
