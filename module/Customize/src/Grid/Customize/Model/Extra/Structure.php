<?php

namespace Grid\Customize\Model\Extra;

use Zork\Stdlib\DateTime;
use Zork\Model\Structure\MapperAwareAbstract;

/**
 * Rule structure
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Structure extends MapperAwareAbstract
{

    /**
     * Customize extra id
     *
     * @var int
     */
    protected $id = null;

    /**
     * Root paragraph id, to which bounded
     *
     * @var int
     */
    protected $rootParagraphId = null;

    /**
     * Customize extra css
     *
     * @var string
     */
    protected $extra = '';

    /**
     * Customize extra updated
     *
     * @var \DateTime
     */
    protected $updated;

    /**
     * Set root paragraph id, to which bounded
     *
     * @param int $id
     * @return \Customize\Model\Rule\Structure
     */
    public function setRootParagraphId( $id )
    {
        $this->rootParagraphId = ( (int) $id ) ?: null;
        return $this;
    }

    /**
     * Set extra
     *
     * @param string $extra
     * @return \Customize\Model\Extra\Structure
     */
    public function setExtra( $extra )
    {
        $this->extra = trim( (string) $extra );
        return $this;
    }

    /**
     * Get updated
     *
     * @return \Zork\Stdlib\DateTime
     */
    public function getUpdated()
    {
        if ( empty( $this->updated ) )
        {
            return new DateTime;
        }

        return $this->updated;
    }

    /**
     * Set updated
     *
     * @param null|int|string|\DateTime|\Zork\Stdlib\DateTime $updated
     * @return \Customize\Model\Rule\Structure
     */
    public function setUpdated( $updated )
    {
        $this->updated = DateTime::create( $updated, true );
        return $this;
    }

}