<?php

namespace Grid\User\Model\User\Group;

use Zork\Model\Mapper\DbAware\ReadWriteMapperAbstract;

/**
 * Mapper
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Mapper extends ReadWriteMapperAbstract
{

    /**
     * Table name used in all queries
     *
     * @var string
     */
    protected static $tableName = 'user_group';

    /**
     * Default column-conversion functions as types;
     * used in selected(), deselect()
     *
     * @var array
     */
    protected static $columns = array(
        'id'            => self::INT,
        'name'          => self::STR,
        'predefined'    => self::BOOL,
        'default'       => self::BOOL,
    );

    /**
     * Contructor
     *
     * @param \User\Model\User\Group\Structure $userGroupStructurePrototype
     */
    public function __construct( Structure $userGroupStructurePrototype = null )
    {
        parent::__construct( $userGroupStructurePrototype ?: new Structure );
    }

    /**
     * Get the default group
     *
     * @return \User\Model\User\Group\Structure|null
     */
    public function findDefault()
    {
        return $this->findOne( array(
            'default' => true,
        ) );
    }

}
