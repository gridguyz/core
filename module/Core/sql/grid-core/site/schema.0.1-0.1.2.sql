--------------------------------------------------------------------------------
-- table: table_plugin_trigger                                                --
--------------------------------------------------------------------------------

CREATE TABLE "table_plugin_trigger"
(
    "id"        SERIAL                      NOT NULL,
    "tables"    CHARACTER VARYING ARRAY     NOT NULL,
    "create"    CHARACTER VARYING           NULL        DEFAULT NULL,
    "drop"      CHARACTER VARYING           NULL        DEFAULT NULL,

    PRIMARY KEY ( "id" ),
    UNIQUE ( "tables" )
);

--------------------------------------------------------------------------------
-- function: table_plugin_run_create_triggers( variadic varchar[] )           --
--------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION "table_plugin_run_create_triggers"(
    VARIADIC "p_tables"  CHARACTER VARYING ARRAY
)
     RETURNS INTEGER
         SET search_path FROM CURRENT
    VOLATILE
    LANGUAGE plpgsql
          AS $$
DECLARE
    "v_result"      INTEGER             DEFAULT 0;
    "v_table"       CHARACTER VARYING;
    "r_trigger"     "table_plugin_trigger"%ROWTYPE;
    "c_trigger"     NO SCROLL CURSOR FOR
                              SELECT *
                                FROM "table_plugin_trigger"
                               WHERE "tables" @> "p_tables"
                                 AND "create" IS NOT NULL;
BEGIN

    <<trigger_loop>>
    FOR "r_trigger" IN "c_trigger" LOOP

        FOREACH "v_table" IN ARRAY "r_trigger"."tables" LOOP
            IF NOT EXISTS( SELECT TRUE
                             FROM INFORMATION_SCHEMA.TABLES
                            WHERE TABLE_SCHEMA = CURRENT_SCHEMA
                              AND TABLE_NAME   = "v_table" ) THEN
                CONTINUE trigger_loop;
            END IF;
        END LOOP;

        EXECUTE format(
            'SELECT %I.%I()',
            CURRENT_SCHEMA,
            "r_trigger"."create"
        );

        "v_result" = "v_result" + 1;

    END LOOP trigger_loop;

    RETURN "v_result";

END $$;

--------------------------------------------------------------------------------
-- function: table_plugin_run_drop_triggers( variadic varchar[] )             --
--------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION "table_plugin_run_drop_triggers"(
    VARIADIC "p_tables"  CHARACTER VARYING ARRAY
)
     RETURNS INTEGER
         SET search_path FROM CURRENT
    VOLATILE
    LANGUAGE plpgsql
          AS $$
DECLARE
    "v_result"      INTEGER             DEFAULT 0;
    "v_table"       CHARACTER VARYING;
    "r_trigger"     "table_plugin_trigger"%ROWTYPE;
    "c_trigger"     NO SCROLL CURSOR FOR
                              SELECT *
                                FROM "table_plugin_trigger"
                               WHERE "tables" @> "p_tables"
                                 AND "drop" IS NOT NULL;
BEGIN

    <<trigger_loop>>
    FOR "r_trigger" IN "c_trigger" LOOP

        FOREACH "v_table" IN ARRAY "r_trigger"."tables" LOOP
            IF NOT EXISTS( SELECT TRUE
                             FROM INFORMATION_SCHEMA.TABLES
                            WHERE TABLE_SCHEMA = CURRENT_SCHEMA
                              AND TABLE_NAME   = "v_table" ) THEN
                CONTINUE trigger_loop;
            END IF;
        END LOOP;

        EXECUTE format(
            'SELECT %I.%I()',
            CURRENT_SCHEMA,
            "r_trigger"."drop"
        );

        "v_result" = "v_result" + 1;

    END LOOP trigger_loop;

    RETURN "v_result";

END $$;

--------------------------------------------------------------------------------
-- function: paragraph_update_search_content()                                --
--------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION "paragraph_update_search_content"(
    "p_paragraph_id"    INTEGER,
    "p_locale"          CHARACTER VARYING
)
        RETURNS BOOLEAN
            SET search_path FROM CURRENT
       LANGUAGE plpgsql
             AS $$
