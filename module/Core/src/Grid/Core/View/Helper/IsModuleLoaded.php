<?php

namespace Grid\Core\View\Helper;

use Traversable;
use Zend\Stdlib\ArrayUtils;
use Zend\View\Helper\AbstractHelper;
use Zend\ModuleManager\ModuleManagerInterface;

/**
 * Grid\Core\View\Helper\IsModuleLoaded
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class IsModuleLoaded extends AbstractHelper
{

    /**
     * @var \Zend\ModuleManager\ModuleManagerInterface
     */
    protected $moduleManager;

    /**
     * @return  \Zend\ModuleManager\ModuleManagerInterface
     */
    public function getModuleManager()
    {
        return $this->moduleManager;
    }

    /**
     * @param   \Zend\ModuleManager\ModuleManagerInterface $moduleManager
     * @return  \Core\View\Helper\AppService
     */
    public function setModuleManager( ModuleManagerInterface $moduleManager )
    {
        $this->moduleManager = $moduleManager;
        return $this;
    }

    /**
     * Constructor
     *
     * @param   \Zend\ModuleManager\ModuleManagerInterface $moduleManager
     */
    public function __construct( ModuleManagerInterface $moduleManager )
    {
        $this->setModuleManager( $moduleManager );
    }

    /**
     * Is module loaded
     *
     * @param   string  $module
     * @return  bool
     */
    public function isModuleLoaded( $module )
    {
        return in_array(
            trim( $module ),
            $this->getModuleManager()
                 ->getModules()
        );
    }

    /**
     * Is all modules loaded
     *
     * @param   array   $modules
     * @return  bool
     */
    protected function isAllModulesLoaded( array $modules )
    {
        foreach ( $modules as $module )
        {
            if ( ! $this->isModuleLoaded( $module ) )
            {
                return false;
            }
        }

        return true;
    }

    /**
     * Is any modules loaded
     *
     * @param   string|array    $modules
     * @return  bool
     */
    public function isModulesLoaded( $modules )
    {
        if ( $modules instanceof Traversable )
        {
            $modules = ArrayUtils::iteratorToArray( $modules );
        }

        if ( ! is_array( $modules ) )
        {
            $modules = preg_split( '/\|{1,2}/', (string) $modules );
        }

        foreach ( $modules as $module )
        {
            if ( ! is_array( $module ) )
            {
                $module = preg_split( '/&{1,2}/', (string) $module );
            }

            if ( $this->isAllModulesLoaded( $module ) )
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Invokable helper
     *
     * @param   string|array $modules
     * @return  \Zend\ServiceManager\ServiceLocatorInterface|mixed
     */
    public function __invoke( $modules )
    {
        return $this->isModulesLoaded( $modules );
    }

}
