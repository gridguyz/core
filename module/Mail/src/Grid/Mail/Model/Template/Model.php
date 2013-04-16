<?php

namespace Grid\Mail\Model\Template;

use Zork\Model\LocaleAwareTrait;
use Zork\Model\LocaleAwareInterface;
use Zork\Model\MapperAwareTrait;
use Zork\Model\MapperAwareInterface;

/**
 * Model
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
     * @param \Mail\Model\Template\Mapper $mailTemplateMapper
     * @param string $locale
     */
    public function __construct( Mapper $mailTemplateMapper, $locale = null )
    {
        $this->setMapper( $mailTemplateMapper )
             ->setLocale( $locale );
    }

    /**
     * Find a structure
     *
     * @param string $name
     * @param string|null $locale
     * @return \Mail\Model\Template\Structure
     */
    public function findByName( $name, $locale = null )
    {
        return $this->getMapper()
                    ->findByName( $name, $locale ?: $this->locale );
    }

    /**
     * Get the paginator
     *
     * @return \Zend\Paginator\Paginator
     */
    public function getPaginator()
    {
        return $this->getMapper()
                    ->getPaginator();
    }

}
