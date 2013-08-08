<?php

namespace Grid\Core\Model\Package;

use Iterator;
use Zork\Model\MapperAwareTrait;
use Zork\Model\MapperAwareInterface;

/**
 * StructureList
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class StructureList implements Iterator,
                               MapperAwareInterface
{

    use MapperAwareTrait;

    /**
     * Package names
     *
     * @var array
     */
    protected $packageNames = array();

    /**
     * Get package names
     *
     * @return  array
     */
    public function getPackageNames()
    {
        return $this->packageNames;
    }

    /**
     * Set package names
     *
     * @param   array   $packageNames
     * @return  \Grid\Core\Model\Package\StructureList
     */
    public function setPackageNames( array $packageNames )
    {
        $this->packageNames = $packageNames;
        return $this;
    }

    /**
     * Constructor
     *
     * @param   \Grid\Core\Model\Package\Mapper $packageMapper
     * @param   array                           $packageNames
     */
    public function __construct( Mapper $packageMapper, array $packageNames )
    {
        $this->setMapper( $packageMapper )
             ->setPackageNames( $packageNames );
    }

    /**
     * Rewind the Iterator to the first element
     *
     * @link    http://php.net/manual/en/iterator.rewind.php
     * @return  void    Any returned value is ignored.
     */
    public function rewind()
    {
        reset( $this->packageNames );
        return $this;
    }

    /**
     * Move forward to next element
     *
     * @link    http://php.net/manual/en/iterator.next.php
     * @return  void    Any returned value is ignored.
     */
    public function next()
    {
        next( $this->packageNames );
        return $this;
    }

    /**
     * Checks if current position is valid
     *
     * @link    http://php.net/manual/en/iterator.valid.php
     * @return  boolean     The return value will be casted to boolean and then evaluated.
     * Returns <b>TRUE</b> on success or <b>FALSE</b> on failure.
     */
    public function valid()
    {
        return (bool) current( $this->packageNames );
    }

    /**
     * Return the key of the current element
     *
     * @link    http://php.net/manual/en/iterator.key.php
     * @return  scalar  scalar on success, or <b>NULL</b> on failure.
     */
    public function key()
    {
        return current( $this->packageNames );
    }

    /**
     * Return the current element
     *
     * @link    http://php.net/manual/en/iterator.current.php
     * @return  Structure
     */
    public function current()
    {
        return $this->getMapper()
                    ->find( current( $this->packageNames ) );
    }

}
