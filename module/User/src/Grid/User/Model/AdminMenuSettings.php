<?php

namespace Grid\User\Model;

use Zork\Rpc\CallableTrait;
use Zork\Rpc\CallableInterface;
use Zork\Model\ModelAwareTrait;
use Zork\Model\ModelAwareInterface;
use Zend\Authentication\AuthenticationService;
use Zork\Authentication\AuthenticationServiceAwareTrait;

/**
 * AdminMenuSettings
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class AdminMenuSettings implements CallableInterface,
                                   ModelAwareInterface
{

    use CallableTrait,
        ModelAwareTrait,
        AuthenticationServiceAwareTrait;

    /**
     * @var string
     */
    const SETTINGS_SECTION  = 'adminMenu';

    /**
     * @const bool
     */
    const OPEN_DEFAULT      = true;

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
    const POSITION_DEFAULT  = self::POSITION_LEFT;

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
     * @var string
     */
    const EDITMODE_DEFAULT  = self::EDITMODE_NONE;

    /**
     * @var array
     */
    protected static $defaults = array(
        'open'      => self::OPEN_DEFAULT,
        'position'  => self::POSITION_DEFAULT,
        'editMode'  => self::EDITMODE_DEFAULT,
    );

    /**
     * Constructor
     *
     * @param   User\Settings\Model     $userSettingsModel
     * @param   AuthenticationService   $authenticationService
     */
    public function __construct( User\Settings\Model    $userSettingsModel,
                                 AuthenticationService  $authenticationService )
    {
        $this->setModel( $userSettingsModel )
             ->setAuthenticationService( $authenticationService );
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
        $auth   = $this->getAuthenticationService();

        if ( ! $auth->hasIdentity() )
        {
            return null;
        }

        $userId = $auth->getIdentity()->id;

        if ( null === $set )
        {
            $setting = $model->find( $userId, static::SETTINGS_SECTION )
                             ->getSetting( $name );

            if ( null === $setting && isset( static::$defaults[$name] ) )
            {
                return static::$defaults[$name];
            }

            return $setting;
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
        $auth   = $this->getAuthenticationService();

        if ( ! $auth->hasIdentity() )
        {
            return null;
        }

        $userId = $auth->getIdentity()->id;

        if ( null === $set )
        {
            $settings = $model->find( $userId, static::SETTINGS_SECTION )
                              ->settings;

            foreach ( static::$defaults as $name => $value )
            {
                if ( ! isset( $settings[$name] ) )
                {
                    $settings[$name] = $value;
                }
            }

            return $settings;
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
                $set = static::POSITION_DEFAULT;
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
                $set = static::EDITMODE_DEFAULT;
            }
        }

        return $this->setting( 'editMode', $set );
    }

}
