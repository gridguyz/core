<?php

namespace Grid\Core\Model;

use PHPUnit_Framework_TestCase as TestCase;

/**
 * TranslateTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class TranslateTest extends TestCase
{

    /**
     * @var \Zend\I18n\Translator\Translator|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $translator;

    /**
     * @var Translate
     */
    protected $translate;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        parent::setUp();

        $this->translator = $this->getMock( 'Zend\I18n\Translator\Translator' );
        $this->translate  = new Translate( $this->translator );
    }

    /**
     * Test translate
     */
    public function testTranslate()
    {
        $this->translator
             ->expects( $this->once() )
             ->method( 'translate' )
             ->with( 'message', 'textDomain', 'en' )
             ->will( $this->returnArgument( 0 ) );

        $this->translate
             ->call( 'translate', array(
                 'message',
                 'textDomain',
                 'en'
             ) );
    }

    /**
     * Test translate plural
     */
    public function testTranslatePlural()
    {
        $this->translator
             ->expects( $this->once() )
             ->method( 'translatePlural' )
             ->with( 'singular', 'plural', 1, 'textDomain', 'en' )
             ->will( $this->returnArgument( 0 ) );

        $this->translate
             ->call( 'translatePlural', array(
                 'singular',
                 'plural',
                 1,
                 'textDomain',
                 'en'
             ) );
    }

    /**
     * Test text-domain
     */
    public function testTextDomain()
    {
        $this->assertNull(
            $this->translate
                 ->call( 'textDomain', array(
                     'textDomain',
                     'en'
                 ) )
        );
    }

}
