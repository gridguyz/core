<?php

namespace Grid\Menu\Model\Menu;

use Zork\Mvc\AdminLocale;
use Zork\Rpc\CallableTrait;
use Zork\Rpc\CallableInterface;
use Zork\Model\MapperAwareTrait;
use Zork\Model\MapperAwareInterface;
use Grid\User\Model\Permissions\Model as PermissionsModel;

/**
 * Rpc
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 * @method \Menu\Model\Menu\Mapper getMapper()
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
     * @return \Menu\Model\Menu\Rpc
     */
    public function setUserPermissionsModel( PermissionsModel $userPermissionsModel )
    {
        $this->userPermissionsModel = $userPermissionsModel;
        return $this;
    }

    /**
     * Construct rpc
     *
     * @param \Menu\Model\Menu\Mapper $menuMapper
     * @param \User\Model\Permissions\Model $userPermissionsModel
     * @param \Zork\Mvc\AdminLocale $locale
     */
    public function __construct( Mapper $menuMapper,
                                 PermissionsModel $userPermissionsModel,
                                 AdminLocale $locale )
    {
        $this->setMapper( $menuMapper->setLocale( $locale->getCurrent() ) )
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
        if ( ! $this->getUserPermissionsModel()
                    ->isAllowed( 'menu', 'edit' ) )
        {
            return array(
                'sourceNode'    => $sourceNode,
                'position'      => $position,
                'relatedNode'   => $relatedNode,
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
     * Rename node
     *
     * @param int $sourceNode
     * @param string $label
     * @return array hash
     */
    public function renameNode( $sourceNode, $label )
    {
        if ( ! $this->getUserPermissionsModel()
                    ->isAllowed( 'menu', 'edit' ) )
        {
            return array(
                'sourceNode'    => $sourceNode,
                'label'         => $label,
                'success'       => false,
            );
        }

        $menu = $this->getMapper()
                     ->find( $sourceNode );

        if ( empty( $menu ) )
        {
            return array(
                'sourceNode'    => null,
                'label'         => $label,
                'success'       => false,
            );
        }

        $menu->label = $label;

        return array(
            'sourceNode'    => $sourceNode,
            'label'         => $label,
            'success'       => (bool) $menu->save(),
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
        if ( ! $this->getUserPermissionsModel()
                    ->isAllowed( 'menu', 'delete' ) )
        {
            return array(
                'sourceNode'    => $sourceNode,
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
