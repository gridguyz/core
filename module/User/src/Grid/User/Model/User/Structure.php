<?php

namespace Grid\User\Model\User;

use Zork\Db\FileTrait;
use Zork\Db\SiteInfoAwareInterface;
use Zend\Permissions\Acl;
use Zork\Stdlib\Password;
use Zork\Model\LocaleAwareTrait;
use Zork\Model\LocaleAwareInterface;
use Zork\Model\Structure\MapperAwareAbstract;

/**
 * Structure
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Structure extends MapperAwareAbstract
             implements LocaleAwareInterface,
                        SiteInfoAwareInterface,
                        Acl\Role\RoleInterface,
                        Acl\Resource\ResourceInterface
{

    use FileTrait,
        LocaleAwareTrait;

    /**
     * @var string
     */
    const DEFAULT_STATE     = 'active';

    /**
     * @var string
     */
    const DEFAULT_LOCALE    = 'en';

    /**
     * @var string
     */
    const STATE_ACTIVE      = 'active';

    /**
     * @var string
     */
    const STATE_INACTIVE    = 'inactive';

    /**
     * @var string
     */
    const STATE_BANNED     = 'banned';

    /**
     * Field: id
     *
     * @var int
     */
    protected $id;

    /**
     * Field: email
     *
     * @var string
     */
    public $email               = '';

    /**
     * Field: email
     *
     * @var string
     */
    protected $displayName      = '';

    /**
     * Field: passwordHash
     *
     * @var string
     */
    protected $passwordHash     = '';

    /**
     * Field: confirmed
     *
     * @var bool
     */
    public $confirmed           = false;

    /**
     * Field: state
     *
     * @var string
     */
    public $state               = self::DEFAULT_STATE;

    /**
     * Field: groupId
     *
     * @var int
     */
    protected $groupId;

    /**
     * Field: avatar
     *
     * @var string
     */
    protected $avatar;

    /**
     * Property: group
     *
     * @var \User\Model\User\Group\Structure
     */
    protected $group;

    /**
     * Property: acl-role id
     *
     * @var string
     */
    private $aclRoleId;

    /**
     * Property: acl-resource id
     *
     * @var string
     */
    private $aclResourceId;

    /**
     * Trim & strip display-names for setting
     *
     * @param string $displayName
     * @return string
     */
    public static function trimDisplayName( $displayName )
    {
        mb_internal_encoding( 'UTF-8' );

        return preg_replace(
            array( '/\s+/u', '/[^\s\pL\pN_-]/u' ),
            array( ' ', '' ),
            trim( $displayName )
        );
    }

    /**
     * Trim & strip display-names while setting
     *
     * @param string $displayName
     * @return \User\Model\User\Structure
     */
    public function setDisplayName( $displayName )
    {
        $this->displayName = static::trimDisplayName( $displayName );
        return $this;
    }

    /**
     * Set the passwordHash property
     *
     * @param string $password
     * @return \User\Model\User\Structure
     */
    public function setPassword( $password )
    {
        $this->passwordHash = Password::hash( $password );
        return $this;
    }

    /**
     * Verify password
     *
     * @param   string      $password
     * @param   boolean     $rehashEnabled
     * @return  boolean|int
     */
    public function verifyPassword( $password, $rehashEnabled = false )
    {
        if ( empty( $this->passwordHash ) )
        {
            return false;
        }

        $verified = Password::verify( $password, $this->passwordHash );

        if ( $verified && $rehashEnabled &&
             Password::needsRehash( $this->passwordHash ) )
        {
            $this->passwordHash = Password::hash( $password );
            $this->save();
        }

        return $verified;
    }

    /**
     * Set avatar
     *
     * @param string $avatar
     * @return \User\Model\User\Structure
     */
    public function setAvatar( $avatar )
    {
        if ( $this->avatar == $avatar )
        {
            return $this;
        }

        $this->removeFile( $this->avatar );
        $this->avatar = $this->addFile( $avatar, 'users/avatar.%s.%s' );

        return $this;
    }

    /**
     * User is in an active state
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->confirmed &&
               $this->state == static::STATE_ACTIVE;
    }

    /**
     * User is in an inactive state
     *
     * @return bool
     */
    public function isInactive()
    {
        return ! $this->confirmed ||
               $this->state == static::STATE_INACTIVE;
    }

    /**
     * User is in a banned state
     *
     * @return bool
     */
    public function isBanned()
    {
        return $this->state == static::STATE_BANNED;
    }

    /**
     * User is in an active state
     *
     * @return bool
     */
    public function makeActive()
    {
        $this->confirmed = true;
        $this->state = static::STATE_ACTIVE;
        return $this;
    }

    /**
     * User is in a banned state
     *
     * @return bool
     */
    public function makeBanned()
    {
        $this->state = static::STATE_BANNED;
        return $this;
    }

    /**
     * Set group-id
     *
     * @param int $groupId
     * @return \User\Model\User\Structure
     */
    public function setGroupId( $groupId )
    {
        $this->groupId = (int) $groupId ?: null;

        if ( $this->group && $this->group->id != $this->groupId )
        {
            $this->group = null;
        }

        return $this;
    }

    /**
     * Get group
     *
     * @return \User\Model\User\Group\Structure
     */
    public function getGroup()
    {
        if ( null === $this->group && $this->groupId )
        {
            $this->group = $this->getMapper()
                                ->getUserGroupMapper()
                                ->find( $this->groupId );
        }

        return $this->group;
    }

    /**
     * Set group
     *
     * @param \User\Model\User\Group\Structure $group
     * @return \User\Model\User\Structure
     */
    public function setGroup( Group\Structure $group = null )
    {
        if ( null === $group )
        {
            $this->groupId  = null;
            $this->group    = null;
        }
        else
        {
            $this->groupId  = $group->id;
            $this->group    = $group;
        }

        $this->aclRole = null;
        return $this;
    }

    /**
     * Returns the string identifier of the Role
     *
     * @return string
     */
    public function getRoleId()
    {
        if ( null === $this->aclRoleId )
        {
            $this->aclRoleId = ( (int) $this->groupId )
                . '.' . ( (int) $this->id );
        }

        return $this->aclRoleId;
    }

    /**
     * Returns the string identifier of the Role
     *
     * @return string
     */
    public function getResourceId()
    {
        if ( null === $this->aclResourceId )
        {
            $this->aclResourceId = 'user.group.' . ( (int) $this->groupId )
                . '.identity.' . ( (int) $this->id );
        }

        return $this->aclResourceId;
    }

    /**
     * Activate this user
     *
     * @return int Number of rows affected
     */
    public function activate()
    {
        return $this->makeActive()
                    ->save();
    }

    /**
     * Ban this user
     *
     * @return int Number of rows affected
     */
    public function ban()
    {
        return $this->makeBanned()
                    ->save();
    }

    /**
     * Get url for view user
     *
     * @param   string|null $locale
     * @return  string
     */
    public function getUri( $locale = null )
    {
        if ( empty( $locale ) )
        {
            $locale = $this->getLocale();
        }

        return '/app/'
             . $locale
             . '/user/view/'
             . rawurlencode( $this->displayName );
    }

    /**
     * String representation of object
     *
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or <b>null</b>
     */
    public function serialize()
    {
        // Force to serialize the group & the role objects too
        $this->getGroup();
        $this->getRoleId();
        $this->getResourceId();
        return parent::serialize();
    }

}
