<?php

namespace Grid\User\Model;

use Zork\Rpc\CallableTrait;
use Zork\Rpc\CallableInterface;
use Zork\Model\ModelAwareTrait;
use Zork\Model\ModelAwareInterface;
use Zend\Authentication\AuthenticationService;

/**
 * AdminMenuSettings
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class AdminMenuSettings implements CallableInterface,
                                   ModelAwareInterface
{

    use CallableTrait,
        ModelAwareTrait;

    /**
     * @var string
     */
    const SETTINGS_SECTION  = 'adminMenu';

    /**
     * @var string
     */
    const POSITION_LEFT     = 'left';

    /**
     * @var string
     */
    const POSITION_RIGHT    = 'right';

    /**
     * Constructor
     *
     * @param \User\Model\User\Settings\Model $userSettingsModel
     */
    public function __construct( User\Settings\Model $userSettingsModel )
    {
        $this->setModel( $userSettingsModel );
    }

    /**
     * Get/set a setting by name
     *
     * @param string $name
     * @param mixed|null $set
     * @return mixed|null
     */
    public function setting( $name, $set = null )
    {
        $model = $this->getModel();

        $auth = new AuthenticationService();

        if ( ! $auth->hasIdentity() )
        {
            return null;
        }

        $userId = $auth->getIdentity()->id;

        if ( null === $set )
        {
            return $model->find( $userId, static::SETTINGS_SECTION )
                         ->getSetting( $name );
        }
        else
        {
            $save = $model->find( $userId, static::SETTINGS_SECTION )
                          ->setSetting( $name, $set )
                          ->save();

            if ( $save )
            {
                return $set;
            }
        }

        return null;
    }

    /**
     * Get/set open state
     *
     * @param bool|null $set
     * @return bool|null
     */
    public function open( $set = null )
    {
        return $this->setting(
            'open',
            null === $set ? null : (bool) $set
        );
    }

    /**
     * Get/set position state
     *
     * @param string|null $set
     * @return string|null
     */
    public function position( $set = null )
    {
        return $this->setting(
            'position',
            null === $set ? null : (string) $set
        );
    }

}
