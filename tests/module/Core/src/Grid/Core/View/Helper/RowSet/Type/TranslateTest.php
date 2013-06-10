<?php

namespace Grid\Core\View\Helper\RowSet\Type;

use PHPUnit_Framework_TestCase as TestCase;

/**
 * TranslateTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class TranslateTest extends TestCase
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

        $translate = new Translate();
        $translate->setView( $view );

        $this->assertEquals( 'foo', $translate->displayValue( 'foo' ) );
        $this->assertEquals( 'bar', $translate->displayValue( 'bar' ) );
        $this->assertEquals( 'baz', $translate->displayValue( 'baz' ) );

        $translatef = new Translate( 'prefix', 'postfix' );
        $translatef->setView( $view );

        $this->assertEquals( 'prefix.foo.postfix', $translatef->displayValue( 'foo' ) );
        $this->assertEquals( 'prefix.bar.postfix', $translatef->displayValue( 'bar' ) );
        $this->assertEquals( 'prefix.baz.postfix', $translatef->displayValue( 'baz' ) );
    }

}
