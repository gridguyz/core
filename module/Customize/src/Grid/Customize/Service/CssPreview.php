<?php

namespace Grid\Customize\Service;

use Zend\Session\ManagerInterface;
use Zork\Session\ContainerAwareTrait;

/**
 * CssPreview
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class CssPreview
{

    use ContainerAwareTrait;

    /**
     * Constructor
     *
     * @param   ManagerInterface  $sessionManager
     */
    public function __construct( ManagerInterface $sessionManager )
    {
        $this->setSessionManager( $sessionManager );
    }

    /**
     * @param   null|string $id
     * @return  string
     */
    public function hasPreviewById( $id )
    {
        $id         = (string) $id;
        $container  = $this->getSessionContainer();
        return isset( $container[$id] );
    }

    /**
     * @param   null|string $id
     * @return  string
     */
    public function getPreviewById( $id, $default = null )
    {
        $id         = (string) $id;
        $container  = $this->getSessionContainer();
        return isset( $container[$id] ) ? $container[$id] : $default;
    }

    /**
     * @param   null|string $id
     * @return  CssPreview
     */
    public function setPreviewById( $id, $value )
    {
        $id             = (string) $id;
        $container      = $this->getSessionContainer();
        $container[$id] = (string) $value;
        return $this;
    }

    /**
     * @param   null|string $id
     * @return  CssPreview
     */
    public function unsetPreviewById( $id )
    {
        $container = $this->getSessionContainer();
        unset( $container[$id] );
        return $this;
    }

    /**
     * @return  CssPreview
     */
    public function unsetPreviews()
    {
        $this->getSessionContainer()
             ->exchangeArray( array() );

        return $this;
    }

    /**
     * @return  array
     */
    public function getPreviews()
    {
        return $this->getSessionContainer()
                    ->getArrayCopy();
    }

}
