<?php

namespace Grid\Core\Installer;

use Grid\Installer\AbstractPatch;

/**
 * Patch
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 *
 * @method \PDO getDb()
 */
class Patch extends AbstractPatch
{

    /**
     * @const int
     */
    const SITE_OWNER_GROUP = 2;

    /**
     * Quote sql-identifier
     *
     * @param   string  $id
     * @return  string
     */
    protected static function quoteIdentifier( $id )
    {
        return '"' . str_replace( '"', '""', $id ) . '"';
    }

    /**
     * Run after patching
     *
     * @param   string  $from
     * @param   string  $to
     * @return  void
     */
    public function afterPatch( $from, $to )
    {
        if ( $this->isZeroVersion( $from ) )
        {
            $platformOwner = $this->selectFromTable( 'user', 'id', array(
                'groupId' => static::SITE_OWNER_GROUP,
            ) );

            if ( ! $platformOwner )
            {
                $platformOwner = $this->insertPlatformOwner();
            }

            $content = $this->selectFromTable( 'paragraph', 'id', array(
                'type' => 'content',
            ) );

            if ( ! $content )
            {
                $content = $this->insertDefaultParagraph( 'content' );
            }

            $menu = $this->selectFromTable( 'menu', 'id' );

            if ( ! $menu )
            {
                $menu = $this->insertDefaultMenu( $content );
            }

            $layout = $this->selectFromTable( 'paragraph', 'id', array(
                'type' => 'layout',
            ) );

            if ( ! $layout )
            {
                $layout = $this->insertDefaultParagraph( 'layout' );
            }

            $subDomain = $this->selectFromTable( 'subdomain', 'id', array(
                'subdomain' => '',
            ) );

            if ( ! $subDomain )
            {
                $subDomain = $this->insertDefaultSubDomain( $layout, $content );
            }
        }
    }

    /**
     * Select a field from a table
     *
     * @param   array|string    $table
     * @param   string          $column
     * @param   array           $where
     * @return  int
     */
    protected function selectFromTable( $table, $column, array $where = array() )
    {
        $whereSql = '';

        foreach ( $where as $col => $value )
        {
            if ( $whereSql )
            {
                $whereSql .= '
               AND ';
            }

            $whereSql .= static::quoteIdentifier( $col ) . ' = :' . $col;
        }

        $query = $this->getDb()->prepare( sprintf(
            'SELECT %s FROM %s WHERE %s ORDER BY %s ASC LIMIT 1',
            static::quoteIdentifier( $column ),
            implode( '.', array_map( array( __CLASS__, 'quoteIdentifier' ), (array) $table ) ),
            $whereSql ?: 'TRUE',
            static::quoteIdentifier( $column )
        ) );

        $query->execute( $where );

        if ( ! $query->rowCount() )
        {
            return null;
        }

        return $query->fetchObject()->$column;
    }

    /**
     * Insert data to table
     *
     * @param   array|string        $table
     * @param   array               $data
     * @param   null|bool|string    $seq
     * @return  int
     */
    protected function insertToTable( $table, array $data, $seq = null )
    {
        $table   = (array) $table;
        $columns = '';
        $values  = '';

        foreach ( $data as $field => $value )
        {
            if ( $columns )
            {
                $columns .= ', ';
            }

            if ( $values )
            {
                $values .= ', ';
            }

            $columns .= static::quoteIdentifier( $field );
            $values  .= ':' . $field;
        }

        $db    = $this->getDb();
        $query = $db->prepare( sprintf(
            'INSERT INTO %s ( %s ) VALUES ( %s )',
            implode( '.', array_map( array( __CLASS__, 'quoteIdentifier' ), $table ) ),
            $columns,
            $values
        ) );

        $query->execute( $data );

        if ( $seq )
        {
            if ( true === $seq )
            {
                $seq = implode( '.', $table ) . '_id_seq';
            }

            return $db->lastInsertId( $seq );
        }

        return null;
    }

    /**
     * Insert developer user
     *
     * @return  int
     */
    protected function insertPlatformOwner()
    {
        $data  = $this->getPatchData();
        $email = $data->get(
            'gridguyz-core',
            'platformOwner-email',
            'Type the platform owner\'s email (must be valid email)',
            null,
            '/^[A-Z0-9\._%\+-]+@[A-Z0-9\.-]+\.[A-Z]{2,4}$/i',
            3
        );

        $displayName = $data->get(
            'gridguyz-core',
            'platformOwner-displayName',
            'Type the platform owner\'s display name',
            strstr( $email, '@', true )
        );

        $password = $data->get(
            'gridguyz-core',
            'platformOwner-password',
            'Type the platform owner\'s password (min length: 4 characters)',
            null,
            '/.{4,}/',
            3
        );

        return $this->insertToTable(
            'user',
            array(
                'email'         => $email,
                'displayName'   => $displayName,
                'passwordhash'  => $this->hash( $password ), // TODO: hash
                'groupId'       => static::SITE_OWNER_GROUP,
                'state'         => 'active',
                'confirmed'     => 't',
                'locale'        => 'en',
            ),
            true
        );
    }

    /**
     * Insert default paragraph: content / layout
     *
     * @return  int
     */
    protected function insertDefaultParagraph( $type )
    {
        $db     = $this->getDb();
        $data   = $this->getPatchData();
        $first  = $this->selectFromTable(
            array( '_central', 'paragraph' ),
            'id',
            array( 'type' => $type )
        );

        $id = $data->get(
            'gridguyz-core',
            $type . 'Id',
            'Type the default ' . $type . '\'s id',
            $first
        );

        $query = $db->prepare( '
            SELECT "paragraph_clone"( :schema, :id ) AS "result"
        ' );

        $query->execute( array(
            'schema'    => '_central',
            'id'        => $id,
        ) );

        while ( $row = $query->fetchObject() )
        {
            return $row->result;
        }

        return null;
    }

    /**
     * Insert default menu
     *
     * @param   int $content
     * @retirn  int
     */
    protected function insertDefaultMenu( $content )
    {
        $root = $this->insertToTable(
            'menu',
            array(
                'type'  => 'container',
                'left'  => 1,
                'right' => 4,
            ),
            true
        );

        $this->insertToTable(
            'menu_label',
            array(
                'menuId'    => $root,
                'locale'    => 'en',
                'label'     => 'Default menu',
            )
        );

        $menuContent = $this->insertToTable(
            'menu',
            array(
                'type'  => 'content',
                'left'  => 2,
                'right' => 3,
            ),
            true
        );

        $this->insertToTable(
            'menu_label',
            array(
                'menuId'    => $menuContent,
                'locale'    => 'en',
                'label'     => 'Home',
            )
        );

        $this->insertToTable(
            'menu_property',
            array(
                'menuId'    => $menuContent,
                'name'      => 'contentId',
                'value'     => $content,
            )
        );

        return $root;
    }

    /**
     * Insert default sub-domain
     *
     * @param   int $layout
     * @param   int $content
     * @retirn  int
     */
    protected function insertDefaultSubDomain( $layout, $content )
    {
        return $this->insertToTable(
            'subdomain',
            array(
                'subdomain'         => '',
                'locale'            => 'en',
                'defaultLayoutId'   => $layout,
                'defaultContentId'  => $content,
            )
        );
    }

}
