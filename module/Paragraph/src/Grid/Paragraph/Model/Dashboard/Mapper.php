<?php

namespace Grid\Paragraph\Model\Dashboard;

use Grid\Customize\Model\Rule;
use Grid\Customize\Model\Rule\HydrateSimpleProperties;
use Grid\Paragraph\Model\Paragraph;
use Zork\Model\LocaleAwareTrait;
use Zork\Model\LocaleAwareInterface;
use Zork\Model\LocaleAwaresAwareInterface;
use Zork\Model\MapperAwareInterface;
use Zork\Model\Mapper\ReadWriteMapperInterface;
use Zend\Stdlib\ArrayUtils;
use Zend\Stdlib\Hydrator\HydratorInterface;

/**
 * \Paragraph\Model\Dashboard\Mapper
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Mapper implements HydratorInterface,
                        LocaleAwareInterface,
                        ReadWriteMapperInterface,
                        LocaleAwaresAwareInterface
{

    use LocaleAwareTrait;

    /**
     * @var \Paragraph\Model\Dashboard\Customization
     */
    protected $customization;

    /**
     * @var \Paragraph\Model\Paragraph\Mapper
     */
    protected $paragraphMapper;

    /**
     * @var \Customize\Model\Rule\Mapper
     */
    protected $customizeRuleMapper;

    /**
     * @var \Paragraph\Model\Dashboard\Structure
     */
    protected $structurePrototype;

    /**
     * @var \Customize\Model\Rule\HydrateSimpleProperties
     */
    protected $hydrateSimpleProperties;

    /**
     * @return \Paragraph\Model\Dashboard\Customization
     */
    public function getCustomization()
    {
        return $this->customization;
    }

    /**
     * @param \Paragraph\Model\Dashboard\Customization $customization
     * @return \Paragraph\Model\Dashboard\Mapper
     */
    public function setCustomization( Customization $customization )
    {
        $this->customization = $customization;
        return $this;
    }

    /**
     * @return \Paragraph\Model\Paragraph\Mapper
     */
    public function getParagraphMapper()
    {
        return $this->paragraphMapper;
    }

    /**
     * @param \Paragraph\Model\Paragraph\Mapper $paragraphMapper
     * @return \Paragraph\Model\Dashboard\Mapper
     */
    public function setParagraphMapper( Grid\Paragraph\Mapper $paragraphMapper )
    {
        $this->paragraphMapper = $paragraphMapper;
        return $this;
    }

    /**
     * Get locale-awares bound objects
     *
     * @return array
     */
    public function getLocaleAwares()
    {
        return array( $this->getParagraphMapper() );
    }

    /**
     * @return \Customize\Model\Rule\Mapper
     */
    public function getCustomizeRuleMapper()
    {
        return $this->customizeRuleMapper;
    }

    /**
     * @return \Customize\Model\Rule\HydrateSimpleProperties
     */
    public function getHydrateSimpleProperties()
    {
        if ( null === $this->hydrateSimpleProperties )
        {
            $this->hydrateSimpleProperties = new HydrateSimpleProperties(
                $this->getCustomizeRuleMapper()
            );
        }

        return $this->hydrateSimpleProperties;
    }

    /**
     * @param \Customize\Model\Rule\Mapper $customizeRuleMapper
     * @return \Paragraph\Model\Dashboard\Mapper
     */
    public function setCustomizeRuleMapper( Rule\Mapper $customizeRuleMapper )
    {
        $this->customizeRuleMapper = $customizeRuleMapper;
        return $this;
    }

    /**
     * Get structure prototype
     *
     * @return \Zork\Model\Structure\StructureAbstract
     */
    public function getStructurePrototype()
    {
        return $this->structurePrototype;
    }

    /**
     * Set structure prototype
     *
     * @param \Zork\Model\Structure\StructureAbstract $structurePrototype
     * @return \Zork\Model\Mapper\DbAware\ReadOnlyMapperAbstract
     */
    public function setStructurePrototype( $structurePrototype )
    {
        if ( $structurePrototype instanceof MapperAwareInterface )
        {
            $structurePrototype->setMapper( $this );
        }

        $this->structurePrototype = $structurePrototype;
        return $this;
    }

    /**
     * Create structure from plain data
     *
     * @param array $data
     * @return \Zork\Model\Structure\StructureAbstract
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
     * @param \Paragraph\Model\Paragraph\Mapper $paragraphMapper
     * @param \Customize\Model\Rule\Mapper $customizeRuleMapper
     * @param \Paragraph\Model\Dashboard\Structure $paragraphDashboardStructurePrototype
     */
    public function __construct( Customization $customization,
                                 Grid\Paragraph\Mapper $paragraphMapper,
                                 Rule\Mapper $customizeRuleMapper,
                                 Structure $paragraphDashboardStructurePrototype = null)
    {
        $this->setCustomization( $customization )
             ->setParagraphMapper( $paragraphMapper )
             ->setCustomizeRuleMapper( $customizeRuleMapper )
             ->setStructurePrototype( $paragraphDashboardStructurePrototype ?: new Structure );
    }

    /**
     * Create a structure
     *
     * @param array|\Traversable $data
     * @return \Paragraph\Model\Dashboard\Structure
     */
    public function create( $data )
    {
        $data = ArrayUtils::iteratorToArray( $data );
        return $this->createStructure( $data );
    }

    /**
     * Find a structure
     *
     * @param int|string|array $primaryKeys
     * @return \Paragraph\Model\Dashboard\Structure
     */
    public function find( $primaryKeys )
    {
        $id = is_array( $primaryKeys ) ? reset( $primaryKeys ) : $primaryKeys;

        if ( empty( $id ) )
        {
            return null;
        }

        $paragraph = $this->getParagraphMapper()
                          ->find( $id );

        if ( empty( $paragraph ) )
        {
            return null;
        }

        $rules      = array();
        $selectors  = $this->getCustomization()
                           ->getSelectorsByParagraph( $paragraph );

        foreach ( $selectors as $key => $selector )
        {
            $rule = $this->getCustomizeRuleMapper()
                         ->findBySelector( $selector );

            if ( empty( $rule ) )
            {
                $rule = $this->getCustomizeRuleMapper()
                             ->create( array(
                                 'paragraphId'  => $paragraph->id,
                                 'selector'     => $selector,
                             ) );
            }

            $rules[$key] = $rule;
        }

        return $this->createStructure( array(
            'paragraph' => $paragraph,
            'rules'     => $rules,
        ) );
    }

    /**
     * Extract values from an object
     *
     * @param  object $object
     * @return array
     */
    public function extract( $object )
    {
        $result = array();
        $type   = $object->paragraph->type;

        $result['paragraph-' . $type] = $this->getParagraphMapper()
                                             ->extract( $object->paragraph );

        $propertyHydrator = $this->getHydrateSimpleProperties();

        foreach ( $object->rules as $key => $rule )
        {
            $result['customize-' . $key] = $propertyHydrator->extract( $rule )
                                                              [ 'properties' ];
        }

        return $result;
    }

    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  object $object
     * @return object
     */
    public function hydrate( array $data, $object )
    {
        $type = $object->paragraph->type;

        if ( isset( $data['paragraph-' . $type] ) )
        {
            $this->getParagraphMapper()
                 ->hydrate( (array) $data['paragraph-' . $type],
                            $object->paragraph );
        }

        $propertyHydrator = $this->getHydrateSimpleProperties();

        foreach ( $object->rules as $key => $rule )
        {
            if ( isset( $data['customize-' . $key] ) )
            {
                $ruleData = array(
                    'properties' => (array) $data['customize-' . $key],
                );

                $propertyHydrator->hydrate( $ruleData, $rule );
            }
        }

        return $object;
    }

    /**
     * Save a structure
     *
     * @param array|\Paragraph\Model\Dashboard\Structure $structure
     * @return int
     */
    public function save( & $structure )
    {
        if ( is_object( $structure ) )
        {
            $paragraph  = $structure->paragraph;
            $rules      = $structure->rules;
        }
        else if ( is_array( $structure ) )
        {
            $paragraph  = $structure['paragraph'];
            $rules      = $structure['rules'];
        }
        else
        {
            return 0;
        }

        $rows = $paragraph->setMapper( $this->getParagraphMapper() )
                          ->save();

        if ( $rows && ! empty( $rules ) )
        {
            foreach ( $rules as $rule )
            {
                $rows += $rule->setMapper( $this->getCustomizeRuleMapper() )
                              ->save();
            }
        }

        return $rows;
    }

    /**
     * Remove a structure
     *
     * @param int|string|array|\Paragraph\Model\Dashboard\Structure $structureOrPrimaryKeys
     * @return int
     */
    public function delete( $structureOrPrimaryKeys )
    {
        if ( is_object( $structureOrPrimaryKeys ) )
        {
            $id = $structureOrPrimaryKeys->paragraph->id;
        }
        else if ( is_array( $structureOrPrimaryKeys ) )
        {
            if ( isset( $structureOrPrimaryKeys['paragraph']['id'] ) )
            {
                $id = $structureOrPrimaryKeys['paragraph']['id'];
            }
            else
            {
                $id = reset( $structureOrPrimaryKeys );
            }
        }
        else
        {
            $id = $structureOrPrimaryKeys;
        }

        return $this->getParagraphMapper()
                    ->delete( $id );
    }

}
