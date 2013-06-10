<?php

namespace Grid\Core\View\Helper\RowSet\Type;

use PHPUnit_Framework_TestCase as TestCase;

/**
 * CallbackTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class CallbackTest extends TestCase
{

    public function testValues()
    {
        $map = array(
            0 => null,
            1 => true,
            2 => 2,
            3 => '3',
        );

        $cb = new Callback( function ( $context, $value ) use ( $map ) {
            return $map[$value];
        } );

        $this->assertSame( null, $cb->displayValue( 0, null ) );
        $this->assertSame( true, $cb->displayValue( 1, null ) );
        $this->assertSame( 2,    $cb->displayValue( 2, null ) );
        $this->assertSame( '3',  $cb->displayValue( 3, null ) );
    }

}
