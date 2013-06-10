<?php

namespace Grid\Core\View\Helper\RowSet\Type;

use PHPUnit_Framework_TestCase as TestCase;

/**
 * DateTimeTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class DateTimeTest extends TestCase
{

    public function testValues()
    {
        $view = $this->getMock( 'Zend\View\Renderer\RendererInterface', array(
            'getEngine',
            'setResolver',
            'render',
            'dateFormat',
        ) );
        $date = new DateTime();
        $date->setView( $view );

        $view->expects( $this->any() )
             ->method( 'dateFormat' )
             ->withAnyParameters()
             ->will( $this->returnArgument( 0 ) );

        $this->assertInstanceOf( 'DateTime', $date->displayValue( new \DateTime ) );
        $this->assertInstanceOf( 'DateTime', $date->displayValue( time() ) );
        $this->assertInstanceOf( 'DateTime', $date->displayValue( date( 'Y-m-d H:i:s' ) ) );
    }

}
