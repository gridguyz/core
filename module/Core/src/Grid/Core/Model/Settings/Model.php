<?php

namespace Grid\Core\Model\Settings;

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
     * @param \Core\Model\Settings\Mapper $settingsMapper
     */
    public function __construct( Mapper $settingsMapper )
    {
        $this->setMapper( $settingsMapper );
    }

    /**
     * Find a settings by section
     *
     * @param string $section
     * @return \Core\Model\Settings\Structure
     */
    public function find( $section )
    {
        return $this->getMapper()
                    ->find( $section );
    }

    /**
     * Save a settings
     *
     * @param \Core\Model\Settings\Structure $settings
     * @return int
     */
    public function save( Structure $settings )
    {
        return $this->getMapper()
                    ->save( $settings );
    }

}
