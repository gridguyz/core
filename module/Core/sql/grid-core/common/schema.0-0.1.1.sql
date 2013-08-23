--------------------------------------------------------------------------------
-- type: enum: settings_type                                                  --
--------------------------------------------------------------------------------

CREATE TYPE "_common"."settings_type" AS ENUM (
    'ini',
    'setting',
    'ini-cache'
);

--------------------------------------------------------------------------------
-- type: enum: user_state                                                     --
--------------------------------------------------------------------------------

CREATE TYPE "_common"."user_state" AS ENUM (
    'active',
    'inactive',
    'banned'
);

--------------------------------------------------------------------------------
-- function: first_agg( anyelement, anyelement )                              --
--------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION "_common"."first_agg"( anyelement, anyelement )
                   RETURNS anyelement
                 IMMUTABLE
                  LANGUAGE SQL
                        AS 'SELECT COALESCE( $1, $2 );';

--------------------------------------------------------------------------------
-- function: last_agg( anyelement, anyelement )                               --
--------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION "_common"."last_agg"( anyelement, anyelement )
                   RETURNS anyelement
                 IMMUTABLE
                  LANGUAGE SQL
                        AS 'SELECT COALESCE( $2, $1 );';

--------------------------------------------------------------------------------
-- aggregate: first( anyelement )                                             --
--------------------------------------------------------------------------------

CREATE AGGREGATE "_common"."first"( anyelement )
(
    SFUNC = "_common"."first_agg",
    STYPE = anyelement
);

--------------------------------------------------------------------------------
-- aggregate: last( anyelement )                                              --
--------------------------------------------------------------------------------

CREATE AGGREGATE "_common"."last"( anyelement )
(
    SFUNC = "_common"."last_agg",
    STYPE = anyelement
);

--------------------------------------------------------------------------------
-- function: session_var_get( varchar )                                       --
--------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION "_common"."session_var_get"(
    "p_key"     CHARACTER VARYING
)
     RETURNS CHARACTER VARYING
      STABLE
    LANGUAGE plpgsql
          AS $$
BEGIN

    CREATE TEMPORARY TABLE IF NOT EXISTS "tmp_session_vars"
    (
        "key"       CHARACTER VARYING   NOT NULL,
        "value"     CHARACTER VARYING,

        PRIMARY KEY ( "key" )
    );

    "p_key" = COALESCE( "p_key", '' );

    RETURN (
        SELECT "value"
          FROM "tmp_session_vars"
         WHERE "key" = "p_key"
         LIMIT 1
    );

END $$;

--------------------------------------------------------------------------------
-- function: session_var_set( varchar, varchar )                              --
--------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION "_common"."session_var_set"(
    "p_key"     CHARACTER VARYING,
    "p_value"   CHARACTER VARYING
)
     RETURNS void
    LANGUAGE plpgsql
          AS $$
BEGIN

    CREATE TEMPORARY TABLE IF NOT EXISTS "tmp_session_vars"
    (
        "key"       CHARACTER VARYING   NOT NULL,
        "value"     CHARACTER VARYING,

        PRIMARY KEY ( "key" )
    );

    "p_key" = COALESCE( "p_key", '' );

    UPDATE "tmp_session_vars"
       SET "value"  = "p_value"
     WHERE "key"    = "p_key";

    IF NOT FOUND THEN

        INSERT INTO "tmp_session_vars" ( "key", "value" )
             VALUES ( "p_key", "p_value" );

    END IF;

END $$;

--------------------------------------------------------------------------------
-- function: copy_schema( varchar, varchar )                                  --
--------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION "_common"."copy_schema"(
    "p_source_schema"       CHARACTER VARYING,
    "p_destination_schema"  CHARACTER VARYING
)
     RETURNS void
    LANGUAGE plpgsql
          AS $$
DECLARE
    "v_sequence_name"       TEXT;
    "v_table_name"          TEXT;
    "v_command_line"        TEXT;
    "v_schema_last_value"   INTEGER;
