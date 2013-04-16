<?php

namespace Grid\Tag\Model\Tag;

use Zend\Db\Sql;
use Zork\Model\LocaleAwareTrait;
use Zork\Model\MapperAwareTrait;
use Zork\Model\LocaleAwareInterface;
use Zork\Model\MapperAwareInterface;
use Zork\Db\Sql\Predicate\TypedParameters;

/**
 * Model
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Model implements LocaleAwareInterface,
                       MapperAwareInterface
{

    use LocaleAwareTrait,
        MapperAwareTrait;

    /**
     * Construct model
     *
     * @param \Tag\Model\Tag\Mapper $tagMapper
     */
    public function __construct( Mapper $tagMapper, $locale = null )
    {
        $this->setMapper( $tagMapper )
             ->setLocale( $locale );
    }

    /**
     * Get paginator for listing
     *
     * @return \Zend\Paginator\Paginator
     */
    public function getPaginator()
    {
        return $this->getMapper()
                    ->getPaginator();
    }

    /**
     * Create a new tag from data
     *
     * @param   array|null $data
     * @return  \Tag\Model\Tag\Structure
     */
    public function create( $data )
    {
        return $this->getMapper()
                    ->create( $data );
    }

    /**
     * Find a tag by id
     *
     * @param   int $id
     * @return  \Tag\Model\Tag\Structure
     */
    public function find( $id )
    {
        return $this->getMapper()
                    ->find( $id );
    }

    /**
     * Find options
     *
     * @param   string  $term
     * @return  array
     */
    public function findOptions( $term = null )
    {
        $term = mb_strtolower( $term, 'UTF-8' );

        return $this->getMapper()
                    ->findOptions(
                        array(
                            'id'        => 'id',
                            'value'     => 'name',
                            'locale'    => 'locale',
                        ),
                        array(
                            new TypedParameters(
                                'POSITION( ? IN LOWER( ? ) ) > 0',
                                array(
                                    $term,
                                    'name',
                                ),
                                array(
                                    TypedParameters::TYPE_VALUE,
                                    TypedParameters::TYPE_IDENTIFIER,
                                )
                            ),
                        ),
                        array(
                            new Sql\Expression(
                                'POSITION( ? IN LOWER( ? ) ) ASC',
                                array(
                                    $term,
                                    'name',
                                ),
                                array(
                                    Sql\Expression::TYPE_VALUE,
                                    Sql\Expression::TYPE_IDENTIFIER,
                                )
                            ),
                            'name' => 'ASC',
                        )
                    );
    }

    /**
     * Find a tag by name
     *
     * @param   string $name
     * @return  \Tag\Model\Tag\Structure
     */
    public function findByName( $name )
    {
        return $this->getMapper()
                    ->findByName( $name );
    }

    /**
     * Find tag usages by locale(s)
     *
     * @param   array|string|null $locale
     * @return  array
     */
    public function findUsagesByLocales( $locale = null )
    {
        if ( empty( $locale ) )
        {
            $locale = $this->getLocale();
        }

        return $this->getMapper()
                    ->findUsagesByLocales( (array) $locale );
    }

    /**
     * Is tag-name available?
     *
     * @param   string  $name
     * @param   array   $params
     * @return  bool
     */
    public function isNameAvailable( $name, $params = null )
    {
        $params = (array) $params;

        return ! $this->getMapper()
                      ->isNameExists(
                            $name,
                            empty( $params['id'] ) ? null : $params['id']
                        );
    }

}
