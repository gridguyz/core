<?php

namespace Grid\Core\Model\ContentUri;

/**
 * DefaultFallback
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class DefaultFallback extends AdapterAbstract
{

    /**
     * Return true if and only if $options accepted by this adapter
     * If returns float as likelyhood the max of these will be used as adapter
     *
     * @param   array   $options
     * @return  float
     */
    public static function acceptsOptions( array $options )
    {
        return 0.1;
    }

    /**
     * Get uri
     *
     * @param   bool    $absolute
     * @return  string
     */
    public function getUri( $absolute = false )
    {
        return '#error-adapterNotFoundFor-'
             . $this->getOption( 'type',      'missing:type' ) . '-'
             . $this->getOption( 'contentId', 'missing:contentId' ) . '-'
             . $this->getOption( 'locale',    'missing:locale' );
    }

}
