<?php

namespace Grid\Paragraph\Model\Paragraph\Structure;

/**
 * Default-fallback
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class DefaultFallback extends AbstractContainer
{

    /**
     * Paragraph-render view-open
     *
     * @var string
     */
    protected static $viewOpen = 'grid/paragraph/render/defaultFallback';

    /**
     * Locale-aware properties
     *
     * @var array
     */
    protected static $localeAwareProperties = array();

    /**
     * This paragraph can be only parent of nothing
     *
     * @var string
     */
    protected static $onlyParentOf = null;

    /**
     * Return true if and only if $options accepted by this adapter
     * If returns float as likelyhood the max of these will be used as adapter
     *
     * @param   array   $options;
     * @return  float
     */
    public static function acceptsOptions( array $options )
    {
        return 0.01;
    }

    /**
     * This paragraph-type properties
     *
     * @return  array
     */
    public static function getAllowedFunctions()
    {
        return array_diff(
            parent::getAllowedFunctions(),
            array( static::PROPERTY_EDIT )
        );
    }

    /**
     * Is the warning message should be displayed
     *
     * @return  bool
     */
    public function displayWarning()
    {
        return $this->isEditable();
    }

    /**
     * Can modify packages?
     *
     * @return  bool
     */
    public function canModifyPackages()
    {
        /* @var $package \Grid\Core\Model\Package\Model */
        /* @var $permissions \Grid\User\Model\Permissions\Model */
        $serviceLocator = $this->getServiceLocator();
        $package        = $serviceLocator->get( 'Grid\Core\Model\Package\Model' );
        $permissions    = $serviceLocator->get( 'Grid\User\Model\Permissions\Model' );

        return $package->getEnabledPackageCount()
            && $permissions->isAllowed( 'package', 'manage' );
    }

}
