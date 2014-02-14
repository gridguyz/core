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
           "selector"           = '#tmp#clone ' || regexp_replace(
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

    INSERT INTO "customize_rule"
         SELECT "customize_rule_clone_tmp".*
           FROM "customize_rule_clone_tmp";

    INSERT INTO "customize_property"
         SELECT "customize_property_clone_tmp".*
           FROM "customize_property_clone_tmp";

    INSERT INTO "customize_extra"
         SELECT "customize_extra_clone_tmp".*
           FROM "customize_extra_clone_tmp";

    IF "v_copy_tags" THEN

        INSERT INTO "paragraph_x_tag" ( "paragraphId", "tagId" )
             SELECT -"paragraphId" AS "paragraphId", "tagId"
               FROM "paragraph_x_tag"
              WHERE "paragraphId" IN (
                        SELECT -"id"
                          FROM "paragraph_clone_tmp"
                    );

    END IF;

    -- replace ids with newly generated ones

    "v_new_rootId" = nextval( 'paragraph_id_seq' );

    UPDATE "paragraph"
       SET "id" = "v_new_rootId"
     WHERE "id" = -"p_rootId";

    UPDATE "paragraph"
       SET "rootId" = "v_new_rootId"
     WHERE "id"     < 0;

    UPDATE "paragraph"
       SET "id" = nextval( 'paragraph_id_seq' )
     WHERE "id" < 0;

    UPDATE "customize_rule"
       SET "id"         = nextval( 'customize_rule_id_seq' ),
           "selector"   = regexp_replace( "selector", '^#tmp#clone ' , '' )
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

    -- return with new ids

    RETURN "v_new_rootId";

END $$;

--------------------------------------------------------------------------------
-- table: customize_rule_x_paragraph                                          --
--------------------------------------------------------------------------------

CREATE TABLE "customize_rule_x_paragraph"
(
    "ruleId"        INTEGER NOT NULL,
    "paragraphId"   INTEGER NOT NULL,

    PRIMARY KEY ( "ruleId", "paragraphId" ),
    FOREIGN KEY ( "ruleId" )
     REFERENCES "customize_rule" ( "id" )
      ON UPDATE CASCADE
      ON DELETE CASCADE,
    FOREIGN KEY ( "paragraphId" )
     REFERENCES "paragraph" ( "id" )
      ON UPDATE CASCADE
      ON DELETE CASCADE
);

INSERT INTO "customize_rule_x_paragraph" ( "ruleId", "paragraphId" )
     SELECT "id", "paragraphId"
       FROM "customize_rule";

--------------------------------------------------------------------------------
-- function: customize_rule_insert_update_selector_trigger                    --
--------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION "customize_rule_insert_update_selector_trigger"()
                   RETURNS TRIGGER
                       SET search_path FROM CURRENT
                  LANGUAGE plpgsql
                        AS $$
DECLARE
    "v_match"       TEXT[];
    "v_insert"      INTEGER;
    "v_preserve"    INTEGER[]   DEFAULT CAST( ARRAY[] AS INTEGER[] );
BEGIN

    FOR "v_match" IN SELECT regexp_matches( NEW."selector",
                                            '#paragraph-(-?\d+)',
                                            'g' ) LOOP

        "v_insert" = CAST( "v_match"[1] AS INTEGER );

        IF EXISTS ( SELECT *
                      FROM "paragraph"
                     WHERE "id" = "v_insert" ) THEN

            IF NOT EXISTS ( SELECT *
                              FROM "customize_rule_x_paragraph"
                             WHERE "ruleId"         = NEW."id"
                               AND "paragraphId"    = "v_insert" ) THEN

                INSERT INTO "customize_rule_x_paragraph" ( "ruleId", "paragraphId" )
                     VALUES ( NEW."id", "v_insert" );

            END IF;

            "v_preserve" = array_append( "v_preserve", "v_insert" );

        END IF;

    END LOOP;

    DELETE FROM "customize_rule_x_paragraph"
          WHERE "ruleId"        = NEW."id"
            AND "paragraphId"   <> ALL ( "v_preserve" );

    RETURN NEW;

END $$;

--------------------------------------------------------------------------------
-- trigger: customize_rule.1000__insert_update_selector                       --
--------------------------------------------------------------------------------

CREATE TRIGGER "1000__insert_update_selector"
         AFTER INSERT
            OR UPDATE OF "selector"
            ON "customize_rule"
           FOR EACH ROW
       EXECUTE PROCEDURE "customize_rule_insert_update_selector_trigger"();

UPDATE "customize_rule"
   SET "selector" = "selector" || '';

--------------------------------------------------------------------------------
-- function: customize_rule_x_paragraph_update_paragraphId_trigger()          --
--------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION "customize_rule_x_paragraph_update_paragraphId_trigger"()
                   RETURNS TRIGGER
                       SET search_path FROM CURRENT
                  LANGUAGE plpgsql
                        AS $$
BEGIN

    UPDATE "customize_rule"
       SET "selector" = regexp_replace(
               "selector",
               '#paragraph-' || TEXT( OLD."paragraphId" ) || '([^\d]|$)',
               '#paragraph-' || TEXT( NEW."paragraphId" ) || '\1',
               'g'
           )
     WHERE "id" = NEW."ruleId";

    RETURN NEW;

END $$;

--------------------------------------------------------------------------------
-- trigger: customize_rule_x_paragraph.1000__update_paragraphId               --
--------------------------------------------------------------------------------

CREATE TRIGGER "1000__update_paragraphId"
         AFTER UPDATE OF "paragraphId"
            ON "customize_rule_x_paragraph"
           FOR EACH ROW
       EXECUTE PROCEDURE "customize_rule_x_paragraph_update_paragraphId_trigger"();

--------------------------------------------------------------------------------
-- function: customize_rule_x_paragraph_delete_trigger()                      --
--------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION "customize_rule_x_paragraph_delete_trigger"()
                   RETURNS TRIGGER
                       SET search_path FROM CURRENT
                  LANGUAGE plpgsql
                        AS $$
BEGIN

    IF NOT EXISTS( SELECT *
                     FROM "paragraph"
                    WHERE "id" = OLD."paragraphId" ) THEN

        DELETE FROM "customize_rule"
              WHERE "id" = OLD."ruleId";

    END IF;

    RETURN OLD;

END $$;

--------------------------------------------------------------------------------
-- trigger: customize_rule_x_paragraph.1000__delete                           --
--------------------------------------------------------------------------------

CREATE TRIGGER "1000__delete"
         AFTER DELETE
            ON "customize_rule_x_paragraph"
           FOR EACH ROW
       EXECUTE PROCEDURE "customize_rule_x_paragraph_delete_trigger"();

--------------------------------------------------------------------------------
-- table: customize_rule                                                      --
--------------------------------------------------------------------------------

ALTER TABLE "customize_rule"
       DROP CONSTRAINT "customize_rule_paragraphId_fkey" CASCADE;

ALTER TABLE "customize_rule"
     RENAME "paragraphId"
         TO "rootParagraphId";

UPDATE "customize_rule"
   SET "rootParagraphId" = (
           SELECT "rootId"
             FROM "paragraph"
            WHERE "id" = "rootParagraphId"
       );

ALTER TABLE "customize_rule"
        ADD FOREIGN KEY ( "rootParagraphId" )
             REFERENCES "paragraph" ( "id" )
               ON UPDATE CASCADE
               ON DELETE CASCADE;

ALTER TABLE "customize_rule"
       DROP CONSTRAINT IF EXISTS "customize_rule_selector_media_key" CASCADE;

CREATE UNIQUE INDEX "customize_rule_selector_media_rootParagraphId_idx"
                 ON "customize_rule" (
                        "selector",
                        "media",
                        COALESCE( "rootParagraphId", 0 )
                    );

--------------------------------------------------------------------------------
-- function: customize_insert_update_rootParagraphId_trigger()                --
--------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION "customize_insert_update_rootParagraphId_trigger"()
                   RETURNS TRIGGER
                       SET search_path FROM CURRENT
                  LANGUAGE plpgsql
                        AS $$
BEGIN

    IF NEW."rootParagraphId" IS NOT NULL THEN

        NEW."rootParagraphId" = (
            SELECT "rootId"
              FROM "paragraph"
             WHERE "id" = NEW."rootParagraphId"
        );

    END IF;

    RETURN NEW;

END $$;

--------------------------------------------------------------------------------
-- trigger: customize_rule_x_paragraph.1000__insert_update_rootParagraphId    --
--------------------------------------------------------------------------------

CREATE TRIGGER "1000__insert_update_rootParagraphId"
        BEFORE INSERT
            OR UPDATE OF "rootParagraphId"
            ON "customize_rule"
           FOR EACH ROW
       EXECUTE PROCEDURE "customize_insert_update_rootParagraphId_trigger"();

--------------------------------------------------------------------------------
-- table: customize_extra                                                     --
--------------------------------------------------------------------------------

CREATE TABLE "customize_extra"
(
    "id"                SERIAL                      NOT NULL,
    "rootParagraphId"   INTEGER                     NULL,
    "extra"             TEXT                        NOT NULL,
    "updated"           TIMESTAMP WITH TIME ZONE    NOT NULL    DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY ( "id" ),
    FOREIGN KEY ( "rootParagraphId" )
     REFERENCES "paragraph" ( "id" )
      ON UPDATE CASCADE
      ON DELETE CASCADE
);

CREATE UNIQUE INDEX "customize_extra_rootParagraphId_idx"
                 ON "customize_extra" ( COALESCE( "rootParagraphId", 0 ) );

--------------------------------------------------------------------------------
-- function: customize_extra_update( int )                                    --
--------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION "customize_extra_update"( "pRootId" INTEGER )
                   RETURNS VOID
                    CALLED ON NULL INPUT
                       SET search_path FROM CURRENT
                  LANGUAGE plpgsql
                        AS $$
BEGIN

    IF "pRootId" IS NULL THEN

        UPDATE "customize_extra"
           SET "updated"            = CURRENT_TIMESTAMP
         WHERE "rootParagraphId"    IS NULL;

        IF NOT FOUND THEN

            INSERT INTO "customize_extra" ( "rootParagraphId",
                                            "extra",
                                            "updated" )
                 VALUES ( NULL,
                          '',
                          CURRENT_TIMESTAMP );

        END IF;

    ELSE

        UPDATE "customize_extra"
           SET "updated"            = CURRENT_TIMESTAMP
         WHERE "rootParagraphId"    = "pRootId";

        IF NOT FOUND AND EXISTS ( SELECT *
                                    FROM "paragraph"
                                   WHERE "id" = "pRootId" ) THEN

            INSERT INTO "customize_extra" ( "rootParagraphId",
                                            "extra",
                                            "updated" )
                 VALUES ( "pRootId",
                          '',
                          CURRENT_TIMESTAMP );

        END IF;

    END IF;

END $$;

--------------------------------------------------------------------------------
-- trigger: customize_extra.1000__insert_update_rootParagraphId               --
--------------------------------------------------------------------------------

CREATE TRIGGER "1000__insert_update_rootParagraphId"
         AFTER INSERT
            OR UPDATE OF "rootParagraphId"
            ON "customize_extra"
           FOR EACH ROW
       EXECUTE PROCEDURE "customize_insert_update_rootParagraphId_trigger"();

--------------------------------------------------------------------------------
-- function: customize_changes_trigger()                                      --
--------------------------------------------------------------------------------

DROP TRIGGER IF EXISTS "9000__customize_changes"
                    ON "customize_rule" CASCADE;

DROP TRIGGER IF EXISTS "9000__customize_changes"
                    ON "customize_property" CASCADE;

CREATE OR REPLACE FUNCTION "customize_changes_trigger"()
                   RETURNS TRIGGER
                       SET search_path FROM CURRENT
                  LANGUAGE plpgsql
                        AS $$
BEGIN

    IF TG_OP <> 'INSERT' THEN
        PERFORM "customize_extra_update"( OLD."rootParagraphId" );
    END IF;

    IF TG_OP <> 'DELETE' THEN
        PERFORM "customize_extra_update"( NEW."rootParagraphId" );
        RETURN NEW;
    END IF;

    RETURN OLD;

END $$;

--------------------------------------------------------------------------------
-- trigger: customize_extra.9000__customize_changes                           --
--------------------------------------------------------------------------------

CREATE TRIGGER "9000__customize_changes"
         AFTER INSERT
            OR UPDATE OF "extra"
            ON "customize_extra"
           FOR EACH ROW
       EXECUTE PROCEDURE "customize_changes_trigger"();

--------------------------------------------------------------------------------
-- trigger: customize_rule.9000__customize_changes                            --
--------------------------------------------------------------------------------

CREATE TRIGGER "9000__customize_changes"
         AFTER INSERT
            OR UPDATE
            OR DELETE
            ON "customize_rule"
           FOR EACH ROW
       EXECUTE PROCEDURE "customize_changes_trigger"();

--------------------------------------------------------------------------------
-- function: customize_property_changes_trigger()                             --
--------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION "customize_property_changes_trigger"()
                   RETURNS TRIGGER
                       SET search_path FROM CURRENT
                  LANGUAGE plpgsql
                        AS $$
BEGIN

    IF TG_OP <> 'INSERT' THEN

        PERFORM "customize_extra_update"( (
            SELECT "rootParagraphId"
              FROM "customize_rule"
             WHERE "id" = OLD."ruleId"
        ) );

    END IF;

    IF TG_OP <> 'DELETE' THEN

        IF TG_OP = 'INSERT' OR OLD."ruleId" <> NEW."ruleId" THEN
            PERFORM "customize_extra_update"( (
                SELECT "rootParagraphId"
                  FROM "customize_rule"
                 WHERE "id" = NEW."ruleId"
            ) );
        END IF;

        RETURN NEW;

    END IF;

    RETURN OLD;

END $$;

--------------------------------------------------------------------------------
-- trigger: customize_property.9000__customize_property_changes               --
--------------------------------------------------------------------------------

CREATE TRIGGER "9000__customize_property_changes"
         AFTER INSERT
            OR UPDATE
            OR DELETE
            ON "customize_property"
           FOR EACH ROW
       EXECUTE PROCEDURE "customize_property_changes_trigger"();

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

--------------------------------------------------------------------------------
-- trigger: paragraph.2000__customize_paragraph_root_changes                  --
--------------------------------------------------------------------------------

CREATE TRIGGER "2000__customize_paragraph_root_changes"
         AFTER UPDATE OF "rootId"
            ON "paragraph"
           FOR EACH ROW
       EXECUTE PROCEDURE "customize_paragraph_root_changes_trigger"();
