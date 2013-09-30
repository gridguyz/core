<?php

namespace Grid\User\Model\User;

use Zork\Rpc\CallableTrait;
use Zork\Rpc\CallableInterface;
use Zork\Model\MapperAwareTrait;
use Zork\Model\MapperAwareInterface;
use Zend\Authentication\AuthenticationService;

/**
 * Rpc
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Rpc implements CallableInterface,
                     MapperAwareInterface
{

    use CallableTrait,
        MapperAwareTrait;

    /**
     * Construct rpc
     *
     * @param \User\Model\User\Mapper $userMapper
     */
    public function __construct( Mapper $userMapper )
    {
        $this->setMapper( $userMapper );
    }

    /**
     * Get user by display name
     *
     * @param string $email
     * @param array|object $fields [optional]
     * @return bool
     */
    public function isEmailAvailable( $email, $fields = array() )
    {
        $fields = (object) $fields;

        return ! $this->getMapper()
                      ->isEmailExists(
                            $email,
                            empty( $fields->id ) ? null : $fields->id
                        ) ?: 'user.action.register.email.taken';
    }

    /**
     * Get user by display name
     *
     * @param string $displayName
     * @param array|object $fields [optional]
     * @return bool
     */
    public function isDisplayNameAvailable( $displayName, $fields = array() )
    {
        $fields      = (object) $fields;
        $displayName = Structure::trimDisplayName( $displayName );

        if ( 3 > mb_strlen( $displayName ) )
        {
            return false;
        }

        return ! $this->getMapper()
                      ->isDisplayNameExists(
                            $displayName,
                            empty( $fields->id ) ? null : $fields->id
                        );
    }

    /**
     * Get user status
     *
     * @return object
     */
    public function status()
    {
        $auth = new AuthenticationService();
        $loggedIn = $auth->hasIdentity();
        $identity = $loggedIn ? $auth->getIdentity() : null;

        return (object) array(
            'loggedIn'      => $loggedIn,
            'id'            => $loggedIn ? $identity->id            : null,
            'email'         => $loggedIn ? $identity->email         : null,
            'displayName'   => $loggedIn ? $identity->displayName   : null,
        );
    }

}
