<?php

namespace Grid\Core\Model\ContentUri;

use Zork\Factory\AdapterInterface as FactoryAdapterInterface;

/**
 * ContentUri AdapterInterface
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
interface AdapterInterface extends FactoryAdapterInterface
{

    /**
     * Get uri
     *
     * @param   bool    $absolute
     * @return  string
     */
    public function getUri( $absolute = false );

}
