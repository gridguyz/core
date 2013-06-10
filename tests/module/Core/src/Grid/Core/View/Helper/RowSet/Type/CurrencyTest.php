<?php

namespace Grid\Core\View\Helper\RowSet\Type;

use PHPUnit_Framework_TestCase as TestCase;

/**
 * CurrencyTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class CurrencyTest extends TestCase
{

    public function testValues()
    {
        $view = $this->getMock( 'Zend\View\Renderer\RendererInterface', array(
            'getEngine',
            'setResolver',
            'render',
            'currencyFormat',
        ) );
        $curr = new Currency();
        $curr->setView( $view );

        $view->expects( $this->at( 0 ) )
             ->method( 'currencyFormat' )
             ->with( '1', 'EUR' )
             ->will( $this->returnValue( '€1' ) );

        $view->expects( $this->at( 1 ) )
             ->method( 'currencyFormat' )
             ->with( '1.50', 'USD' )
             ->will( $this->returnValue( '$1.5' ) );

        $this->assertSame( '€1', $curr->displayValue( '1 EUR' ) );
        $this->assertSame( '$1.5', $curr->displayValue( '1.50 USD' ) );
        $this->assertSame( '1USD', $curr->displayValue( '1USD' ) );
    }

}
