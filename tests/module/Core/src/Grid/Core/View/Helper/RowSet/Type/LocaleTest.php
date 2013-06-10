<?php

namespace Grid\Core\View\Helper\RowSet\Type;

use PHPUnit_Framework_TestCase as TestCase;

/**
 * LocaleTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class LocaleTest extends TestCase
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

        $translate = new Locale();
        $translate->setView( $view );

        $this->assertEquals( 'locale.sub.en', $translate->displayValue( 'en' ) );
        $this->assertEquals( 'locale.sub.fr', $translate->displayValue( 'fr' ) );
        $this->assertEquals( 'locale.sub.en_US', $translate->displayValue( 'en_US' ) );
    }

}
