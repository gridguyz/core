<?php

namespace Grid\Paragraph\Model\Paragraph\Structure;

/**
 * Grid\Paragraph\Model\Paragraph\Structure\AbstractContainer
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
abstract class AbstractContainer extends ProxyAbstract
{

    /**
     * Bound children (save them on save())
     *
     * @var \Paragraph\Model\Paragraph\Structure\ProxyAbstract[]
     */
    private $boundChildren = array();

    /**
     * @param \Paragraph\Model\Paragraph\Structure\ProxyAbstract $child
     * @return \Paragraph\Model\Paragraph\Structure\ProxyAbstract
     */
    protected function bindChild( ProxyAbstract $child )
    {
        $this->boundChildren[] = $child;
        return $this;
    }

    /**
     * @param int $inc
     * @return void
     */
    private function calculateLeftRight( & $inc )
    {
        $this->proxyBase()->left = ++$inc;

        foreach ( $this->boundChildren as $child )
        {
            if ( $child instanceof self )
            {
                $child->calculateLeftRight( $inc );
            }
            else
            {
                $child->proxyBase()->left   = ++$inc;
                $child->proxyBase()->right  = ++$inc;
            }
        }

        $this->proxyBase()->right = ++$inc;
    }

    /**
     * @return \Paragraph\Model\Paragraph\Structure\ProxyAbstract
     */
    public function prepareCreate()
    {
        $inc = 0;
        parent::prepareCreate();
        $this->calculateLeftRight( $inc );
        return $this;
    }

    /**
     * Save me
     *
     * @return int Number of affected rows
     */
    public function save()
    {
        $rows = parent::save();

        if ( $rows )
        {
            $rootId = $this->getRootId();

            foreach ( $this->boundChildren as $child )
            {
                $rows += $child->setRootId( $rootId )
                               ->save();
            }

            $this->boundChildren = array();
        }

        return $rows;
    }

}
