<?php

namespace Grid\Core\Model;

use Zork\Stdlib\FileSystem;

/**
 * ClearTmp
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class ClearTmp
{

    /**
     * @const string
     */
    const TMP_PATH  = './public/tmp';

    /**
     * @const string
     */
    const TMP_TTL   = 18000;

    /**
     * Clear old files in tmp
     *
     * @return  int
     */
    public function __invoke()
    {
        if ( mt_rand( 0, 100 ) < 5 ) // ~ 5% probablity
        {
            return FileSystem::clearOldFiles(
                static::TMP_PATH,
                static::TMP_TTL,
                true,
                true
            );
        }

        return 0;
    }

}
