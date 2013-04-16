<?php

namespace Grid\Mail\Model\Template;

use Locale;
use Zend\Db\Sql\Expression;
use Zork\Model\LocaleAwareTrait;
use Zork\Model\LocaleAwareInterface;
use Zork\Model\Mapper\DbAware\ReadWriteMapperAbstract;

/**
 * Mapper
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Mapper extends ReadWriteMapperAbstract
          implements LocaleAwareInterface
{

    use LocaleAwareTrait;

    /**
     * @var string
     */
    const FALLBACK_LOCALE = 'en';

    /**
     * Table name used in all queries
     *
     * @var string
     */
    protected static $tableName = 'mail_template';

    /**
     * Default column-conversion functions as types;
     * used in selected(), deselect()
     *
     * @var array
     */
    protected static $columns = array(
        'id'            => self::INT,
        'name'          => self::STR,
        'locale'        => self::STR,
        'fromAddress'   => self::STR,
        'fromName'      => self::STR,
        'subject'       => self::STR,
        'bodyHtml'      => self::STR,
        'bodyText'      => self::STR,
    );

    /**
     * Contructor
     *
     * @param \Mail\Model\Template\Structure $mailTemplateStructurePrototype
     * @param string $locale
     */
    public function __construct( Structure $mailTemplateStructurePrototype = null,
                                 $locale = null )
    {
        parent::__construct( $mailTemplateStructurePrototype ?: new Structure );
        $this->setLocale( $locale );
    }

    /**
     * Find a structure by name (and a locale)
     *
     * @param string $name
     * @param string|null $locale
     * @return \Mail\Model\Template\Structure
     */
    public function findByName( $name, $locale = null )
    {
        $locale = $locale ?: $this->getLocale();
        $lang   = Locale::getPrimaryLanguage( $locale );

        return $this->findOne( array(
            'name' => $name,
        ), array(
            new Expression(
                'CASE ? ' .
                    'WHEN ? THEN 1 ' .
                    'WHEN ? THEN 2 ' .
                    'WHEN ? THEN 3 ' .
                    'ELSE 4 ' .
                'END ASC',
                array(
                    'locale',
                    $locale,
                    $lang,
                    static::FALLBACK_LOCALE
                ),
                array(
                    Expression::TYPE_IDENTIFIER,
                    Expression::TYPE_VALUE,
                    Expression::TYPE_VALUE,
                    Expression::TYPE_VALUE,
                )
            ),
        ) );
    }

}
