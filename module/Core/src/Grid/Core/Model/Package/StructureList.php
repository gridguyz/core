<?php

namespace Grid\Core\Model\Package;

use Iterator;
use Countable;
use SeekableIterator;
use Zork\Model\MapperAwareTrait;
use Zork\Model\MapperAwareInterface;

/**
 * StructureList
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class StructureList implements Iterator,
                               Countable,
                               SeekableIterator,
                               MapperAwareInterface
{

    use MapperAwareTrait;

    /**
     * @var int
     */
    private $index = 0;

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
        $this->packageNames = array_values( $packageNames );
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
        $this->index = 0;
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
        $this->index++;
        return $this;
    }

    /**
     * Seeks to a position
     *
     * @link    http://php.net/manual/en/seekableiterator.seek.php
     * @param   int     $position The position to seek to.
     * @return  void    No value is returned.
     */
    public function seek( $position )
    {
        $this->index = $position;
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
        return isset( $this->packageNames[$this->index] );
    }

    /**
     * Return the key of the current element
     *
     * @link    http://php.net/manual/en/iterator.key.php
     * @return  scalar  scalar on success, or <b>NULL</b> on failure.
     */
    public function key()
    {
        return isset( $this->packageNames[$this->index] )
            ? $this->packageNames[$this->index]
            : null;
    }

    /**
     * Return the current element
     *
     * @link    http://php.net/manual/en/iterator.current.php
     * @return  Structure
     */
    public function current()
    {
        $name = $this->key();

        if ( $name )
        {
            return $this->getMapper()
                        ->find( $name );
        }

        return null;
    }

    /**
     * Count elements of an object
     *
     * @link    http://php.net/manual/en/countable.count.php
     * @return  int     The custom count as an integer.
     */
    public function count()
    {
        return count( $this->packageNames );
    }

}
