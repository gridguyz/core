--------------------------------------------------------------------------------
-- function: paragraph_clone( varchar, int )                                  --
--------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION "paragraph_clone"(
    "p_source_schema"   CHARACTER VARYING,
    "p_rootId"          INTEGER
)
     RETURNS INTEGER
         SET search_path FROM CURRENT
    LANGUAGE plpgsql
          AS $$
DECLARE
    "v_new_rootId"  INTEGER;
    "v_copy_tags"   BOOLEAN DEFAULT FALSE;
BEGIN

    IF "p_source_schema" IS NULL THEN
        "p_source_schema"   = CURRENT_SCHEMA;
        "v_copy_tags"       = TRUE;
    ELSE
        "v_copy_tags"       = ( "p_source_schema" = CURRENT_SCHEMA );
    END IF;

    -- create temporary tables for clone

    DROP TABLE IF EXISTS "paragraph_clone_tmp";
    DROP TABLE IF EXISTS "paragraph_property_clone_tmp";
    DROP TABLE IF EXISTS "customize_rule_clone_tmp";
    DROP TABLE IF EXISTS "customize_property_clone_tmp";
    DROP TABLE IF EXISTS "customize_extra_clone_tmp";

    EXECUTE format(
        'CREATE TEMP TABLE "paragraph_clone_tmp" AS ' ||
        'SELECT * FROM %I."paragraph" ' ||
        'WHERE "rootId" = %L ' ||
        'ORDER BY "left" ASC, "right" DESC, "id" ASC',
        "p_source_schema",
        "p_rootId"
    );

    EXECUTE format(
        'CREATE TEMP TABLE "paragraph_property_clone_tmp" AS ' ||
        'SELECT * FROM %I."paragraph_property"' ||
        'WHERE "paragraphId" IN ( SELECT "id" FROM "paragraph_clone_tmp" )',
        "p_source_schema",
        "p_rootId"
    );

    EXECUTE format(
        'CREATE TEMP TABLE "customize_rule_clone_tmp" AS ' ||
        'SELECT * FROM %I."customize_rule"' ||
        'WHERE "rootParagraphId" IN ( SELECT "id" FROM "paragraph_clone_tmp" )',
        "p_source_schema",
        "p_rootId"
    );

    EXECUTE format(
        'CREATE TEMP TABLE "customize_property_clone_tmp" AS ' ||
        'SELECT * FROM %I."customize_property"' ||
        'WHERE "ruleId" IN ( SELECT "id" FROM "customize_rule_clone_tmp" )',
        "p_source_schema",
        "p_rootId"
    );

    EXECUTE format(
        'CREATE TEMP TABLE "customize_extra_clone_tmp" AS ' ||
        'SELECT * FROM %I."customize_extra"' ||
        'WHERE "rootParagraphId" IN ( SELECT "id" FROM "paragraph_clone_tmp" )',
        "p_source_schema",
        "p_rootId"
    );

    -- update ids to preserve uniqueness

    UPDATE "paragraph_clone_tmp"
       SET "id"     = -"id",
           "rootId" = -"rootId";

    UPDATE "paragraph_property_clone_tmp"
       SET "paragraphId" = -"paragraphId";

    UPDATE "customize_rule_clone_tmp"
       SET "id"                 = -"id",
           "rootParagraphId"    = -"rootParagraphId",
           "selector"           = regexp_replace(
               "selector",
               '#paragraph-(\d+)',
               '#paragraph--\1',
               'g'
           );

    UPDATE "customize_property_clone_tmp"
       SET "id"     = -"id",
           "ruleId" = -"ruleId";

    UPDATE "customize_extra_clone_tmp"
       SET "id"                 = -"id",
           "rootParagraphId"    = -"rootParagraphId";

    -- insert data back with negative ids

    INSERT INTO "paragraph"
         SELECT "paragraph_clone_tmp".*
           FROM "paragraph_clone_tmp";

    INSERT INTO "paragraph_property"
         SELECT "paragraph_property_clone_tmp".*
           FROM "paragraph_property_clone_tmp"
      LEFT JOIN "paragraph_property"
             ON "paragraph_property"."paragraphId"  = "paragraph_property_clone_tmp"."paragraphId"
            AND "paragraph_property"."locale"       = "paragraph_property_clone_tmp"."locale"
            AND "paragraph_property"."name"         = "paragraph_property_clone_tmp"."name"
          WHERE "paragraph_property"."paragraphId"  IS NULL;

    INSERT INTO "customize_extra"
         SELECT "customize_extra_clone_tmp".*
           FROM "customize_extra_clone_tmp";

    INSERT INTO "customize_rule"
         SELECT "customize_rule_clone_tmp".*
           FROM "customize_rule_clone_tmp";

    INSERT INTO "customize_property"
         SELECT "customize_property_clone_tmp".*
           FROM "customize_property_clone_tmp";

    IF "v_copy_tags" THEN

        INSERT INTO "paragraph_x_tag" ( "paragraphId", "tagId" )
             SELECT -"paragraphId" AS "paragraphId", "tagId"
               FROM "paragraph_x_tag"
              WHERE "paragraphId" IN (
                        SELECT -"id"
                          FROM "paragraph_clone_tmp"
                    );

    END IF;

    DELETE FROM "paragraph_property"
          WHERE "locale"        = '*'
            AND "name"          = 'lastModified'
            AND "paragraphId"   < 0;

    -- replace ids with newly generated ones

    "v_new_rootId" = nextval( 'paragraph_id_seq' );

    UPDATE "paragraph"
       SET "id"     = "v_new_rootId",
           "rootId" = "v_new_rootId"
     WHERE "id"     = -"p_rootId";

    UPDATE "paragraph"
       SET "rootId" = "v_new_rootId"
     WHERE "id"     < 0;

    UPDATE "paragraph"
       SET "id" = nextval( 'paragraph_id_seq' )
     WHERE "id" < 0;

    UPDATE "customize_rule"
       SET "id" = nextval( 'customize_rule_id_seq' )
     WHERE "id" < 0;

    UPDATE "customize_property"
       SET "id" = nextval( 'customize_property_id_seq' )
     WHERE "id" < 0;

    UPDATE "customize_extra"
       SET "id" = nextval( 'customize_extra_id_seq' )
     WHERE "id" < 0;

    -- drop temporary tables

    DROP TABLE IF EXISTS "paragraph_clone_tmp";
    DROP TABLE IF EXISTS "paragraph_property_clone_tmp";
    DROP TABLE IF EXISTS "customize_rule_clone_tmp";
    DROP TABLE IF EXISTS "customize_property_clone_tmp";
    DROP TABLE IF EXISTS "customize_extra_clone_tmp";

    -- return with new pargraph id

    RETURN "v_new_rootId";

