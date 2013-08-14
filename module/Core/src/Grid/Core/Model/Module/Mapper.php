<?php

namespace Grid\Core\Model\Module;

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
    protected static $tableName = 'module';

    /**
     * Default column-conversion functions as types;
     * used in selected(), deselect()
     *
     * @var array
     */
    protected static $columns = array(
        'id'        => self::INT,
        'module'    => self::STR,
        'enabled'   => self::BOOL,
    );

    /**
     * Contructor
     *
     * @param \Grid\Core\Model\Module\Structure $subDomainStructurePrototype
     */
    public function __construct( Structure $subDomainStructurePrototype = null )
    {
        parent::__construct( $subDomainStructurePrototype ?: new Structure );
    }

    /**
     * Find module by name
     *
     * @param   string  $name
     * @return  \Grid\Core\Model\Module\Structure
     */
    public function findByName( $name )
    {
        return $this->findOne( array(
            'module' => (string) $name,
        ) );
    }

}