DECLARE
    "v_title"           CHARACTER VARYING           DEFAULT '';
    "v_keywords"        CHARACTER VARYING           DEFAULT '';
    "v_description"     CHARACTER VARYING           DEFAULT '';
    "v_content"         TEXT                        DEFAULT '';
    "v_published"       BOOLEAN                     DEFAULT TRUE;
    "v_published_from"  TIMESTAMP WITH TIME ZONE    DEFAULT NULL;
    "v_published_to"    TIMESTAMP WITH TIME ZONE    DEFAULT NULL;
    "v_all_access"      BOOLEAN                     DEFAULT TRUE;
    "v_access_groups"   INTEGER ARRAY               DEFAULT CAST( ARRAY[] AS INTEGER ARRAY );
    "v_access_users"    INTEGER ARRAY               DEFAULT CAST( ARRAY[] AS INTEGER ARRAY );
    "v_value"           TEXT;
    "r_tags"            "tag"%ROWTYPE;
    "r_properties"      "paragraph_property"%ROWTYPE;
    "c_tags"            NO SCROLL CURSOR FOR
                                  SELECT "tag".*
                                    FROM "paragraph_x_tag"
                                    JOIN "tag" ON "paragraph_x_tag"."tagId" = "tag"."id"
                                   WHERE "paragraph_x_tag"."paragraphId"    = "p_paragraph_id"
                                     AND ( "tag"."locale" IS NULL
                                        OR "tag"."locale" IN ( '', '*', SUBSTRING( "p_locale" FOR 2 ), "p_locale" ) );
    "c_rootproperties"  NO SCROLL CURSOR FOR
                                  SELECT "paragraph_property".*
                                    FROM "paragraph_property"
                                   WHERE "paragraph_property"."paragraphId" = "p_paragraph_id"
                                     AND "paragraph_property"."locale"      = '*';
    "c_properties"      NO SCROLL CURSOR FOR
                                  SELECT "paragraph_property".*
                                    FROM "paragraph"
                                    JOIN "paragraph_property"
                                      ON "paragraph_property"."paragraphId" = "paragraph"."id"
                                   WHERE "paragraph"."rootId"               = "p_paragraph_id"
                                     AND "paragraph_property"."locale"      = "p_locale"
                                ORDER BY "paragraph"."left"     ASC,
                                         "paragraph"."right"    DESC;
BEGIN

    IF NOT EXISTS( SELECT TRUE
                     FROM INFORMATION_SCHEMA.TABLES
                    WHERE TABLE_SCHEMA = CURRENT_SCHEMA
                      AND TABLE_NAME   = 'search' ) THEN

        RETURN FALSE;

    END IF;

    FOR "r_properties" IN "c_rootproperties" LOOP

        BEGIN

            CASE "r_properties"."name"

                WHEN 'published' THEN
                    "v_published" = "r_properties"."value" IS NOT NULL
                                AND "r_properties"."value" NOT IN ( '', '0', 'f' );

                WHEN 'publishedFrom' THEN
                    IF "r_properties"."value" IS NOT NULL AND '' <> "r_properties"."value" THEN
                        "v_published_from" = CAST( "r_properties"."value" AS TIMESTAMP WITH TIME ZONE );
                    END IF;

                WHEN 'publishedTo' THEN
                    IF "r_properties"."value" IS NOT NULL AND '' <> "r_properties"."value" THEN
                        "v_published_to" = CAST( "r_properties"."value" AS TIMESTAMP WITH TIME ZONE );
                    END IF;

                WHEN 'allAccess' THEN
                    "v_all_access" = "r_properties"."value" IS NOT NULL
                                 AND "r_properties"."value" NOT IN ( '', '0', 'f' );

                WHEN 'accessGroups' THEN
                    "v_access_groups" = "v_access_groups" || CAST(
                        string_to_array(
                            regexp_replace(
                                COALESCE( "r_properties"."value", '' ),
                                '[^0-9,\.]',
                                '',
                                'g'
                            ),
                            ',',
                            ''
                        ) AS INTEGER ARRAY
                    );

                WHEN 'accessUsers' THEN
                    "v_access_users" = "v_access_users" || CAST(
                        string_to_array(
                            regexp_replace(
                                COALESCE( "r_properties"."value", '' ),
                                '[^0-9,\.]',
                                '',
                                'g'
                            ),
                            ',',
                            ''
                        ) AS INTEGER ARRAY
                    );

                ELSE

                    IF "r_properties"."name" LIKE 'accessGroups.%' THEN
                        "v_access_groups" = "v_access_groups" || CAST( "r_properties"."value" AS INTEGER );
                    END IF;

                    IF "r_properties"."name" LIKE 'accessUsers.%' THEN
                        "v_access_users" = "v_access_users" || CAST( "r_properties"."value" AS INTEGER );
                    END IF;

            END CASE;

        EXCEPTION

            WHEN invalid_text_representation
              OR invalid_datetime_format
              OR datetime_field_overflow THEN
                NULL;

        END;

    END LOOP;

    FOR "r_tags" IN "c_tags" LOOP
        "v_keywords" = "v_keywords" || "r_tags"."name" || ' ';
    END LOOP;

    FOR "r_properties" IN "c_properties" LOOP

        "v_value" = "r_properties"."value";

        IF "v_value" IS NULL OR '' = "v_value" THEN
            CONTINUE;
        END IF;

        IF "v_value" ~ '^\s*<' OR "v_value" ~ '<(p|div|b|i|strong|span)[\s>]' THEN
            -- strip scripts, styles
            "v_value" = regexp_replace( "v_value", '<(script|style)(\s[^>]*)?>.*?</>', '', 'gi' );
        ELSE
            -- convert to html
            "v_value" = REPLACE( "v_value", '&', '&amp;' );
            "v_value" = REPLACE( "v_value", '<', '&lt;' );
            "v_value" = REPLACE( "v_value", '>', '&gt;' );
            "v_value" = REPLACE( "v_value", '"', '&quot;' );
            "v_value" = '<p>' || "v_value" || '</p>';
        END IF;

        IF "r_properties"."paragraphId" = "p_paragraph_id" THEN

            CASE "r_properties"."name"

                WHEN 'title' THEN
                    "v_value" = regexp_replace( "v_value", '<[^>]*>', '', 'g' );
                    "v_value" = REPLACE( "v_value", '&quot;', '"' );
                    "v_value" = REPLACE( "v_value", '&gt;', '>' );
                    "v_value" = REPLACE( "v_value", '&lt;', '<' );
                    "v_value" = REPLACE( "v_value", '&amp;', '&' );
                    "v_title" = "v_title" || "v_value" || ' ';

                WHEN 'metaKeywords' THEN
                    "v_keywords" = "v_keywords" || "v_value" || ' ';

                WHEN 'metaDescription', 'leadText' THEN
                    "v_description" = "v_description" || "v_value" || ' ';

                ELSE
                    "v_content" = "v_content" || "v_value" || ' ';

            END CASE;

        ELSE

            "v_content" = "v_content" || "v_value" || ' ';

        END IF;

    END LOOP;

    RETURN "search_update"(
        "search_content_update"(
            'paragraph.content',
            "p_paragraph_id",
            "v_published",
            "v_published_from",
            "v_published_to",
            "v_all_access",
            "v_access_groups",
            "v_access_users"
        ),
        "p_locale",
        TRIM( BOTH ' ' FROM "v_title" ),
        TRIM( BOTH ' ' FROM "v_keywords" ),
        TRIM( BOTH ' ' FROM "v_description" ),
        TRIM( BOTH ' ' FROM "v_content" )
    );

