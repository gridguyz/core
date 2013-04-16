<?php

namespace Grid\Paragraph\Model\Paragraph;

use Zork\Rpc\CallableTrait;
use Zork\Rpc\CallableInterface;
use Zork\Model\MapperAwareTrait;
use Zork\Model\MapperAwareInterface;
use Grid\User\Model\Permissions\Model as PermissionsModel;

/**
 * Rpc
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 * @method \Paragraph\Model\Paragraph\Mapper getMapper()
 */
class Rpc implements CallableInterface,
                     MapperAwareInterface
{

    use CallableTrait,
        MapperAwareTrait;

    /**
     * @var \User\Model\Permissions\Model
     */
    protected $userPermissionsModel;

    /**
     * @return \User\Model\Permissions\Model
     */
    public function getUserPermissionsModel()
    {
        return $this->userPermissionsModel;
    }

    /**
     * @param \User\Model\Permissions\Model $userPermissionsModel
     * @return \Paragraph\Model\Paragraph\Rpc
     */
    public function setUserPermissionsModel( PermissionsModel $userPermissionsModel )
    {
        $this->userPermissionsModel = $userPermissionsModel;
        return $this;
    }

    /**
     * Construct rpc
     *
     * @param \Paragraph\Model\Paragraph\Mapper $paragraphMapper
     * @param \User\Model\Permissions\Model $userPermissionsModel
     */
    public function __construct( Mapper $paragraphMapper,
                                 PermissionsModel $userPermissionsModel )
    {
        $this->setMapper( $paragraphMapper )
             ->setUserPermissionsModel( $userPermissionsModel );
    }

    /**
     * Move node
     *
     * @param int $sourceNode
     * @param string $position
     * @param int $relatedNode
     * @return array hash
     */
    public function moveNode( $sourceNode, $position, $relatedNode )
    {
        $sourceNode = (int) $sourceNode;
        $sourceRoot = $this->getMapper()
                           ->findRootOf( $sourceNode );

        if ( empty( $sourceRoot ) ||
             ! $sourceRoot->isEditable() )
        {
            return array(
                'sourceNode'    => null,
                'position'      => $position,
                'relatedNode'   => $relatedNode,
                'success'       => false,
            );
        }

        $relatedNode = (int) $relatedNode;
        $relatedRoot = $this->getMapper()
                            ->findRootOf( $relatedNode );

        if ( empty( $relatedRoot ) ||
             ! $relatedRoot->isEditable() )
        {
            return array(
                'sourceNode'    => $sourceNode,
                'position'      => $position,
                'relatedNode'   => null,
                'success'       => false,
            );
        }

        $success = $this->getMapper()
                        ->moveNode( $sourceNode,
                                    strtolower( $position ),
                                    $relatedNode );

        return array(
            'sourceNode'    => $sourceNode,
            'position'      => $position,
            'relatedNode'   => $relatedNode,
            'success'       => (bool) $success,
        );
    }

    /**
     * Delete node
     *
     * @param int $sourceNode
     * @return array hash
     */
    public function deleteNode( $sourceNode )
    {
        $sourceNode = (int) $sourceNode;
        $sourceRoot = $this->getMapper()
                           ->findRootOf( $sourceNode );

        if ( empty( $sourceRoot ) ||
             ! $sourceRoot->isEditable() )
        {
            return array(
                'sourceNode'    => null,
                'success'       => false,
            );
        }

        $success = $this->getMapper()
                        ->delete( $sourceNode );

        return array(
            'sourceNode'    => $sourceNode,
            'success'       => (bool) $success,
        );
    }

}
