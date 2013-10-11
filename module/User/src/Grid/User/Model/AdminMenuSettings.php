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
     * @var string
     */
    const EDITMODE_NONE     = 'none';

    /**
     * @var string
     */
    const EDITMODE_CONTENT  = 'content';

    /**
     * @var string
     */
    const EDITMODE_LAYOUT   = 'layout';

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
     * @param   string      $name
     * @param   mixed|null  $set
     * @return  mixed|null
     */
    public function setting( $name, $set = null )
    {
        $model  = $this->getModel();
        $auth   = new AuthenticationService();

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
     * Get/set all settings
     *
     * @param   mixed|null  $set
     * @return  mixed|null
     */
    public function settings( $set = null )
    {
        $model  = $this->getModel();
        $auth   = new AuthenticationService();

        if ( ! $auth->hasIdentity() )
        {
            return null;
        }

        $userId = $auth->getIdentity()->id;

        if ( null === $set )
        {
            return $model->find( $userId, static::SETTINGS_SECTION )
                         ->settings;
        }
        else
        {
            $structure = $model->find( $userId, static::SETTINGS_SECTION );
            $structure->settings = $set;
            $save = $structure->save();

            if ( $save )
            {
                return $save;
            }
        }

        return null;
    }

    /**
     * Get/set open state
     *
     * @param   bool|null   $set
     * @return  bool|null
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
     * @param   string|null $set
     * @return  string|null
     */
    public function position( $set = null )
    {
        static $validPositions = array(
            self::POSITION_LEFT,
            self::POSITION_RIGHT
        );

        if ( null !== $set )
        {
            $set = strtolower( $set );

            if ( ! in_array( $set, $validPositions ) )
            {
                $set = static::POSITION_LEFT;
            }
        }

        return $this->setting( 'position', $set );
    }

    /**
     * Get/set edit-mode state
     *
     * @param   string|null $set
     * @return  string|null
     */
    public function editMode( $set = null )
    {
        $validModes = array(
            self::EDITMODE_NONE,
            self::EDITMODE_CONTENT,
            self::EDITMODE_LAYOUT
        );

        if ( null !== $set )
        {
            $set = strtolower( $set );

            if ( empty( $set ) || ! in_array( $set, $validModes ) )
            {
                $set = static::EDITMODE_NONE;
            }
        }

        return $this->setting( 'editMode', $set );
    }

}
