<?php

namespace Grid\Core\View\Helper\RowSet\Type;

/**
 * Thumbnail
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Thumbnail extends AbstractHelper
{

    /**
     * @var int
     */
    const DEFAULT_WIDTH     = 25;

    /**
     * @var int
     */
    const DEFAULT_HEIGHT    = 25;

    /**
     * @var string
     */
    const DEFAULT_METHOD    = 'fit';

    /**
     * @var array
     */
    public static $defaultOptions = array(
        'width'     => self::DEFAULT_WIDTH,
        'height'    => self::DEFAULT_HEIGHT,
        'method'    => self::DEFAULT_METHOD,
    );

    /**
     * @var array
     */
    protected $options = array();

    /**
     * @return array
     */
    public function getOptions()
    {
        return array_merge( static::$defaultOptions, $this->options );
    }

    /**
     * @param array $options
     * @param bool $merge
     * @return \Core\View\Helper\RowSet\Type\Thumbnail
     */
    public function setOptions( array $options, $merge = true )
    {
        if ( $merge )
        {
            $options = array_merge( $this->options, $options );
        }

        $this->options = $options;
        return $this;
    }

    /**
     * @param null|array $options
     */
    public function __construct( $options = null )
    {
        if ( null !== $options )
        {
            $this->setOptions( (array) $options );
        }
    }

    /**
     * Display a single value
     *
     * @param int|string|\DateTime $value
     * @return string
     */
    public function displayValue( $value )
    {
        if ( empty( $value ) )
        {
            return '';
        }

        return $this->view
                    ->htmlTag( 'img', null, array(
                        'data-js-type' => 'js.ui.toolTip',
                        'src'   => $this->view
                                        ->thumbnail( $value,
                                                     $this->getOptions() ),
                        'title' => $this->view
                                        ->htmlTag( 'img', null, array(
                                            'src'   => $value,
                                            'style' => 'max-width: 300px;'
                                                    . ' max-height: 500px;'
                                                    . ' margin-bottom: -3px;',
                                        ) ),
                    ) );
    }

}
