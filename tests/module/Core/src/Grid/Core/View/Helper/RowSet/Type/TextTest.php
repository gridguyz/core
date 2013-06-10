<?php

namespace Grid\Core\View\Helper\RowSet\Type;

use PHPUnit_Framework_TestCase as TestCase;

/**
 * TextTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class TextTest extends TestCase
{

    public function testValues()
    {
        $view = $this->getMock( 'Zend\View\Renderer\RendererInterface', array(
            'getEngine',
            'setResolver',
            'render',
            'translate',
            'escapeJs',
            'escapeHtml',
        ) );

        $view->expects( $this->any() )
             ->method( 'translate' )
             ->withAnyParameters()
             ->will( $this->returnArgument( 0 ) );

        $view->expects( $this->any() )
             ->method( 'escapeJs' )
             ->withAnyParameters()
             ->will( $this->returnArgument( 0 ) );

        $view->expects( $this->any() )
             ->method( 'escapeHtml' )
             ->withAnyParameters()
             ->will( $this->returnArgument( 0 ) );

        $text = new Text( 20 );
        $text->setView( $view );

        $this->assertEquals( str_repeat( 'foo', 3 ), $text->displayValue( str_repeat( 'foo', 3 ) ) );
        $this->assertEquals( str_repeat( 'foo', 6 ), $text->displayValue( str_repeat( 'foo', 6 ) ) );
        $this->assertRegExp( '/<button(\s+[^>]*)?>.*<\/button>\s*$/', $text->displayValue( str_repeat( 'foo', 9 ) ) );
    }

}