END $$;

--------------------------------------------------------------------------------
-- function: customize_paragraph_root_changes_trigger()                       --
--------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION "customize_paragraph_root_changes_trigger"()
                   RETURNS TRIGGER
                       SET search_path FROM CURRENT
                  LANGUAGE plpgsql
                        AS $$
DECLARE
    "vOldExtra" TEXT;
BEGIN

    IF OLD."rootId" = OLD."id" OR NEW."rootId" = NEW."id" THEN
        RETURN NEW;
    END IF;

    IF OLD."rootId" <> NEW."rootId" THEN

        UPDATE "customize_rule"
           SET "rootParagraphId" = NEW."rootId"
         WHERE "rootParagraphId" = OLD."rootId";

        IF NOT EXISTS ( SELECT *
                          FROM "paragraph"
                         WHERE "id" = OLD."rootId"
                           AND "id" = "rootId" ) THEN

            SELECT "extra"
              INTO "vOldExtra"
              FROM "customize_extra"
             WHERE "rootParagraphId" = OLD."rootId";

            IF FOUND THEN

                IF "vOldExtra" IS NOT NULL AND "vOldExtra" <> '' THEN

                    UPDATE "customize_extra"
                       SET "extra" = "extra" || ' ' || "vOldExtra"
                     WHERE "rootParagraphId" = NEW."rootId";

                    IF NOT FOUND THEN

                        INSERT INTO "customize_extra" ( "rootParagraphId",
                                                        "extra",
                                                        "updated" )
                             VALUES ( NEW."rootId",
                                      "vOldExtra",
                                      CURRENT_TIMESTAMP );

                    END IF;

                END IF;

                DELETE FROM "customize_extra"
                      WHERE "rootParagraphId" = OLD."rootId";

            END IF;

        END IF;

    END IF;

    RETURN NEW;

END $$;
