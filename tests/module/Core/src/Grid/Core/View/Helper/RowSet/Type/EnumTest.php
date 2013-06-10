<?php

namespace Grid\Core\View\Helper\RowSet\Type;

use PHPUnit_Framework_TestCase as TestCase;

/**
 * EnumTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class EnumTest extends TestCase
{

    public function testValues()
    {
        $view = $this->getMock( 'Zend\View\Renderer\RendererInterface', array(
            'getEngine',
            'setResolver',
            'render',
            'translate',
        ) );

        $view->expects( $this->any() )
             ->method( 'translate' )
             ->withAnyParameters()
             ->will( $this->returnArgument( 0 ) );

        $enum = new Enum( array(
            'key1' => 'val1',
            'key2' => 'val2',
        ) );

        $enum->setView( $view );

        $this->assertEquals( 'val1', $enum->displayValue( 'key1' ) );
        $this->assertEquals( 'val2', $enum->displayValue( 'key2' ) );
        $this->assertEquals( 'foo',  $enum->displayValue( 'foo' ) );

        $enumf = new Enum( array(
            'key1' => 'val1',
            'key2' => 'val2',
        ), 'prefix', 'postfix' );
        $enumf->setView( $view );

        $this->assertEquals( 'prefix.val1.postfix', $enumf->displayValue( 'key1' ) );
        $this->assertEquals( 'prefix.val2.postfix', $enumf->displayValue( 'key2' ) );
        $this->assertEquals( 'prefix.foo.postfix',  $enumf->displayValue( 'foo' ) );
    }

}