BEGIN

    -- creating schema

    EXECUTE format( 'CREATE SCHEMA %I', "p_destination_schema" );

    -- copy sequences

    FOR "v_sequence_name" IN

        SELECT TEXT( SEQUENCE_NAME )
          FROM INFORMATION_SCHEMA.SEQUENCES
         WHERE SEQUENCE_CATALOG = CURRENT_CATALOG
           AND SEQUENCE_SCHEMA  = "p_source_schema"

    LOOP

        EXECUTE format(
            'CREATE SEQUENCE %I.%I',
            "p_destination_schema",
            "v_sequence_name"
        );

        EXECUTE format( 'SELECT last_value FROM %I.%I',
                        "p_source_schema", "v_sequence_name" )
           INTO "v_schema_last_value";

        PERFORM setval(
            quote_ident( "p_destination_schema" ) ||
                '.' || quote_ident( "v_sequence_name" ),
            "v_schema_last_value" + 1,
            FALSE
        );

    END LOOP;

    -- copy tables

    FOR "v_table_name" IN

        SELECT TEXT( TABLE_NAME )
          FROM INFORMATION_SCHEMA.TABLES
         WHERE TABLE_CATALOG    = CURRENT_CATALOG
           AND TABLE_SCHEMA     = "p_source_schema"

    LOOP

        EXECUTE format(
            'CREATE TABLE %1$I.%3$I ( LIKE %2$I.%3$I INCLUDING ALL )',
            "p_destination_schema",
            "p_source_schema",
            "v_table_name"
        );

    END LOOP;

    -- correct defaults, where points to an old schema's sequence

    FOR "v_command_line" IN

        SELECT format( 'ALTER TABLE %I.%I ALTER COLUMN %I SET DEFAULT %s',
                       TABLE_SCHEMA, TABLE_NAME, COLUMN_NAME,
                       replace( COLUMN_DEFAULT,
                                'nextval(''' || quote_ident( "p_source_schema" ) || '.',
                                'nextval(''' || quote_ident( TABLE_SCHEMA ) || '.' ) )
          FROM INFORMATION_SCHEMA.COLUMNS
         WHERE TABLE_CATALOG    = CURRENT_CATALOG
           AND TABLE_SCHEMA     = "p_destination_schema"
           AND COLUMN_DEFAULT LIKE 'nextval(''' || quote_ident( "p_source_schema" ) || '.%'

    LOOP

        EXECUTE "v_command_line";

    END LOOP;

    -- copy contents of the tables

    FOR "v_table_name" IN

        SELECT TEXT( TABLE_NAME )
          FROM INFORMATION_SCHEMA.TABLES
         WHERE TABLE_CATALOG    = CURRENT_CATALOG
           AND TABLE_SCHEMA     = "p_source_schema"

    LOOP

        EXECUTE format(
            'INSERT INTO %1$I.%3$I ( SELECT * FROM %2$I.%3$I )',
            "p_destination_schema",
            "p_source_schema",
            "v_table_name"
        );

    END LOOP;

    -- add foreign keys

    FOR "v_command_line" IN

        SELECT format(
                   'ALTER TABLE %I.%I'          ||
                   ' ADD CONSTRAINT %I'         ||
                   ' FOREIGN KEY ( %s )'        ||
                   ' REFERENCES %I.%I ( %s )'   ||
                   ' ON UPDATE ' || UPDATE_RULE ||
                   ' ON DELETE ' || DELETE_RULE,
                   "p_destination_schema",
                   ( SELECT DISTINCT TEXT( TABLE_NAME )
                       FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                      WHERE CONSTRAINT_CATALOG  = REFERENTIAL_CONSTRAINTS.CONSTRAINT_CATALOG
                        AND CONSTRAINT_SCHEMA   = REFERENTIAL_CONSTRAINTS.CONSTRAINT_SCHEMA
                        AND CONSTRAINT_NAME     = REFERENTIAL_CONSTRAINTS.CONSTRAINT_NAME ),
                   CONSTRAINT_NAME,
                   ( SELECT string_agg( quote_ident( TEXT( column_name ) ), ', '
                                        ORDER BY POSITION_IN_UNIQUE_CONSTRAINT )
                       FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                      WHERE CONSTRAINT_CATALOG = REFERENTIAL_CONSTRAINTS.CONSTRAINT_CATALOG
                        AND CONSTRAINT_SCHEMA  = REFERENTIAL_CONSTRAINTS.CONSTRAINT_SCHEMA
                        AND CONSTRAINT_NAME    = REFERENTIAL_CONSTRAINTS.CONSTRAINT_NAME ),
                   CASE UNIQUE_CONSTRAINT_SCHEMA
                       WHEN "p_source_schema" THEN "p_destination_schema"
                       ELSE UNIQUE_CONSTRAINT_SCHEMA
                   END,
                   ( SELECT DISTINCT TEXT( TABLE_NAME )
                       FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                      WHERE CONSTRAINT_CATALOG  = REFERENTIAL_CONSTRAINTS.CONSTRAINT_CATALOG
                        AND CONSTRAINT_SCHEMA   = REFERENTIAL_CONSTRAINTS.UNIQUE_CONSTRAINT_SCHEMA
                        AND CONSTRAINT_NAME     = REFERENTIAL_CONSTRAINTS.UNIQUE_CONSTRAINT_NAME ),
                   ( SELECT string_agg( quote_ident( TEXT( column_name ) ), ', '
                                        ORDER BY ORDINAL_POSITION )
                       FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                      WHERE CONSTRAINT_CATALOG = REFERENTIAL_CONSTRAINTS.CONSTRAINT_CATALOG
                        AND CONSTRAINT_SCHEMA  = REFERENTIAL_CONSTRAINTS.UNIQUE_CONSTRAINT_SCHEMA
                        AND CONSTRAINT_NAME    = REFERENTIAL_CONSTRAINTS.UNIQUE_CONSTRAINT_NAME )
               )
          FROM INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS
         WHERE CONSTRAINT_CATALOG   = CURRENT_CATALOG
           AND CONSTRAINT_SCHEMA    = "p_source_schema"

    LOOP

        EXECUTE "v_command_line";

    END LOOP;

    -- copy functions (except aggregates & window functions)

    FOR "v_command_line" IN

        SELECT regexp_replace(
                   regexp_replace(
                       pg_get_functiondef( pg_proc.oid ),
                       format(
                           '^CREATE( OR REPLACE)? FUNCTION %I.',
                           "p_source_schema"
                       ),
                       format(
                           'CREATE OR REPLACE FUNCTION %I.',
                           "p_destination_schema"
                       )
                   ),
                   format(
                       ' SET search_path TO %I(,\s*%I)?',
                       "p_source_schema",
                       '_common'
                   ),
                   format(
                       ' SET search_path TO %I, %I',
                       "p_destination_schema",
                       '_common'
                   )
               )
          FROM pg_catalog.pg_proc
          JOIN pg_catalog.pg_namespace
            ON pg_namespace.oid         = pg_proc.pronamespace
         WHERE pg_proc.proisagg         = FALSE
           AND pg_proc.proiswindow      = FALSE
           AND pg_namespace.nspname     = "p_source_schema"

    LOOP

        EXECUTE "v_command_line";

    END LOOP;

    -- copy triggers

    FOR "v_command_line" IN

        SELECT regexp_replace(
                   regexp_replace(
                       pg_get_triggerdef( pg_trigger.oid ),
                       ' ON .* FOR ',
                       format(
                           ' ON %I.%I FOR ',
                           "p_destination_schema",
                           pg_class.relname
                       )
                   ),
                   ' EXECUTE PROCEDURE .*\.',
                   format(
                       ' EXECUTE PROCEDURE %I.',
                       "p_destination_schema"
                   )
               )
          FROM pg_catalog.pg_trigger
          JOIN pg_catalog.pg_class
            ON pg_class.oid             = pg_trigger.tgrelid
          JOIN pg_catalog.pg_namespace
            ON pg_namespace.oid         = pg_class.relnamespace
         WHERE pg_trigger.tgisinternal  = FALSE
           AND pg_namespace.nspname     = "p_source_schema"

    LOOP

        EXECUTE "v_command_line";

    END LOOP;

END $$;
