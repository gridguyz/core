<?php

namespace Grid\Paragraph\Model\Dashboard;

use Zork\Model\LocaleAwareTrait;
use Zork\Model\LocaleAwareInterface;
use Zork\Model\MapperAwareTrait;
use Zork\Model\MapperAwareInterface;

/**
 * \Paragraph\Model\Dashboard\Model
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Model implements LocaleAwareInterface,
                       MapperAwareInterface
{

    use LocaleAwareTrait,
        MapperAwareTrait;

    /**
     * Construct model
     *
     * @param \Paragraph\Model\Dashboard\Mapper $paragraphDashboardMapper
     * @param string $locale
     */
    public function __construct( Mapper $paragraphDashboardMapper,
                                 $locale = null )
    {
        $this->setMapper( $paragraphDashboardMapper )
             ->setLocale( $locale );
    }

    /**
     * Find a structure
     *
     * @param int $id
     * @return \Paragraph\Model\Dashboard\Structure
     */
    public function find( $id )
    {
        return $this->getMapper()
                    ->find( $id );
    }

}
