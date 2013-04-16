<?php

namespace Grid\User\Model\User\Settings;

use Zork\Model\MapperAwareTrait;
use Zork\Model\MapperAwareInterface;

/**
 * Model
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Model implements MapperAwareInterface
{

    use MapperAwareTrait;

    /**
     * Construct model
     *
     * @param \User\Model\User\Settings\Mapper $userMapper
     */
    public function __construct( Mapper $userSettingsMapper )
    {
        $this->setMapper( $userSettingsMapper );
    }

    /**
     * Find a user's settings by id & section
     *
     * @param int $userId
     * @param string $section
     * @return \User\Model\User\Settings\Structure
     */
    public function find( $userId, $section )
    {
        return $this->getMapper()
                    ->find( $userId, $section );
    }

}
