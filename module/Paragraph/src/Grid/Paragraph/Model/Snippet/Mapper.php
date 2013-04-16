<?php

namespace Grid\Paragraph\Model\Snippet;

use Traversable;
use SplFileInfo;
use Zork\Db\SiteInfo;
use FilesystemIterator;
use Zend\Stdlib\ArrayUtils;
use Zend\Paginator\Paginator;
use Zork\Db\SiteInfoAwareTrait;
use Zork\Db\SiteInfoAwareInterface;
use Zork\Model\MapperAwareInterface;
use Zork\Iterator\CallbackMapIterator;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Zork\Model\Structure\StructureAbstract;
use Zork\Model\Mapper\ReadWriteMapperInterface;
use Zend\Paginator\Adapter\Iterator as IteratorPaginator;

/**
 * Mapper
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Mapper implements HydratorInterface,
                        SiteInfoAwareInterface,
                        ReadWriteMapperInterface
{

    use SiteInfoAwareTrait;

    /**
     * @const string
     */
    const SAVE_PATH = './public/uploads/%s/snippets/';

    /**
     * Save path
     *
     * @var string
     */
    private $savePath;

    /**
     * Structure prototype for the mapper
     *
     * @var \Paragraph\Model\Snippet\Structure
     */
    protected $structurePrototype;

    /**
     * Allowed extensions
     *
     * @var string
     */
    protected $allowedExtensions = array(
        '.js', '.css',
    );

    /**
     * Get structure prototype
     *
     * @return \Paragraph\Model\Snippet\Structure
     */
    public function getStructurePrototype()
    {
        return $this->structurePrototype;
    }

    /**
     * Set structure prototype
     *
     * @param   \Paragraph\Model\Snippet\Structure  $structurePrototype
     * @return  \Paragraph\Model\Snippet\Mapper
     */
    public function setStructurePrototype( Structure $structurePrototype )
    {
        if ( $structurePrototype instanceof MapperAwareInterface )
        {
            $structurePrototype->setMapper( $this );
        }

        $this->structurePrototype = $structurePrototype;
        return $this;
    }

    /**
     * Constructor
     *
     * @param   \Zork\Db\SiteInfo                   $siteInfo
     * @param   \Paragraph\Model\Snippet\Structure  $structurePrototype
     */
    public function __construct( SiteInfo $siteInfo, Structure $structurePrototype = null )
    {
        $this->setSiteInfo( $siteInfo )
             ->setStructurePrototype( $structurePrototype ?: new Structure );
    }

    /**
     * Get save path
     *
     * @return string
     */
    public function getSavePath()
    {
        if ( empty( $this->savePath ) )
        {
            $this->savePath = sprintf(
                static::SAVE_PATH,
                $this->getSiteInfo()
                     ->getSchema()
            );

            $dir = rtrim( $this->savePath, '/' );

            if ( ! is_dir( $dir ) )
            {
                @ mkdir( $dir, 0777, true );
            }
        }

        return $this->savePath;
    }

    /**
     * Create structure from plain data
     *
     * @param   array   $data
     * @return  \Paragraph\Model\Snippet\Structure
     */
    protected function createStructure( array $data )
    {
        $structure = clone $this->structurePrototype;
        $structure->setOptions( $data );

        if ( $structure instanceof MapperAwareInterface )
        {
            $structure->setMapper( $this );
        }

        return $structure;
    }

    /**
     * Extract values from an object
     *
     * @param   \Paragraph\Model\Snippet\Structure  $object
     * @return  array
     */
    public function extract( $structure )
    {
        if ( $structure instanceof StructureAbstract )
        {
            return $structure->toArray();
        }

        if ( $structure instanceof Traversable )
        {
            return ArrayUtils::iteratorToArray( $structure );
        }

        return (array) $structure;
    }

    /**
     * Hydrate $object with the provided $data.
     *
     * @param   array                               $data
     * @param   \Paragraph\Model\Snippet\Structure  $object
     * @return  \Paragraph\Model\Snippet\Structure
     */
    public function hydrate( array $data, $structure )
    {
        if ( ! empty( $data['file'] ) )
        {
            if ( ! empty( $data['file']['tmp_name'] ) )
            {
                $data['name'] = $data['file']['name'];
                $data['code'] = @ file_get_contents( $data['file']['tmp_name'] );
                @ unlink( $data['file']['tmp_name'] );
            }

            unset( $data['file'] );
        }

        if ( ! empty( $data['type'] ) )
        {
            $data['name'] .= '.' . $data['type'];
            unset( $data['type'] );
        }

        if ( $structure instanceof StructureAbstract )
        {
            $structure->setOptions( $data );
        }
        else
        {
            foreach ( $data as $key => $value )
            {
                $structure->$key = $value;
            }
        }

        if ( $structure instanceof MapperAwareInterface )
        {
            $structure->setMapper( $this );
        }

        return $structure;
    }

    /**
     * Create a structure
     *
     * @param   array|\Traversable  $data
     * @return  \Paragraph\Model\Snippet\Structure
     */
    public function create( $data )
    {
        $data = ArrayUtils::iteratorToArray( $data );
        return $this->createStructure( $data );
    }

    /**
     * Find a structure
     *
     * @param   string  $name
     * @return  \Paragraph\Model\Snippet\Structure
     */
    public function find( $name )
    {
        $name = (string) $name;
        $path = $this->getSavePath();

        if ( ! is_file( $path . $name ) )
        {
            return null;
        }

        return $this->createStructure( array(
            'name' => $name,
            'code' => @ file_get_contents( $path . $name ),
        ) );
    }

    /**
     * Find a structure is available
     *
     * @param   string $name
     * @return  bool
     */
    public function isNameExists( $name )
    {
        return is_file( $this->getSavePath() . $name );
    }

    /**
     * Save a structure
     *
     * @param   array|\Paragraph\Model\Snippet\Structure $structure
     * @return  int
     */
    public function save( & $structure )
    {
        if ( is_object( $structure ) )
        {
            $name = (string) $structure->name;
            $code = (string) $structure->code;
        }
        else if ( is_array( $structure ) )
        {
            $name = (string) $structure['name'];
            $code = (string) $structure['code'];
        }
        else
        {
            return 0;
        }

        if ( empty( $name ) )
        {
            return 0;
        }

        $ext = strrchr( $name, '.' );

        if ( empty( $ext ) || ! in_array( $ext, $this->allowedExtensions ) )
        {
            return 0;
        }

        return @ file_put_contents( $this->getSavePath() . $name, $code ) ? 1 : 0;
    }

    /**
     * Remove a structure
     *
     * @param   string|array|\Paragraph\Model\Snippet\Structure $structureOrName
     * @return  int
     */
    public function delete( $structureOrName )
    {
        $path = $this->getSavePath();

        if ( is_object( $structureOrName ) )
        {
            $name = $structureOrName->name;
        }
        else if ( is_array( $structureOrName ) )
        {
            $name = $structureOrName['name'];
        }
        else
        {
            $name = (string) $structureOrName;
        }

        if ( ! is_file( $path . $name ) )
        {
            return 0;
        }

        return (int) @ unlink( $path . $name );
    }

    /**
     * Find all snippets
     *
     * @return \Iterator
     */
    public function findAll()
    {
        return new CallbackMapIterator(
            new FilesystemIterator(
                $this->getSavePath(),
                FilesystemIterator::SKIP_DOTS |
                FilesystemIterator::KEY_AS_FILENAME |
                FilesystemIterator::CURRENT_AS_FILEINFO
            ),
            function ( SplFileInfo $info ) {
                return $info->getFilename();
            }
        );
    }

    /**
     * Find options for listing
     *
     * @return array
     */
    public function findOptions()
    {
        return ArrayUtils::iteratorToArray( $this->findAll() );
    }

    /**
     * Get paginator for listing
     *
     * @return  \Zend\Paginator\Paginator
     */
    public function getPaginator()
    {
        return new Paginator(
            new IteratorPaginator(
                new CallbackMapIterator(
                    $this->findAll(),
                    array( $this, 'find' )
                )
            )
        );
    }

}
