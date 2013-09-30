<?php

namespace Grid\Core\View\Helper;

use Zork\Stdlib\String;
use Zork\Paginator\Adapter\DbSelect;
use Zend\Db\Sql\Predicate;
use Zend\Db\Sql\Expression;
use Zork\Db\Sql\Predicate\ILike;
use Zend\Paginator\Paginator;
use Zend\View\Helper\AbstractHelper;
use Zend\View\Helper\HelperInterface;
use Zend\Session\Container as SessionContainer;

/**
 * RowSet view-helper
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class RowSet extends AbstractHelper
{

    /**
     * @var int
     */
    const FLAGS_DEFAULT             = 0;

    /**
     * @var int
     */
    const FLAG_LAYOUT_FILTERING     = 1;

    /**
     * @var int
     */
    const FLAG_LAYOUT_AJAX          = 2;

    /**
     * @var int
     */
    const FLAG_LAYOUT_ALL           = 3;

    /**
     * @var string
     */
    const BOOL                      = 'Bool';

    /**
     * @var string
     */
    const CALLBACK                  = 'Callback';

    /**
     * @var string
     */
    const CURRENCY                  = 'Currency';

    /**
     * @var string
     */
    const DATE                      = 'Date';

    /**
     * @var string
     */
    const DATETIME                  = 'DateTime';

    /**
     * @var string
     */
    const ENUM                      = 'Enum';

    /**
     * @var string
     */
    const FLOAT                     = 'Float';

    /**
     * @var string
     */
    const HTML                      = 'Html';

    /**
     * @var string
     */
    const INT                       = 'Int';

    /**
     * @var string
     */
    const LOCALE                    = 'Locale';

    /**
     * @var string
     */
    const SET                       = 'Set';

    /**
     * @var string
     */
    const STRING                    = 'String';

    /**
     * @var string
     */
    const REPLACE                   = 'Replace';

    /**
     * @var string
     */
    const TEXT                      = 'Text';

    /**
     * @var string
     */
    const TIME                      = 'Time';

    /**
     * @var string
     */
    const TRANSLATE                 = 'Translate';

    /**
     * @var string
     */
    const THUMBNAIL                 = 'Thumbnail';

    /**
     * @var string
     */
    const STR                       = self::STRING;

    /**
     * @var string
     */
    const CHAR                      = self::STRING;

    /**
     * @var string
     */
    const VARCHAR                   = self::STRING;

    /**
     * @var string
     */
    const INTEGER                   = self::INT;

    /**
     * @var string
     */
    const REAL                      = self::FLOAT;

    /**
     * @var string
     */
    const DOUBLE                    = self::FLOAT;

    /**
     * @var string
     */
    const BOOLEAN                   = self::BOOL;

    /**
     * @var string
     */
    const TIMESTAMP                 = self::DATETIME;

    /**
     * @var string
     */
    const TIMESTAMP_TZ              = self::DATETIME;

    /**
     * @var string
     */
    const CHARACTER_VARYING         = self::STRING;

    /**
     * @var int
     */
    protected $flags;

    /**
     * @var \Zend\Paginator\Paginator
     */
    protected $paginator;

    /**
     * @var bool
     */
    protected $columnsUseTranslation        = true;

    /**
     * @var string
     */
    protected $columnTranslatePrefix        = 'default.column';

    /**
     * @var string
     */
    protected $columnTranslatePostfix       = '';

    /**
     * @var string
     */
    protected $columnTranslateTextDomain    = 'default';

    /**
     * @var string
     */
    protected $id;

    /**
     * @var array
     */
    protected $columns                      = array();

    /**
     * @var array
     */
    protected $icons                        = array();

    /**
     * @var int
     */
    protected $page                         = 0;

    /**
     * @var int
     */
    protected $pageRange                    = 7;

    /**
     * @var int
     */
    protected $itemCountPerPage             = 15;

    /**
     * Hidden columns
     *
     * @var array
     */
    protected $hiddenColumns                = array();

    /**
     * Default search
     *
     * @var array
     */
    protected $defaultSearch                = array();

    /**
     * Default orders
     *
     * @var array
     */
    protected $defaultOrders                = array();

    /**
     * Not set labels
     *
     * @var array
     */
    protected $notSetLabels                 = array();

    /**
     * Empty labels
     *
     * @var array
     */
    protected $emptyLabels                  = array();

    /**
     * @var \Zend\Session\Container
     */
    protected $store;

    /**
     * @var \Zend\Http\Request
     */
    protected $request;

    /**
     * Request is pared or not
     *
     * @var array
     */
    protected $requestParsed                = false;

    /**
     * Get flags
     *
     * @return int
     */
    public function getFlags()
    {
        return $this->flags;
    }

    /**
     * Set flags
     *
     * @param int $flags
     * @return \Core\View\Helper\RowSet
     */
    public function setFlags( $flags = self::FLAGS_DEFAULT )
    {
        $this->flags = (int) $flags;
        return $this;
    }

    /**
     * Has flags
     *
     * @param int $flags
     * @return bool
     */
    public function hasFlags( $flags )
    {
        return (bool) ( $this->flags & (int) $flags );
    }

    /**
     * Add flags
     *
     * @param int $flags
     * @return \Core\View\Helper\RowSet
     */
    public function addFlags( $flags )
    {
        $this->flags |= (int) $flags;
        return $this;
    }

    /**
     * @return \Zend\Paginator\Paginator
     */
    public function getPaginator()
    {
        return $this->paginator;
    }

    /**
     * @param \Zend\Paginator\Paginator $paginator
     * @return \Core\View\Helper\RowSet
     */
    public function setPaginator( Paginator $paginator )
    {
        $this->paginator = $paginator;
        return $this;
    }

    /**
     * @return bool
     */
    public function getColumnsUseTranslation()
    {
        return $this->columnsUseTranslation;
    }

    /**
     * @param bool $columnsUseTranslation
     * @return \Core\View\Helper\RowSet
     */
    public function setColumnsUseTranslation( $columnsUseTranslation = true )
    {
        $this->columnsUseTranslation = (bool) $columnsUseTranslation;
        return $this;
    }

    /**
     * @return string
     */
    public function getColumnTranslatePrefix()
    {
        return $this->columnTranslatePrefix;
    }

    /**
     * @param string $columnTranslatePrefix
     * @return \Core\View\Helper\RowSet
     */
    public function setColumnTranslatePrefix( $columnTranslatePrefix )
    {
        $this->columnTranslatePrefix = (string) $columnTranslatePrefix;
        return $this;
    }

    /**
     * @return string
     */
    public function getColumnTranslatePostfix()
    {
        return $this->columnTranslatePostfix;
    }

    /**
     * @param string $columnTranslatePostfix
     * @return \Core\View\Helper\RowSet
     */
    public function setColumnTranslatePostfix( $columnTranslatePostfix )
    {
        $this->columnTranslatePostfix = (string) $columnTranslatePostfix;
        return $this;
    }

    /**
     * @return string
     */
    public function getColumnTranslateTextDomain()
    {
        return $this->columnTranslateTextDomain;
    }

    /**
     * @param string $columnTranslateTextDomain
     * @return \Core\View\Helper\RowSet
     */
    public function setColumnTranslateTextDomain( $columnTranslateTextDomain )
    {
        $this->columnTranslateTextDomain = (string) $columnTranslateTextDomain;
        return $this;
    }

    /**
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param int $page
     * @return \Core\View\Helper\RowSet
     */
    public function setPage( $page )
    {
        $this->page = (int) $page;
        return $this;
    }

    /**
     * @return array
     */
    public function getIcons()
    {
        return $this->icons;
    }

    /**
     * @param  array $icons
     * @return \Core\View\Helper\RowSet
     */
    public function setIcons( array $icons )
    {
        $this->icons = $icons;
        return $this;
    }

    /**
     * @param  string $column
     * @return bool
     */
    public function hasIcon( $column )
    {
        return ! empty( $this->icons[$column] );
    }

    /**
     * @param  string $column
     * @return string|null
     */
    public function getIcon( $column, $value )
    {
        if ( empty( $this->icons[$column] ) )
        {
            return null;
        }

        $icon = $this->icons[$column];

        if ( is_string( $icon ) && strstr( $icon, '%' ) )
        {
            return sprintf( $icon, $value );
        }

        if ( is_callable( $icon ) )
        {
            return $icon( $value );
        }

        return null;
    }

    /**
     * @return array
     */
    public function getNotSetLabels()
    {
        return $this->notSetLabels;
    }

    /**
     * @param  array $labels
     * @return \Core\View\Helper\RowSet
     */
    public function setNotSetLabels( array $labels )
    {
        $this->notSetLabels = $labels;
        return $this;
    }

    /**
     * @param  string $column
     * @return bool
     */
    public function hasNotSetLabel( $column )
    {
        return ! empty( $this->notSetLabels[$column] );
    }

    /**
     * @param  string $column
     * @param  string $fallback
     * @return string
     */
    public function getNotSetLabel( $column, $fallback = 'default.rowSet.notSet' )
    {
        return empty( $this->notSetLabels[$column] )
            ? $fallback
            : $this->notSetLabels[$column];
    }

    /**
     * @return array
     */
    public function getEmptyLabels()
    {
        return $this->emptyLabels;
    }

    /**
     * @param  array $labels
     * @return \Core\View\Helper\RowSet
     */
    public function setEmptyLabels( array $labels )
    {
        $this->emptyLabels = $labels;
        return $this;
    }

    /**
     * @param  string $column
     * @return bool
     */
    public function hasEmptyLabel( $column )
    {
        return ! empty( $this->emptyLabels[$column] );
    }

    /**
     * @param  string $column
     * @param  string $fallback
     * @return string
     */
    public function getEmptyLabel( $column, $fallback = 'default.rowSet.empty' )
    {
        return empty( $this->emptyLabels[$column] )
            ? $fallback
            : $this->emptyLabels[$column];
    }

    /**
     * @return int
     */
    public function getPageRange()
    {
        return $this->pageRange;
    }

    /**
     * @param int $pageRange
     * @return \Core\View\Helper\RowSet
     */
    public function setPageRange( $pageRange )
    {
        $this->pageRange = (int) $pageRange;
        return $this;
    }

    /**
     * @return int
     */
    public function getItemCountPerPage()
    {
        return $this->itemCountPerPage;
    }

    /**
     * @param int $itemCountPerPage
     * @return \Core\View\Helper\RowSet
     */
    public function setItemCountPerPage( $itemCountPerPage )
    {
        $this->itemCountPerPage = (int) $itemCountPerPage;
        return $this;
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @param array $columns
     * @return \Core\View\Helper\RowSet
     */
    public function setColumns( array $columns )
    {
        foreach ( $columns as & $column )
        {
            if ( ! $column instanceof RowSet\Type\TypeInterface )
            {
                $column = call_user_func_array(
                    array( $this, 'column' ),
                    (array) $column
                );
            }
        }

        $this->columns = $columns;
        return $this;
    }

    /**
     * Get hidden-columns
     *
     * @return array
     */
    public function getHiddenColumns()
    {
        return $this->hiddenColumns;
    }

    /**
     * Set hidden-columns
     *
     * @param array|Traversable $columns
     * @return \Core\View\Helper\RowSet
     */
    public function setHiddenColumns( $columns )
    {
        $this->hiddenColumns = array();

        foreach ( $columns as $column )
        {
            $this->hiddenColumns[] = $column;
        }

        return $this;
    }

    /**
     * Get default search
     *
     * @return array
     */
    public function getDefaultSearch()
    {
        return $this->defaultSearch;
    }

    /**
     * Set default search
     *
     * @param array|Traversable $search
     * @return \Core\View\Helper\RowSet
     */
    public function setDefaultSearch( $search )
    {
        $this->defaultSearch = array();

        foreach ( $search as $column => $spec )
        {
            $this->defaultSearch[$column] = $spec;
        }

        return $this;
    }

    /**
     * Get default orders
     *
     * @return array
     */
    public function getDefaultOrders()
    {
        return $this->defaultOrders;
    }

    /**
     * Set default orders
     *
     * @param array|Traversable $orders
     * @return \Core\View\Helper\RowSet
     */
    public function setDefaultOrders( $orders )
    {
        $this->defaultOrders = array();

        foreach ( $orders as $order => $dir )
        {
            $this->defaultOrders[$order] = $dir;
        }

        return $this;
    }

    /**
     * Get column's id
     *
     * @param string $column
     * @return string
     */
    public function getColumnId( $column )
    {
        if ( $this->getColumnsUseTranslation() )
        {
            $translatePrefix    = $this->getColumnTranslatePrefix();
            $translatePostfix   = $this->getColumnTranslatePostfix();

            $column = ( empty( $translatePrefix )
                        ? '' : $translatePrefix . '.' ) .
                      $column .
                      ( empty( $translatePostfix )
                        ? '' : '.' . $translatePostfix );
        }

        return $column;
    }

    /**
     * Get column's translated name
     *
     * @param string $column
     * @return string
     */
    public function getColumnName( $column )
    {
        if ( $this->getColumnsUseTranslation() )
        {
            $column = $this->view->translate(
                $this->getColumnId( $column ),
                $this->getColumnTranslateTextDomain()
            );
        }

        return $column;
    }

    /**
     * Get RowSet's ID
     *
     * @return string
     */
    public function getId()
    {
        if ( empty( $this->id ) )
        {
            if ( $this->getColumnsUseTranslation() )
            {
                $id = trim( $this->getColumnTranslatePrefix() . '.' .
                            $this->getColumnTranslatePostfix(), '.' );

                if ( ! empty( $id ) )
                {
                    return str_replace( '.', '_', $id );
                }
            }

            $this->id = String::generateRandom();
        }

        return $this->id;
    }

    /**
     * @return \Zend\Session\Container
     */
    public function getStore()
    {
        if ( null === $this->store )
        {
            $this->store = new SessionContainer(
                __CLASS__ . '_' . str_replace( '.', '_', $this->getId() )
            );

            if ( ! isset( $this->store['freeWord'] ) )
            {
                $this->store['freeWord'] = '';
            }

            if ( ! isset( $this->store['enable'] ) ||
                 count( $this->store['enable'] ) == 0 )
            {
                $enable     = array();
                $hiddens    = $this->getHiddenColumns();

                foreach ( array_keys( $this->getColumns() ) as $column )
                {
                    $enable[$column] = ! in_array( $column, $hiddens );
                }

                $this->store['enable'] = $enable;
            }

            if ( ! isset( $this->store['search'] ) )
            {
                $this->store['defaultSearch'] = true;
                $this->store['search'] = $this->getDefaultSearch();
            }
            else
            {
                $this->store['defaultSearch'] = false;
            }

            if ( ! isset( $this->store['orders'] ) )
            {
                $this->store['defaultOrders'] = true;
                $this->store['orders'] = $this->getDefaultOrders();
            }
            else
            {
                $this->store['defaultOrders'] = false;
            }
        }

        return $this->store;
    }

    /**
     * @return \Zend\Http\Request
     */
    public function getRequest()
    {
        if ( null === $this->request )
        {
            $this->request = $this->view
                                  ->appService( 'Request' );
        }

        return $this->request;
    }

    /**
     * Is column searched
     *
     * @param string $column
     * @return bool
     */
    public function isColumnSearched( $column )
    {
        $store = $this->getStore();

        if ( isset( $store['search'][$column] ) )
        {
            foreach ( $store['search'][$column] as $search )
            {
                if ( ! empty( $search ) )
                {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Call as a functor
     *
     * @param \Zend\Paginator\Paginator $paginator
     * @param int $flags
     * @return \Core\View\Helper\RowSet
     */
    public function __invoke( Paginator $paginator = null,
                              $flags = self::FLAGS_DEFAULT )
    {
        if ( null !== $paginator )
        {
            $this->id               = null;
            $this->requestParsed    = false;

            $this->setPaginator( $paginator )
                 ->setFlags( $flags );
        }

        return $this;
    }

    /**
     * Create a column type
     *
     * @param string $type
     * @param mixed $...
     * @return \Core\View\Helper\RowSet\Type\TypeInterface
     */
    public function column( $type )
    {
        if ( is_callable( $type ) )
        {
            $args = func_get_args();
            $type = static::CALLBACK;
        }
        else
        {
            $type = ucfirst( (string) $type );
            $args = func_get_args();
            array_shift( $args );
        }

        $class = __CLASS__ . '\\Type\\' . $type;

        if ( ! class_exists( $class ) )
        {
            throw new \LogicException( 'RowSet-type does not exists: ' . $class );
        }

        $reflection = new \ReflectionClass( $class );
        $result     = $reflection->newInstanceArgs( $args );

        if ( $result instanceof HelperInterface )
        {
            $result->setView( $this->getView() );
        }

        return $result;
    }

    /**
     * Escape like
     *
     * @param   string  $like
     * @return  string
     */
    protected function escapeLike( $like )
    {
        return strtr(
            $like,
            array(
                '\\'    => '\\\\',
                '_'     => '\\_',
                '%'     => '\\%',
                '?'     => '_',
                '*'     => '%',
            )
        );
    }

    /**
     * @return void
     */
    protected function parseRequest()
    {
        $request = $this->getRequest();

        if ( $this->requestParsed ||
             empty( $request ) ||
             ! $this->hasFlags( self::FLAG_LAYOUT_FILTERING ) )
        {
            return;
        }

        $this->requestParsed = true;

        $store      = $this->getStore();
        $columns    = $this->getColumns();
        $adapter    = $this->getPaginator()
                           ->getAdapter();

        if ( ! $adapter instanceof DbSelect )
        {
            return;
        }

        $sql        = $adapter->getSql();
        $select     = $adapter->getSelect();
        $platform   = $sql->getAdapter()
                          ->getPlatform();

        $freeSearch = '';
        $freeParams = array();
        $freeTypes  = array();
        $reqmap     = array(
            'columns'   => 'enable',
            'search'    => 'search',
            'freeWord'  => 'freeWord',
            'orders'    => 'orders',
        );

        foreach ( $reqmap as $key => $map )
        {
            $requested = $request->getPost( $key, null );

            if ( null !== $requested )
            {
                $store[$map] = $requested;
            }
        }

        $orders     = array_filter( $store['orders'] );
        $freeWords  = array_filter( preg_split( '/\s+/', $store['freeWord'] ) );

        if ( ! empty( $freeWords ) ||
             ! empty( $store['search'] ) ||
             ! empty( $orders ) )
        {
            $select = $sql->select()
                          ->from( array( 'search' => $select ) );
        }

        if ( ! empty( $freeWords ) )
        {
            foreach ( $store['enable'] as $column => $enabled )
            {
                if ( $enabled )
                {
                    $type = $columns[$column];

                    if ( ( $type instanceof RowSet\Type\Enum &&
                           ! $type instanceof RowSet\Type\Set ) ||
                         $type instanceof RowSet\Type\String    ||
                         $type instanceof RowSet\Type\Replace   ||
                         $type instanceof RowSet\Type\Text      ||
                         $type instanceof RowSet\Type\Translate )
                    {
                        if ( $freeSearch )
                        {
                            $freeSearch .= ' || \' \' || ';
                        }

                        $freeSearch  .= 'COALESCE( TEXT( ? ), \'\' )';
                        $freeParams[] = $column;
                        $freeTypes[]  = Expression::TYPE_IDENTIFIER;
                    }
                }
            }

            if ( $freeSearch )
            {
                $predicates     = array();
                $freeExpression = new Expression(
                    $freeSearch,
                    $freeParams,
                    $freeTypes
                );

                foreach ( $freeWords as $freeWord )
                {
                    $predicates[] = new Predicate\Operator(
                        $freeExpression,
                        'ILIKE',
                        '%' . trim( $this->escapeLike( $freeWord ), '%' ) . '%',
                        Predicate\Operator::TYPE_VALUE,
                        Predicate\Operator::TYPE_LITERAL
                    );
                }

                $select->where( array(
                    new Predicate\PredicateSet( $predicates ),
                ) );
            }
        }

        foreach ( $store['enable'] as $column => $enabled )
        {
            if ( $enabled )
            {
                if ( isset( $store['search'][$column] ) )
                {
                    $search = $store['search'][$column];
                    $type   = $columns[$column];
                    $handled = false;

                    if ( ( $type instanceof RowSet\Type\Enum ) &&
                            ! empty( $search['enum'] ) &&
                            is_array( $search['enum'] ) )
                    {
                        $handled = true;
                        $select->where( array(
                            new Predicate\In( $column, $search['enum'] )
                        ) );
                    }
                    else if ( ( $type instanceof RowSet\Type\Set ) &&
                            ! empty( $search['set'] ) &&
                            is_array( $search['set'] ) )
                    {
                        $handled = true;
                        $select->where( array(
                            new Predicate\Expression(
                                $platform->quoteIdentifier( $column ) .
                                '::text[] && ARRAY[' . implode( ', ',
                                    array_fill( 0, count( $search['set'] ), '?' )
                                ) . ']::text[]',
                                $search['set']
                            )
                        ) );
                    }
                    else if ( $type instanceof RowSet\Type\Bool &&
                              ! empty( $search['bool'] ) )
                    {
                        $handled = true;
                        $select->where( array(
                            new Predicate\Expression(
                                ( $search['bool'] == 'not' ? 'NOT ' : '' ) .
                                $platform->quoteIdentifier( $column )
                            )
                        ) );
                    }
                    else
                    {
                        if ( ( $type instanceof RowSet\Type\Int ||
                               $type instanceof RowSet\Type\Float ||
                               $type instanceof RowSet\Type\Currency ) &&
                             ! empty( $search['min'] ) )
                        {
                            $handled = true;
                            $select->where( array(
                                new Predicate\Operator(
                                    $column,
                                    Predicate\Operator::OP_GTE,
                                    $search['min']
                                )
                            ) );
                        }

                        if ( ( $type instanceof RowSet\Type\Int ||
                               $type instanceof RowSet\Type\Float ||
                               $type instanceof RowSet\Type\Currency ) &&
                                ! empty( $search['max'] ) )
                        {
                            $handled = true;
                            $select->where( array(
                                new Predicate\Operator(
                                    $column,
                                    Predicate\Operator::OP_LTE,
                                    $search['max']
                                )
                            ) );
                        }

                        if ( $type instanceof RowSet\Type\Currency &&
                                  ! empty( $search['currency'] ) )
                        {
                            $handled = true;
                            $select->where( array(
                                new Predicate\Operator(
                                    $column,
                                    '~',
                                    $search['currency'] . '$'
                                )
                            ) );
                        }

                        if ( $type instanceof RowSet\Type\Date &&
                             ! empty( $search['from'] ) )
                        {
                            $handled = true;
                            $select->where( array(
                                new Predicate\Operator(
                                    $column,
                                    Predicate\Operator::OP_GTE,
                                    $search['from']
                                )
                            ) );
                        }

                        if ( $type instanceof RowSet\Type\Date &&
                            ! empty( $search['to'] ) )
                        {
                            $handled = true;
                            $select->where( array(
                                new Predicate\Operator(
                                    $column,
                                    Predicate\Operator::OP_LTE,
                                    $search['to']
                                )
                            ) );
                        }
                    }

                    if ( ! $handled && ! empty( $search['like'] ) )
                    {
                        $select->where( array(
                            new ILike(
                                $column,
                                $this->escapeLike( $search['like'] )
                            ),
                        ) );
                    }
                }
            }
        }

        $select->order( $orders );

        if ( $select !== $adapter->getSelect() )
        {
            $adapter->setSelect( $select );
        }
    }

    /**
     * Render the row-set
     *
     * @param bool $bodyOnly
     * @return string
     */
    public function render( $bodyOnly = false )
    {
        $this->parseRequest();

        $result = $this->view->render( 'rowSet/layout', array(
            'rowSet' => $this,
        ) );

        if ( $bodyOnly )
        {
            return $result;
        }

        if ( $this->hasFlags( self::FLAG_LAYOUT_FILTERING ) )
        {
            $result = $this->view->render( 'rowSet/layout/filtering', array(
                'content'   => $result,
                'rowSet'    => $this,
            ) );
        }

        if ( $this->hasFlags( self::FLAG_LAYOUT_AJAX ) )
        {
            $result = $this->view->render( 'rowSet/layout/ajax', array(
                'content'   => $result,
                'rowSet'    => $this,
            ) );
        }
        else
        {
            $result = $this->view->render( 'rowSet/layout/basic', array(
                'content'   => $result,
                'rowSet'    => $this,
            ) );
        }

        return $result;
    }

}
