<?php

namespace Grid\Core\Model\Settings\Structure;

use Grid\Core\Model\Settings\StructureAbstract;
use Zork\ServiceManager\ServiceLocatorAwareTrait;
// use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * SiteDefinition
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Locale extends StructureAbstract
          implements ServiceLocatorAwareInterface
{

    use ServiceLocatorAwareTrait;

    /**
     * @const string
     */
    const ACCEPTS_SECTION   = 'locale';

    /**
     * Field: section
     *
     * @var int
     */
    protected $section      = self::ACCEPTS_SECTION;

    /**
     * Update enabled setting
     *
     * @param array|null $new
     * @param array|null $old
     * @return array|null
     */
    protected function updateEnabled( $new, $old )
    {
        $result     = array();
        $service    = $this->getServiceLocator()
                           ->get( 'Locale' );

        foreach ( $service->getAvailableFlags() as $locale => $enabled )
        {
            $result[$locale] = (string) (
                ! empty( $new[$locale] ) ||
                in_array( $locale, $new )
            );
        }

        return $result;
    }

    /**
     * Extract enabled
     *
     * @param array $enabled
     * @return array
     */
    public function extractEnabled( $enabled )
    {
        return array_keys( array_filter( $enabled ) );
    }

}
