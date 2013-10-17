<?php

namespace Grid\User\Model\User;

use Zork\Rpc\CallableTrait;
use Zork\Rpc\CallableInterface;
use Zork\Model\MapperAwareTrait;
use Zork\Model\MapperAwareInterface;
use Zend\Authentication\AuthenticationService;
use Zork\Authentication\AuthenticationServiceAwareTrait;

/**
 * Rpc
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Rpc implements CallableInterface,
                     MapperAwareInterface
{

    use CallableTrait,
        MapperAwareTrait,
        AuthenticationServiceAwareTrait;

    /**
     * Construct rpc
     *
     * @param   Mapper                  $userMapper
     * @param   AuthenticationService   $authenticationService
     */
    public function __construct( Mapper                 $userMapper,
                                 AuthenticationService  $authenticationService )
    {
        $this->setMapper( $userMapper )
             ->setAuthenticationService( $authenticationService );
    }

    /**
     * Get user by display name
     *
     * @param   string          $email
     * @param   array|object    $fields [optional]
     * @return  bool
     */
    public function isEmailAvailable( $email, $fields = array() )
    {
        $fields = (object) $fields;

        return $this->getMapper()
                    ->isEmailExists(
                          $email,
                          empty( $fields->id ) ? null : $fields->id
                      )
               ? 'user.action.register.email.taken'
               : true;
    }

    /**
     * Get user by display name
     *
     * @param   string          $displayName
     * @param   array|object    $fields [optional]
     * @return  bool
     */
    public function isDisplayNameAvailable( $displayName, $fields = array() )
    {
        $fields      = (object) $fields;
        $displayName = Structure::trimDisplayName( $displayName );

        if ( 3 > mb_strlen( $displayName ) )
        {
            return 'user.action.register.displayName.tooShort';
        }

        return $this->getMapper()
                    ->isDisplayNameExists(
                          $displayName,
                          empty( $fields->id ) ? null : $fields->id
                      )
               ? 'user.action.register.displayName.taken'
               : true;
    }

    /**
     * Get user status
     *
     * @return  object
     */
    public function status()
    {
        $auth       = $this->getAuthenticationService();
        $loggedIn   = $auth->hasIdentity();
        $identity   = $loggedIn ? $auth->getIdentity() : null;

        return (object) array(
            'loggedIn'      => $loggedIn,
            'id'            => $loggedIn ? $identity->id            : null,
            'email'         => $loggedIn ? $identity->email         : null,
            'displayName'   => $loggedIn ? $identity->displayName   : null,
        );
    }

}
