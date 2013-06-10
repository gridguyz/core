<?php

namespace Grid\Core\View\Helper\RowSet\Type;

use PHPUnit_Framework_TestCase as TestCase;

/**
 * BoolTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class BoolTest extends TestCase
{

    public function testValues()
    {
        $bool = new Bool;

        $this->assertEquals( $bool::TRUE,  $bool->displayValue( true    ) );
        $this->assertEquals( $bool::FALSE, $bool->displayValue( false   ) );
        $this->assertEquals( $bool::FALSE, $bool->displayValue( null    ) );
        $this->assertEquals( $bool::FALSE, $bool->displayValue( 0       ) );
        $this->assertEquals( $bool::FALSE, $bool->displayValue( ''      ) );
        $this->assertEquals( $bool::FALSE, $bool->displayValue( '0'     ) );
        $this->assertEquals( $bool::FALSE, $bool->displayValue( 'f'     ) );
        $this->assertEquals( $bool::FALSE, $bool->displayValue( 'n'     ) );
        $this->assertEquals( $bool::FALSE, $bool->displayValue( 'no'    ) );
        $this->assertEquals( $bool::FALSE, $bool->displayValue( 'off'   ) );
        $this->assertEquals( $bool::FALSE, $bool->displayValue( 'false' ) );
        $this->assertEquals( $bool::TRUE,  $bool->displayValue( 1       ) );
        $this->assertEquals( $bool::TRUE,  $bool->displayValue( '1'     ) );
        $this->assertEquals( $bool::TRUE,  $bool->displayValue( 'y'     ) );
        $this->assertEquals( $bool::TRUE,  $bool->displayValue( 't'     ) );
        $this->assertEquals( $bool::TRUE,  $bool->displayValue( 'on'    ) );
        $this->assertEquals( $bool::TRUE,  $bool->displayValue( 'yes'   ) );
        $this->assertEquals( $bool::TRUE,  $bool->displayValue( 'true'  ) );
    }

}
