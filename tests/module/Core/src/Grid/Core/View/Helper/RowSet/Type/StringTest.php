<?php

namespace Grid\Core\View\Helper\RowSet\Type;

use PHPUnit_Framework_TestCase as TestCase;

/**
 * StringTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class StringTest extends TestCase
{

    public function testValues()
    {
        $string = new String;

        $this->assertSame( '2',     $string->displayValue( 2      ) );
        $this->assertSame( '1.5',   $string->displayValue( 1.5    ) );
        $this->assertSame( '1.5f',  $string->displayValue( '1.5f' ) );
        $this->assertSame( '1',     $string->displayValue( true   ) );
        $this->assertSame( '',      $string->displayValue( false  ) );
        $this->assertSame( '',      $string->displayValue( null   ) );
        $this->assertSame( '',      $string->displayValue( ''     ) );
    }

}
