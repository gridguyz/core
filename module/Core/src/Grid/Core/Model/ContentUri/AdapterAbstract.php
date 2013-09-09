<?php

namespace Grid\Core\Model\ContentUri;

use Zork\Factory\AdapterAbstract as FactoryAdapterAbstract;

/**
 * AdapterAbstract
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
abstract class AdapterAbstract extends FactoryAdapterAbstract
                            implements AdapterInterface
{

    /**
     * @const string
     * @abstract
     */
    const TYPE = '';

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string|null
     */
    protected $subType;

    /**
     * @var string
     */
    protected $contentId;

    /**
     * @var string
     */
    protected $locale;

    /**
     * Return true if and only if $options accepted by this adapter
     * If returns float as likelyhood the max of these will be used as adapter
     *
     * @param   array   $options
     * @return  float
     */
    public static function acceptsOptions( array $options )
    {
        $tl = strlen( static::TYPE );

        return isset( $options['type'] ) && (
            static::TYPE == $options['type'] || (
                strlen( $options['type'] ) > $tl &&
                ( static::TYPE . '.' ) == substr( $options['type'], 0, $tl + 1 )
            )
        );
    }

    /**
     * Return a new instance of the adapter by $options
     *
     * @param   array   $options
     * @return  AdapterAbstract
     */
    public static function factory( array $options = null )
    {
        if ( ! empty( $options['type'] ) &&
             empty( $options['subType'] ) &&
             $type = strstr( $options['type'], '.', true ) )
        {
            $options['subType'] = substr( $options['type'], strlen( $type ) + 1 );
            $options['type']    = $type;
        }

        return parent::factory( $options );
    }

}