END $$;

--------------------------------------------------------------------------------
-- function: paragraph_update_all_search_content()                            --
--------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION "paragraph_update_all_search_content"()
          RETURNS BOOLEAN
              SET search_path FROM CURRENT
         LANGUAGE SQL
               AS $$

    SELECT bool_or( "paragraph_update_search_content"( "root"."id", "paragraph_property"."locale" ) )
      FROM "paragraph"
INNER JOIN "paragraph" AS "root"
        ON "paragraph"."rootId"                 = "root"."id"
       AND "root"."type"                        = 'content'
INNER JOIN "paragraph_property"
        ON "paragraph_property"."paragraphId"   = "paragraph"."id"
       AND "paragraph_property"."locale"        IS NOT NULL
       AND "paragraph_property"."locale"        NOT IN ( '', '*' )
  GROUP BY "root"."id",
           "paragraph_property"."locale";

$$;

--------------------------------------------------------------------------------
-- function: paragraph_update_search_content_trigger()                        --
--------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION "paragraph_update_search_content_trigger"()
          RETURNS TRIGGER
              SET search_path FROM CURRENT
         LANGUAGE plpgsql
               AS $$
DECLARE
    "r_row"     RECORD;
    "v_root"    INTEGER;
BEGIN

    IF TG_OP = 'DELETE' THEN
        "r_row" = OLD;
    ELSE
        "r_row" = NEW;
    END IF;

    IF NOT EXISTS( SELECT TRUE
                     FROM INFORMATION_SCHEMA.TABLES
                    WHERE TABLE_SCHEMA = CURRENT_SCHEMA
                      AND TABLE_NAME   = 'search' ) THEN

        RETURN "r_row";

    END IF;

    SELECT "rootId"
      INTO "v_root"
      FROM "paragraph"
     WHERE "id" = "r_row"."paragraphId";

    IF EXISTS( SELECT *
                 FROM "paragraph"
                WHERE "id"    = "v_root"
                  AND "type"  = 'content' ) THEN

        SELECT "paragraph_update_search_content"(
            "v_root",
            "r_row"."locale"
        );

    END IF;

    RETURN "r_row";

END $$;

--------------------------------------------------------------------------------
-- trigger: 1000_search_update_paragraph_content                              --
--------------------------------------------------------------------------------

CREATE TRIGGER "1000_paragraph_update_search_content"
         AFTER INSERT
            OR UPDATE
            OR DELETE
            ON "paragraph_property"
           FOR EACH ROW
       EXECUTE PROCEDURE "paragraph_update_search_content_trigger"();
