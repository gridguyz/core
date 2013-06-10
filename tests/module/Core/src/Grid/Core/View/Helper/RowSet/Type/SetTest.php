<?php

namespace Grid\Core\View\Helper\RowSet\Type;

use PHPUnit_Framework_TestCase as TestCase;

/**
 * SetTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class SetTest extends TestCase
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

        $set = new Set( array(
            'key1' => 'val1',
            'key2' => 'val2',
        ), null, null, null, ', ' );

        $set->setView( $view );

        $this->assertEquals( 'val1', $set->displayValue( 'key1' ) );
        $this->assertEquals( 'val2', $set->displayValue( 'key2' ) );
        $this->assertEquals( 'foo',  $set->displayValue( 'foo' ) );

        $this->assertEquals(
            'val1, val2, foo',
            $set->displayValue( array( 'key1', 'key2', 'foo' ) )
        );

        $setf = new Set( array(
            'key1' => 'val1',
            'key2' => 'val2',
        ), 'prefix', 'postfix', null, ', ' );
        $setf->setView( $view );

        $this->assertEquals( 'prefix.val1.postfix', $setf->displayValue( 'key1' ) );
        $this->assertEquals( 'prefix.val2.postfix', $setf->displayValue( 'key2' ) );
        $this->assertEquals( 'prefix.foo.postfix',  $setf->displayValue( 'foo' ) );

        $this->assertEquals(
            'prefix.val1.postfix, prefix.val2.postfix, prefix.foo.postfix',
            $setf->displayValue( array( 'key1', 'key2', 'foo' ) )
        );
    }

}
