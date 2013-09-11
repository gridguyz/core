<?php

namespace Grid\Customize\Model\Extra;

use Traversable;
use Zork\Db\SiteInfo;
use Zend\Stdlib\ArrayUtils;
use Zork\Db\SiteInfoAwareTrait;
use Zork\Db\SiteInfoAwareInterface;
use Zork\Model\MapperAwareInterface;
use Zend\Stdlib\Hydrator\HydratorInterface;

/**
 * Mapper
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Mapper implements HydratorInterface,
                        SiteInfoAwareInterface
{

    use SiteInfoAwareTrait;

    /**
     * @const string
     */
    const EXTRA_CSS_PATH_PATTERN = './public/uploads/%s/customize/extra.css';

    /**
     * Constructor
     *
     * @param   \Zork\Db\SiteInfo   $siteInfo
     */
    public function __construct( SiteInfo $siteInfo )
    {
        $this->setSiteInfo( $siteInfo );
    }

    /**
     * Get extra.css path
     *
     * @return  string
     */
    protected function getExtraCssPath()
    {
        return sprintf(
            static::EXTRA_CSS_PATH_PATTERN,
            $this->getSiteInfo()
                 ->getSchema()
        );
    }

    /**
     * Extract values from an object
     *
     * @param   object  $structure
     * @return  array
     */
    public function extract( $structure )
    {
        if ( $structure instanceof Structure )
        {
            return $structure->toArray();
        }

        if ( $structure instanceof Traversable )
        {
            return ArrayUtils::iteratorToArray( $structure );
        }

        return (array) $structure;
    }

    /**
     * Hydrate $object with the provided $data.
     *
     * @param   array   $data
     * @param   object  $structure
     * @return  object
     */
    public function hydrate( array $data, $structure )
    {
        if ( $structure instanceof Structure )
        {
            $structure->setOptions( $data );
        }
        else
        {
            foreach ( $data as $key => $value )
            {
                $structure->$key = $value;
            }
        }

        if ( $structure instanceof MapperAwareInterface )
        {
            $structure->setMapper( $this );
        }

        return $structure;
    }

    /**
     * Find (the only) structure
     *
     * @return  Structure
     */
    public function find()
    {
        $path = $this->getExtraCssPath();
        $css  = '';

        if ( is_file( $path ) )
        {
            $css = (string) @ file_get_contents( $path );
        }

        return new Structure( array(
            'css'       => $css,
            'mapper'    => $this,
        ) );
    }

    /**
     * Save (the only) structure
     *
     * @param   Structure|array|string  $structureOrCss
     * @return  int
     */
    public function save( $structureOrCss )
    {
        if ( $structureOrCss instanceof Structure )
        {
            $structureOrCss = $structureOrCss->css;
        }

        if ( $structureOrCss instanceof Traversable )
        {
            $structureOrCss = ArrayUtils::iteratorToArray( $structureOrCss );
        }

        if ( is_array( $structureOrCss ) )
        {
            if ( array_key_exists( 'css', $structureOrCss ) )
            {
                $structureOrCss = (string) $structureOrCss['css'];
            }
            else
            {
                $structureOrCss = (string) reset( $structureOrCss );
            }
        }
        else
        {
            $structureOrCss = (string) $structureOrCss;
        }

        return (int) @ file_put_contents(
            $this->getExtraCssPath(),
            $structureOrCss
        );
    }

    /**
     * Delete (the only) structure by overwriting it with the default
     *
     * @return  int
     */
    public function delete()
    {
        return $this->save( new Structure );
    }

}
