<?php

namespace Grid\Core\View\Helper\RowSet\Type;

use PHPUnit_Framework_TestCase as TestCase;

/**
 * IntTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class IntTest extends TestCase
{

    public function testValues()
    {
        $int = new Int;

        $this->assertSame( '2', $int->displayValue( 2      ) );
        $this->assertSame( '1', $int->displayValue( 1.5    ) );
        $this->assertSame( '1', $int->displayValue( '1.5f' ) );
        $this->assertSame( '1', $int->displayValue( true   ) );
        $this->assertSame( '0', $int->displayValue( false  ) );
        $this->assertSame( '0', $int->displayValue( null   ) );
        $this->assertSame( '0', $int->displayValue( ''     ) );
    }

}
