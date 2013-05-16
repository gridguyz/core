<?php

namespace Grid\Paragraph\Model\Paragraph;

use Traversable;
use Zend\Paginator;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Predicate;
use Zend\Db\Sql\Expression;
use Zend\Stdlib\ArrayUtils;
use Zork\Model\LocaleAwareTrait;
use Zork\Model\LocaleAwareInterface;
use Zork\Model\MapperAwareTrait;
use Zork\Model\MapperAwareInterface;
use Zork\Db\Sql\Predicate\TypedParameters;
use Zend\Authentication\AuthenticationService;
use Grid\User\Model\Permissions\Model as PermissionsModel;

/**
 * \Paragraph\Model\Paragraph\Model
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Model implements MapperAwareInterface,
                       LocaleAwareInterface
{

    use MapperAwareTrait,
        LocaleAwareTrait;

    /**
     * Permissions model
     *
     * @var \User\Model\Permissions\Model
     */
    protected $permissionsModel;

    /**
     * Get permissions model
     *
     * @return  \User\Model\Permissions\Model
     */
    public function getPermissionsModel()
    {
        return $this->permissionsModel;
    }

    /**
     * Set permissions model
     *
     * @param   \User\Model\Permissions\Model       $permissionsModel
     * @return  \Paragraph\Model\Paragraph\Structure\ProxyBase
     */
    public function setPermissionsModel( PermissionsModel $permissionsModel )
    {
        $this->permissionsModel = $permissionsModel;
        return $this;
    }

    /**
     * Construct model
     *
     * @param   \User\Model\Permissions\Model       $permissionsModel
     * @param   \Paragraph\Model\Paragraph\Mapper   $paragraphMapper
     * @param   string                              $locale
     */
    public function __construct( PermissionsModel $permissionsModel,
                                 Mapper           $paragraphMapper,
                                 $locale = null )
    {
        $this->setPermissionsModel( $permissionsModel )
             ->setMapper( $paragraphMapper )
             ->setLocale( $locale );
    }

    /**
     * Create a structure
     *
     * @param   array $data
     * @return  \Paragraph\Model\Paragraph\StructureInterface
     */
    public function create( $data )
    {
        return $this->getMapper()
                    ->create( $data );
    }

    /**
     * Find a structure
     *
     * @param   int $id
     * @return  \Paragraph\Model\Paragraph\StructureInterface
     */
    public function find( $id )
    {
        return $this->getMapper()
                    ->find( $id );
    }

    /**
     * Find paragraphs as "$id" => "$name" pairs
     *
     * @param   string              $type
     * @param   string|null         $schema
     * @param   array|string|null   $firstOrder
     * @return  array
     */
    public function findOptions( $type, $schema = null, $firstOrder = null )
    {
        $mapper = $this->getMapper();
        $order  = array(
            'name'      => 'ASC',
            'rootId'    => 'ASC',
            'left'      => 'ASC',
        );

        if ( null !== $schema )
        {
            $mapper = clone $mapper;
            $mapper->setDbSchema( $schema );
        }

        if ( null !== $firstOrder )
        {
            if ( ! is_array( $firstOrder ) )
            {
                $firstOrder = array( $firstOrder => 'ASC' );
            }

            $order = array_merge( $firstOrder, $order );
        }

        return $mapper->findOptions(
            array(
                'value'                 => 'id',
                'label'                 => 'name',
                'data-title-text'       => 'title',
                'data-created'          => 'created',
                'data-lead-image'       => 'leadImage',
                'data-last-modified'    => 'lastModified',
            ),
            array(
                'type'                  => (string) $type
            ),
            $order
        );
    }

    /**
     * Find render-list
     *
     * @param   int|string $idOrMetaName
     * @return  \Paragraph\Model\Paragraph\StructureInterface[]
     */
    public function findRenderList( $idOrMetaName )
    {
        return $this->getMapper()
                    ->findRenderList( $idOrMetaName );
    }

    /**
     * Get paginator for listing
     *
     * @param   string $type
     * @return  \Zend\Paginator\Paginator
     */
    public function getPaginator( $type )
    {
        $type = (string) $type;

        switch ( $type )
        {
            case 'content':
                $joins = array(
                    'paragraph_property_layout' => array(
                        'table'     => array( 'paragraph_property_layout' => 'paragraph_property' ),
                        'where'     => new Expression(
                            '?.? = ?.? AND ?.? = ?',
                            array(
                                'paragraph',                 'id',
                                'paragraph_property_layout', 'paragraphId',
                                'paragraph_property_layout', 'name',
                                'layoutId',
                            ),
                            array(
                                Expression::TYPE_IDENTIFIER, Expression::TYPE_IDENTIFIER,
                                Expression::TYPE_IDENTIFIER, Expression::TYPE_IDENTIFIER,
                                Expression::TYPE_IDENTIFIER, Expression::TYPE_IDENTIFIER,
                                Expression::TYPE_VALUE,
                            )
                        ),
                        'columns'   => array(),
                        'type'      => Select::JOIN_LEFT,
                    ),
                    'paragraph_layout' => array(
                        'table'     => array( 'paragraph_layout' => 'paragraph' ),
                        'where'     => new Expression(
                            'CAST( CASE
                                     WHEN ?.? = \'\' THEN NULL
                                     ELSE ?.?
                                   END AS INT ) = ?.?',
                            array(
                                'paragraph_property_layout', 'value',
                                'paragraph_property_layout', 'value',
                                'paragraph_layout',          'id',
                            ),
                            array(
                                Expression::TYPE_IDENTIFIER, Expression::TYPE_IDENTIFIER,
                                Expression::TYPE_IDENTIFIER, Expression::TYPE_IDENTIFIER,
                            )
                        ),
                        'columns'   => array( 'layoutName' => 'name' ),
                        'type'      => Select::JOIN_LEFT,
                    ),
                );
                $columns = array(
                    'defaultFor' => new Expression(
                        '( SELECT COUNT(*) FROM ? WHERE ?.? = ?.? )',
                        array(
                            'subdomain',
                            'subdomain', 'defaultContentId',
                            'paragraph', 'id',
                        ),
                        array(
                            Expression::TYPE_IDENTIFIER,
                            Expression::TYPE_IDENTIFIER, Expression::TYPE_IDENTIFIER,
                            Expression::TYPE_IDENTIFIER, Expression::TYPE_IDENTIFIER,
                        )
                    ),
                );
                break;

            case 'layout':
                $joins   = null;
                $columns = array(
                    'defaultFor' => new Expression(
                        '( SELECT COUNT(*) FROM ? WHERE ?.? = ?.? )',
                        array(
                            'subdomain',
                            'subdomain', 'defaultLayoutId',
                            'paragraph', 'id',
                        ),
                        array(
                            Expression::TYPE_IDENTIFIER,
                            Expression::TYPE_IDENTIFIER, Expression::TYPE_IDENTIFIER,
                            Expression::TYPE_IDENTIFIER, Expression::TYPE_IDENTIFIER,
                        )
                    ),
                );
                break;

            case 'widget':
                $joins  = array(
                    'paragraph_content' => array(
                        'table'     => array( 'paragraph_content' => 'paragraph' ),
                        'where'     => 'paragraph.rootId = paragraph_content.id',
                        'columns'   => array(
                            'rootName'  => 'name',
                            'rootType'  => 'type',
                        ),
                        'type'      => Select::JOIN_LEFT,
                    ),
                );
                $columns = null;
                break;

            default:
                $joins   = null;
                $columns = null;
                break;
        }

        return $this->getMapper()
                    ->getPaginator(
                        array( 'paragraph.type' => $type ),
                        array( 'rootId'         => 'ASC',
                               'left'           => 'ASC' ),
                        $columns,
                        $joins
                    );
    }

    /**
     * Add joins for properties
     *
     * @param array $joins
     * @param array $properties
     */
    private function addJoinsForProperties( array &$joins, array $properties )
    {
        foreach ( $properties as $property )
        {
            $joins['paragraph_property_' . $property] = array(
                'table'     => array(
                    'paragraph_property_' . $property => 'paragraph_property'
                ),
                'where'     => new TypedParameters(
                    '?.? = ?.? AND ?.? = ?',
                    array(
                        'paragraph',
                        'id',
                        'paragraph_property_' . $property,
                        'paragraphId',
                        'paragraph_property_' . $property,
                        'name',
                        $property,
                    ),
                    array(
                        TypedParameters::TYPE_IDENTIFIER,
                        TypedParameters::TYPE_IDENTIFIER,
                        TypedParameters::TYPE_IDENTIFIER,
                        TypedParameters::TYPE_IDENTIFIER,
                        TypedParameters::TYPE_IDENTIFIER,
                        TypedParameters::TYPE_IDENTIFIER,
                        TypedParameters::TYPE_VALUE,
                    )
                ),
                'columns'   => array( $property => 'value' ),
                'type'      => Select::JOIN_LEFT,
            );
        }
    }

    /**
     * Add joins for properties
     *
     * @param array $joins
     * @param array $properties
     */
    private function addJoinForArrayProperty( array &$joins, $property, $value )
    {
        $joins['paragraph_property_' . $property] = array(
            'table'     => array(
                'paragraph_property_' . $property => 'paragraph_property'
            ),
            'where'     => new TypedParameters(
                '?.? = ?.? AND ?.? LIKE ? AND ?.? = ?',
                array(
                    'paragraph',
                    'id',
                    'paragraph_property_' . $property,
                    'paragraphId',
                    'paragraph_property_' . $property,
                    'name',
                    $property . '.%',
                    'paragraph_property_' . $property,
                    'value',
                    $value,
                ),
                array(
                    TypedParameters::TYPE_IDENTIFIER,
                    TypedParameters::TYPE_IDENTIFIER,
                    TypedParameters::TYPE_IDENTIFIER,
                    TypedParameters::TYPE_IDENTIFIER,
                    TypedParameters::TYPE_IDENTIFIER,
                    TypedParameters::TYPE_IDENTIFIER,
                    TypedParameters::TYPE_VALUE,
                    TypedParameters::TYPE_IDENTIFIER,
                    TypedParameters::TYPE_IDENTIFIER,
                    TypedParameters::TYPE_VALUE,
                )
            ),
            'columns'   => array(),
            'type'      => Select::JOIN_LEFT,
        );
    }

    /**
     * Get paginator for listing
     *
     * @param   string|array    $tags
     * @param   bool            $all
     * @return  \Zend\Paginator\Paginator
     */
    public function getContentPaginatorByTags( $tags, $all = false )
    {
        if ( empty( $tags ) )
        {
            return new Paginator\Paginator(
                new Paginator\Adapter\Null()
            );
        }

        $this;

        $quantifier = null;
        $columns    = array();
        $joins      = array();
        $where      = array( 'paragraph.type' => 'content' );

        if ( $tags instanceof Traversable )
        {
            $tags = ArrayUtils::iteratorToArray( $tags );
        }
        else
        {
            $tags = (array) $tags;
        }

        if ( $all || count( $tags ) === 1 )
        {
            $i = 0;

            foreach ( $tags as $tag )
            {
                $i++;

                $joins['paragraph_x_tag_' . $i] = array(
                    'table'     => array( 'paragraph_x_tag_' . $i => 'paragraph_x_tag' ),
                    'where'     => 'paragraph.id = paragraph_x_tag_' . $i . '.paragraphId',
                    'columns'   => array(),
                    'type'      => Select::JOIN_INNER,
                );

                $joins['tag_' . $i] = array(
                    'table'     => array( 'tag_' . $i => 'tag' ),
                    'where'     => 'paragraph_x_tag_' . $i . '.tagId = tag_' . $i . '.id',
                    'columns'   => array(),
                    'type'      => Select::JOIN_INNER,
                );

                $where['tag_' . $i . '.name'] = $tag;
            }
        }
        else
        {
            $joins['paragraph_x_tag'] = array(
                'table'     => 'paragraph_x_tag',
                'where'     => 'paragraph.id = paragraph_x_tag.paragraphId',
                'columns'   => array(),
                'type'      => Select::JOIN_INNER,
            );

            $joins['tag'] = array(
                'table'     => 'tag',
                'where'     => 'paragraph_x_tag.tagId = tag.id',
                'columns'   => array(),
                'type'      => Select::JOIN_INNER,
            );

            $where[] = new Predicate\In( 'tag.name', $tags );

            $columns['created'] = new Expression(
                'CAST( ?.? AS TIMESTAMP WITH TIME ZONE )',
                array(
                    'paragraph_property_created',
                    'value',
                ),
                array(
                    Expression::TYPE_IDENTIFIER,
                    Expression::TYPE_IDENTIFIER,
                )
            );

            $quantifier = 'DISTINCT';
        }

        $permissions = $this->getPermissionsModel();

        if ( ! $permissions->isAllowed( 'paragraph.content', 'view' ) )
        {
            $auth = new AuthenticationService();
            $user = $auth->hasIdentity() ? $auth->getIdentity() : null;
            $pub  = array();

            $this->addJoinsForProperties( $joins, array(
                'allAccess',
                'published',
                'publishedFrom',
                'publishedTo',
            ) );

            $pub[] = new Predicate\Operator(
                'paragraph_property_published.value',
                Predicate\Operator::OP_EQ,
                '1'
            );

            $pub[] = new Predicate\PredicateSet( array(
                new Predicate\IsNull(
                    'paragraph_property_publishedFrom.value'
                ),
                new Predicate\Operator(
                    'paragraph_property_publishedFrom.value',
                    Predicate\Operator::OP_EQ,
                    ''
                ),
                new TypedParameters(
                    'CAST( ?.? AS TIMESTAMP WITH TIME ZONE ) >= CURRENT_TIMESTAMP',
                    array(
                        'paragraph_property_publishedFrom',
                        'value',
                    ),
                    array(
                        TypedParameters::TYPE_IDENTIFIER,
                        TypedParameters::TYPE_IDENTIFIER,
                    )
                )
            ), Predicate\PredicateSet::OP_OR );

            $pub[] = new Predicate\PredicateSet( array(
                new Predicate\IsNull(
                    'paragraph_property_publishedTo.value'
                ),
                new Predicate\Operator(
                    'paragraph_property_publishedTo.value',
                    Predicate\Operator::OP_EQ,
                    ''
                ),
                new TypedParameters(
                    'CAST( ?.? AS TIMESTAMP WITH TIME ZONE ) <= CURRENT_TIMESTAMP',
                    array(
                        'paragraph_property_publishedTo',
                        'value',
                    ),
                    array(
                        TypedParameters::TYPE_IDENTIFIER,
                        TypedParameters::TYPE_IDENTIFIER,
                    )
                )
            ), Predicate\PredicateSet::OP_OR );

            if ( $user )
            {
                $this->addJoinForArrayProperty( $joins, 'editGroups', $user->groupId );
                $this->addJoinForArrayProperty( $joins, 'editUsers',  $user->id      );

                $pub = array(
                    new Predicate\PredicateSet(
                        array(
                            new Predicate\PredicateSet(
                                $pub,
                                Predicate\PredicateSet::OP_AND
                            ),
                            new Predicate\IsNotNull(
                                'paragraph_property_editGroups.value'
                            ),
                            new Predicate\IsNotNull(
                                'paragraph_property_editUsers.value'
                            ),
                        ),
                        Predicate\PredicateSet::OP_OR
                    ),
                );
            }

            $where = array_merge( $where, $pub );

            $this->addJoinForArrayProperty( $joins, 'accessGroups', $user ? $user->groupId : '' );
            $this->addJoinForArrayProperty( $joins, 'accessUsers',  $user ? $user->id      : '' );

            $where[] = new Predicate\PredicateSet( array(
                new Predicate\Operator(
                    'paragraph_property_allAccess.value',
                    Predicate\Operator::OP_EQ,
                    '1'
                ),
                new Predicate\IsNotNull(
                    'paragraph_property_accessGroups.value'
                ),
                new Predicate\IsNotNull(
                    'paragraph_property_accessUsers.value'
                ),
            ), Predicate\PredicateSet::OP_OR );
        }

        $this->addJoinsForProperties( $joins, array( 'created' ) );

        return $this->getMapper()
                    ->getPaginator(
                        $where,
                        array(
                            new Expression(
                                'CAST( ?.? AS TIMESTAMP WITH TIME ZONE ) DESC',
                                array(
                                    'paragraph_property_created',
                                    'value',
                                ),
                                array(
                                    Expression::TYPE_IDENTIFIER,
                                    Expression::TYPE_IDENTIFIER,
                                )
                            ),
                        ),
                        $columns,
                        $joins,
                        $quantifier
                    );
    }

    /**
     * Append node $sourceNode to $newParentNode
     *
     * @param   int     $sourceNode
     * @param   string  $position
     * @param   int     $relatedNode
     * @return  bool
     */
    public function appendTo( $sourceNode, $newParentNode )
    {
        $mapper = $this->getMapper();

        return $mapper->moveNode( $sourceNode,
                                  $mapper::MOVE_APPEND,
                                  $newParentNode );
    }

    /**
     * Clone a node (with properties & customize)
     *
     * @param   int         $sourceNode
     * @param   string|null $sourceSchema optional, default: current schema
     * @return  int         Cloned paragraph-id
     */
    public function cloneFrom( $sourceNode, $sourceSchema = null )
    {
        return $this->getMapper()
                    ->cloneNode( $sourceNode, $sourceSchema );
    }

}
