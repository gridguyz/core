<?php

namespace Grid\Menu\Model\Menu;

use Zend\Navigation;
use Zork\Iterator\DepthList;
use Zork\Model\LocaleAwareTrait;
use Zork\Model\LocaleAwareInterface;
use Zork\Model\MapperAwareTrait;
use Zork\Model\MapperAwareInterface;

/**
 * \Menu\Model\Menu\Model
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Model implements LocaleAwareInterface,
                       MapperAwareInterface
{

    use MapperAwareTrait,
        LocaleAwareTrait;

    /**
     * Construct model
     *
     * @param \Menu\Model\Menu\Mapper $menuMapper
     * @param string $locale
     */
    public function __construct( Mapper $menuMapper, $locale = null )
    {
        $this->setMapper( $menuMapper )
             ->setLocale( $locale );
    }

    /**
     * Create a structure
     *
     * @param array $data
     * @return \Menu\Model\Menu\StructureInterface
     */
    public function create( $data )
    {
        return $this->getMapper()
                    ->create( $data );
    }

    /**
     * Find a structure
     *
     * @param int $id
     * @return \Menu\Model\Menu\StructureInterface
     */
    public function find( $id )
    {
        return $this->getMapper()
                    ->find( $id );
    }

    /**
     * Find the first structure (usually default)
     *
     * @return \Menu\Model\Menu\StructureInterface
     */
    public function findFirst()
    {
        return $this->getMapper()
                    ->findFirst();
    }

    /**
     * Find render-list
     *
     * @param int|null $id
     * @return \Paragraph\Model\Paragraph\StructureInterface[]
     */
    public function findRenderList( $id = null )
    {
        return $this->getMapper()
                    ->findRenderList( $id );
    }

    /**
     * Find menus as id => label pairs
     *
     * @return array
     */
    public function findOptions()
    {
        return $this->getMapper()
                    ->findOptions(
                        array(
                            'value'         => 'id',
                            'label'         => 'label',
                            'data-level'    => 'level',
                        ),
                        array(),
                        array(
                            'left'          => 'ASC'
                        )
                    );
    }

    /**
     * Find navigation
     *
     * @param int|null $id
     * @return null|\Zend\Navigation\AbstractContainer
     */
    public function findNavigation( $id = null )
    {
        $renderList = $this->findRenderList( $id );

        if ( null !== $id && empty( $renderList ) )
        {
            return null;
        }

        $convert = new ConvertToNavigation( $renderList );
        return null === $id ? $convert->getRoot() : $convert->getLast();
    }

    /**
     * Append node $sourceNode to $newParentNode
     *
     * @param int $sourceNode
     * @param int $newParentNode
     * @return bool
     */
    public function appendTo( $sourceNode, $newParentNode )
    {
        $mapper = $this->getMapper();

        return $mapper->moveNode( $sourceNode,
                                  $mapper::MOVE_APPEND,
                                  $newParentNode );
    }

    /**
     * Interleave paragraph-nodes
     *
     * Update $updateNode's descendant menu-paragraphs
     * to be more like in $likeNode's.
     *
     * @param int $updateNode
     * @param int $likeNode
     * @return bool
     */
    public function interleaveParagraphs( $updateNode, $likeNode )
    {
        return $this->getMapper()
                    ->interleaveParagraphs( $updateNode, $likeNode );
    }

}
