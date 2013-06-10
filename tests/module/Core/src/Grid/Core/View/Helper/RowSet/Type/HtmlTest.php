<?php

namespace Grid\Core\View\Helper\RowSet\Type;

use PHPUnit_Framework_TestCase as TestCase;

/**
 * HtmlTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class HtmlTest extends TestCase
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

        $html = new Html( 20 );
        $html->setView( $view );

        $this->assertEquals( 'foo', $html->displayValue( '<div>foo</div>' ) );
        $this->assertRegExp( '/^[^<>]+<button(\s+[^>]*)?>.*<\/button>\s*$/', $html->displayValue( str_repeat( '<div>foo</div>', 9 ) ) );
    }

}
