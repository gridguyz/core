<?php

namespace Grid\Core\View\Helper\RowSet\Type;

use PHPUnit_Framework_TestCase as TestCase;

/**
 * FloatTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class FloatTest extends TestCase
{

    public function testValues()
    {
        $float = new Float;

        $this->assertSame( '2',     $float->displayValue( 2      ) );
        $this->assertSame( '1.5',   $float->displayValue( 1.5    ) );
        $this->assertSame( '1.5',   $float->displayValue( '1.5f' ) );
        $this->assertSame( '1',     $float->displayValue( true   ) );
        $this->assertSame( '0',     $float->displayValue( false  ) );
        $this->assertSame( '0',     $float->displayValue( null   ) );
        $this->assertSame( '0',     $float->displayValue( ''     ) );
    }

}
