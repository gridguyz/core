<?php

namespace Grid\Core\View\Helper;

use Zend\View\Helper\AbstractHelper;

/**
 * Grid\Core\View\Helper\Uploads
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Uploads extends AbstractHelper
{

    /**
     * @const string
     */
    const URI_PREFIX = '/uploads/';

    /**
     * @var string
     */
    protected $schema;

    /**
     * Constructor
     *
     * @param string $schema
     */
    public function __construct( $schema )
    {
        $this->setSchema( $schema );
    }

    /**
     * Get schema
     *
     * @return schema
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * Set schema
     *
     * @param   string  $schema
     * @return  \Core\View\Helper\Uploads
     */
    public function setSchema( $schema )
    {
        $this->schema = (string) $schema ?: null;
        return $this;
    }

    /**
     * Invokable helper
     *
     * @param   string  $postfix
     * @return  string
     */
    public function __invoke( $postfix = '' )
    {
        if ( empty( $this->schema ) )
        {
            return null;
        }

        return static::URI_PREFIX . $this->schema . '/' . ltrim( $postfix, '/' );
    }

}
