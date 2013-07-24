<?php

namespace Grid\User\Model;

use Zork\Stdlib\String;
use Zork\Cache\AbstractCacheStorage;

/**
 * ConfirmHash
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class ConfirmHash extends AbstractCacheStorage
{

    /**
     * @var int
     */
    const HASH_LENGTH = 24;

    /**
     * Request a password-change
     *
     * @param   string  $email
     * @return  string  hash
     */
    public function create( $email )
    {
        $store = $this->getCacheStorage();

        do
        {
            $hash = String::generateRandom( self::HASH_LENGTH, null, true );
        }
        while ( $store->hasItem( $hash ) );

        $store->setItem( $hash, $email );
        return $hash;
    }

    /**
     * Requested password change's hash is valid
     *
     * @param   string  $hash
     * @return  bool
     */
    public function has( $hash )
    {
        return $this->getCacheStorage()
                    ->hasItem( $hash );
    }

    /**
     * Requested password change's email by hash
     *
     * @param   string  $hash
     * @return  string  email
     */
    public function find( $hash )
    {
        return $this->getCacheStorage()
                    ->getItem( $hash );
    }

    /**
     * Resolve a password change request
     *
     * @param   string  $hash
     * @return  bool
     */
    public function delete( $hash )
    {
        return $this->getCacheStorage()
                    ->removeItem( $hash );
    }

}
