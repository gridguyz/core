<?php

namespace Grid\Paragraph\Model\Paragraph\Structure;

use Zork\Stdlib\DateTime;
use DateTime as BaseDateTime;
use Zend\ServiceManager\Exception\ServiceNotFoundException;

/**
 * Layout
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Layout extends AbstractRoot
{

    /**
     * Paragraph type
     *
     * @var string
     */
    protected static $type = 'layout';

    /**
     * Locale-aware properties
     *
     * @var array
     */
    protected static $localeAwareProperties = array();

    /**
     * Created
     *
     * @var \DateTime
     */
    protected $created = null;

    /**
     * Last modified
     *
     * @var \DateTime
     */
    private $_lastModified = null;

    /**
     * Input date
     *
     * @param string $date
     * @param string $format
     * @return \DateTime
     */
    protected function inputDate( $date, $format = null )
    {
        if ( empty( $date ) )
        {
            $date = null;
        }

        if ( ! $date instanceof DateTime )
        {
            if ( $date instanceof BaseDateTime )
            {
                $date = DateTime::createFromFormat(
                    DateTime::ISO8601,
                    $date->format( DateTime::ISO8601 )
                );
            }

            if ( is_int( $date ) )
            {
                $date = new DateTime( '@' . $date );
            }
            else if ( empty( $format ) )
            {
                $date = new DateTime( $date );
            }
            else
            {
                $date = DateTime::createFromFormat( $format, $date );
            }
        }

        return $date;
    }

    /**
     * Set created date
     *
     * @param \DateTime|string $date
     * @param string|null $format
     * @return \Paragraph\Model\Paragraph\Structure\Content
     */
    public function setCreated( $date, $format = null )
    {
        $this->created = $this->inputDate( $date, $format );
        return $this;
    }

    /**
     * Get last-modified date
     *
     * @return \DateTime
     */
    public function getLastModified()
    {
        return $this->_lastModified;
    }

    /**
     * Set last-modified date
     *
     * @param \DateTime|string $date
     * @param string|null $format
     * @return \Paragraph\Model\Paragraph\Structure\Content
     */
    public function setLastModified( $date, $format = null )
    {
        $this->_lastModified = $this->inputDate( $date, $format );
        return $this;
    }

    /**
     * This paragraph-type properties
     *
     * @return array
     */
    public static function getAllowedFunctions()
    {
        return array_merge(
            array_diff(
                parent::getAllowedFunctions(),
                array( static::PROPERTY_DELETE )
            ),
            array( static::PROPERTY_EDIT_LAYOUT )
        );
    }

    /**
     * @return \Paragraph\Model\Paragraph\Structure\Layout
     */
    public function prepareCreate()
    {
        if ( empty( $this->created ) )
        {
            $this->created = new DateTime();
        }

        $mapper = $this->getMapper();

        $this->bindChild( $mapper->create( array(
            'type' => 'contentPlaceholder',
        ) ) );

        return parent::prepareCreate();
    }

    /**
     * Get the rendered content
     *
     * @return mixed|null
     */
    public function getRenderedContent()
    {
        try
        {
            return $this->getServiceLocator()
                        ->get( 'RenderedContent' );
        }
        catch ( ServiceNotFoundException $ex )
        {
            return null;
        }
    }

}
