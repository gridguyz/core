<?php

namespace Grid\Core\Model;

use Zork\Rpc\CallableTrait;
use Zork\Rpc\CallableInterface;
use Zork\Captcha\Regeneratable;

/**
 * Captcha
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Captcha implements CallableInterface
{

    use CallableTrait;

    /**
     * Create regeneratable captcha
     *
     * @param array|null $options
     * @return \Zork\Captcha\Regeneratable
     */
    protected function createRegeneratable( $options = null )
    {
        return new Regeneratable( $options );
    }

    /**
     * Regenerate a captcha by id
     *
     * @param string $id
     * @return bool
     */
    public function regenerate( $id )
    {
        return $this->createRegeneratable( array( 'id' => $id ) )
                    ->regenerate();
    }

    /**
     * A captcha is valid by id
     *
     * @param string $id
     * @param string $value
     * @param array|object $context
     * @return bool
     */
    public function isValid( $id, $value, $context = null )
    {
        return $this->createRegeneratable( array( 'id' => $id ) )
                    ->isValid( $value, $context );
    }

}
