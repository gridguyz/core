--------------------------------------------------------------------------------
-- table: module                                                              --
--------------------------------------------------------------------------------

CREATE TABLE "module"
(
    "id"        SERIAL              NOT NULL,
    "module"    CHARACTER VARYING   NOT NULL,
    "enabled"   BOOLEAN             NOT NULL    DEFAULT FALSE,

    PRIMARY KEY ( "id" ),
    UNIQUE ( "module" )
);

--------------------------------------------------------------------------------
-- table: settings                                                            --
--------------------------------------------------------------------------------

CREATE TABLE "settings"
(
    "id"    SERIAL                      NOT NULL,
    "key"   CHARACTER VARYING           NOT NULL,
    "value" CHARACTER VARYING           NOT NULL,
    "type"  "_common"."settings_type"   NOT NULL,

    PRIMARY KEY ( "id" ),
    UNIQUE ( "key" )
);

--------------------------------------------------------------------------------
-- table: user_group                                                          --
--------------------------------------------------------------------------------

CREATE TABLE "user_group"
(
    "id"            SERIAL              NOT NULL,
    "name"          CHARACTER VARYING   NOT NULL,
    "predefined"    BOOLEAN             NOT NULL    DEFAULT FALSE,
    "default"       BOOLEAN             NOT NULL    DEFAULT FALSE,

    PRIMARY KEY ( "id" )
);

--------------------------------------------------------------------------------
-- table: user                                                                --
--------------------------------------------------------------------------------

CREATE TABLE "user"
(
    "id"              SERIAL                  NOT NULL,
    "email"           CHARACTER VARYING       NOT NULL,
    "displayName"     CHARACTER VARYING       NOT NULL,
    "passwordHash"    CHARACTER VARYING       NOT NULL,
    "state"           "_common"."user_state"  NOT NULL    DEFAULT 'active'::"_common"."user_state",
    "confirmed"       BOOLEAN                 NOT NULL    DEFAULT FALSE,
    "locale"          CHARACTER VARYING       NOT NULL    DEFAULT '',
    "avatar"          CHARACTER VARYING,
    "groupId"         INTEGER                 NOT NULL,

    PRIMARY KEY ( "id" ),
    UNIQUE ( "email" ),
    UNIQUE ( "displayName" ),
    FOREIGN KEY ( "groupId" )
     REFERENCES "user_group" ( "id" )
      ON UPDATE CASCADE
      ON DELETE RESTRICT
);

--------------------------------------------------------------------------------
-- table: user_identity                                                       --
--------------------------------------------------------------------------------

CREATE TABLE "user_identity"
(
    "id"          SERIAL              NOT NULL,
    "userId"      INTEGER             NOT NULL,
    "identity"    CHARACTER VARYING   NOT NULL,

    PRIMARY KEY ( "id" ),
    FOREIGN KEY ( "userId" )
     REFERENCES "user" ( "id" )
      ON UPDATE CASCADE
      ON DELETE CASCADE
);

--------------------------------------------------------------------------------
-- table: user_settings                                                       --
--------------------------------------------------------------------------------

CREATE TABLE "user_settings"
(
    "id"      SERIAL              NOT NULL,
    "userId"  INTEGER             NOT NULL,
    "section" CHARACTER VARYING   NOT NULL,
    "key"     CHARACTER VARYING   NOT NULL,
    "value"   TEXT,

    PRIMARY KEY ( "id" ),
    UNIQUE ( "userId", "section", "key" ),
    FOREIGN KEY ( "userId" )
     REFERENCES "user" ( "id" )
      ON UPDATE CASCADE
      ON DELETE CASCADE
);

--------------------------------------------------------------------------------
-- table: user_right                                                          --
--------------------------------------------------------------------------------

CREATE TABLE "user_right"
(
    "id"          SERIAL              NOT NULL,
    "label"       CHARACTER VARYING,
    "group"       CHARACTER VARYING   NOT NULL,
    "resource"    CHARACTER VARYING   NOT NULL,
    "privilege"   CHARACTER VARYING   NOT NULL,
    "optional"    BOOLEAN             NOT NULL    DEFAULT TRUE,
    "module"      CHARACTER VARYING,

    PRIMARY KEY ( "id" ),
    UNIQUE ( "group", "resource", "privilege" )
);

--------------------------------------------------------------------------------
-- table: user_right_x_user                                                   --
--------------------------------------------------------------------------------

CREATE TABLE "user_right_x_user"
(
    "rightId"   INTEGER     NOT NULL,
    "userId"    INTEGER     NOT NULL,

    PRIMARY KEY ( "rightId", "userId" ),
    FOREIGN KEY ( "rightId" )
     REFERENCES "user_right" ( "id" )
      ON UPDATE CASCADE
      ON DELETE CASCADE,
    FOREIGN KEY ( "userId" )
     REFERENCES "user" ( "id" )
      ON UPDATE CASCADE
      ON DELETE CASCADE
);

--------------------------------------------------------------------------------
-- table: user_right_x_user_group                                             --
--------------------------------------------------------------------------------

CREATE TABLE "user_right_x_user_group"
(
    "rightId"   INTEGER     NOT NULL,
    "groupId"   INTEGER     NOT NULL,

    PRIMARY KEY ( "rightId", "groupId" ),
    FOREIGN KEY ( "groupId" )
     REFERENCES "user_group" ( "id" )
      ON UPDATE CASCADE
      ON DELETE CASCADE,
    FOREIGN KEY ( "rightId" )
     REFERENCES "user_right" ( "id" )
      ON UPDATE CASCADE
      ON DELETE CASCADE
);

--------------------------------------------------------------------------------
-- table: tag                                                                 --
--------------------------------------------------------------------------------

CREATE TABLE "tag"
(
    "id"        SERIAL              NOT NULL,
    "locale"    CHARACTER VARYING,
    "name"      CHARACTER VARYING   NOT NULL,

    PRIMARY KEY ( "id" ),
    UNIQUE ( "name" )
);

--------------------------------------------------------------------------------
-- table: paragraph                                                           --
--------------------------------------------------------------------------------

CREATE TABLE "paragraph"
(
    "id"      SERIAL              NOT NULL,
    "type"    CHARACTER VARYING   NOT NULL,
    "rootId"  INTEGER             NOT NULL,
    "left"    INTEGER             NOT NULL,
    "right"   INTEGER             NOT NULL,
    "name"    CHARACTER VARYING,

    PRIMARY KEY ( "id" ),
    FOREIGN KEY ( "rootId" )
     REFERENCES "paragraph" ( "id" )
      ON UPDATE CASCADE
      ON DELETE CASCADE
);

CREATE INDEX ON "paragraph" ( "left"    ASC  );
CREATE INDEX ON "paragraph" ( "right"   DESC );
CREATE INDEX ON "paragraph" ( "rootId"  ASC  );

--------------------------------------------------------------------------------
-- table: paragraph_property                                                  --
--------------------------------------------------------------------------------

CREATE TABLE "paragraph_property"
(
    "paragraphId"   INTEGER             NOT NULL,
    "locale"        CHARACTER VARYING   NOT NULL,
    "name"          CHARACTER VARYING   NOT NULL,
    "value"         TEXT,

    PRIMARY KEY ( "paragraphId", "locale", "name" ),
    FOREIGN KEY ( "paragraphId" )
     REFERENCES "paragraph" ( "id" )
      ON UPDATE CASCADE
      ON DELETE CASCADE
);

CREATE INDEX ON "paragraph_property" ( "value" );

--------------------------------------------------------------------------------
-- table: paragraph_x_tag                                                     --
--------------------------------------------------------------------------------

CREATE TABLE "paragraph_x_tag"
(
  "paragraphId" INTEGER NOT NULL,
  "tagId"       INTEGER NOT NULL,

  PRIMARY KEY ( "paragraphId", "tagId" ),
  FOREIGN KEY ( "paragraphId" )
   REFERENCES "paragraph" ( "id" )
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  FOREIGN KEY ( "tagId" )
   REFERENCES "tag" ( "id" )
    ON UPDATE CASCADE
    ON DELETE CASCADE
);

--------------------------------------------------------------------------------
-- table: customize_rule                                                      --
--------------------------------------------------------------------------------

CREATE TABLE "customize_rule"
(
    "id"          SERIAL              NOT NULL,
    "selector"    CHARACTER VARYING   NOT NULL,
    "media"       CHARACTER VARYING   NOT NULL    DEFAULT '',
    "paragraphId" INTEGER,

    PRIMARY KEY ( "id" ),
    UNIQUE ( "selector", "media" ),
    FOREIGN KEY ( "paragraphId" )
     REFERENCES "paragraph" ( "id" )
      ON UPDATE CASCADE
      ON DELETE CASCADE
);

--------------------------------------------------------------------------------
-- table: customize_property                                                  --
--------------------------------------------------------------------------------

CREATE TABLE "customize_property"
(
    "id"        SERIAL              NOT NULL,
    "ruleId"    INTEGER             NOT NULL,
    "name"      CHARACTER VARYING   NOT NULL,
    "value"     CHARACTER VARYING   NOT NULL,
    "priority"  CHARACTER VARYING,

    PRIMARY KEY ( "id" ),
    FOREIGN KEY ( "ruleId" )
     REFERENCES "customize_rule" ( "id" )
      ON UPDATE CASCADE
      ON DELETE CASCADE
);

CREATE INDEX ON "customize_property" ( "ruleId", "name" );

--------------------------------------------------------------------------------
-- table: subdomain                                                           --
--------------------------------------------------------------------------------

CREATE TABLE "subdomain"
(
    "id"                SERIAL              NOT NULL,
    "subdomain"         CHARACTER VARYING   NOT NULL    DEFAULT '',
    "locale"            CHARACTER VARYING   NOT NULL    DEFAULT 'en',
    "defaultLayoutId"   INTEGER             NOT NULL,
    "defaultContentId"  INTEGER             NOT NULL,

    PRIMARY KEY ( "id" ),
    UNIQUE ( "subdomain" ),
    FOREIGN KEY ( "defaultContentId" )
     REFERENCES "paragraph" ( "id" )
      ON UPDATE CASCADE
      ON DELETE RESTRICT,
    FOREIGN KEY ( "defaultLayoutId" )
     REFERENCES "paragraph" ( "id" )
      ON UPDATE CASCADE
      ON DELETE RESTRICT
);

--------------------------------------------------------------------------------
-- table: content_uri                                                         --
--------------------------------------------------------------------------------

CREATE TABLE "content_uri"
(
    "id"            SERIAL              NOT NULL,
    "subdomainId"   INTEGER             NOT NULL,
    "contentId"     INTEGER             NOT NULL,
    "locale"        CHARACTER VARYING   NOT NULL,
    "uri"           CHARACTER VARYING   NOT NULL,
    "default"       BOOLEAN             NOT NULL    DEFAULT FALSE,

    PRIMARY KEY ( "id" ),
    UNIQUE ( "subdomainId", "uri" ),
    FOREIGN KEY ( "contentId" )
     REFERENCES "paragraph" ( "id" )
      ON UPDATE CASCADE
      ON DELETE CASCADE,
    FOREIGN KEY ( "subdomainId" )
     REFERENCES "subdomain" ( "id" )
      ON UPDATE CASCADE
      ON DELETE CASCADE
);

--------------------------------------------------------------------------------
-- table: menu                                                                --
--------------------------------------------------------------------------------

CREATE TABLE "menu"
(
    "id"        SERIAL              NOT NULL,
    "left"      INTEGER             NOT NULL,
    "right"     INTEGER             NOT NULL,
    "type"      CHARACTER VARYING   NOT NULL,
    "target"    CHARACTER VARYING,

    PRIMARY KEY ( "id" )
);

CREATE INDEX ON "menu" ( "left"  ASC  );
CREATE INDEX ON "menu" ( "right" DESC );

--------------------------------------------------------------------------------
-- table: menu_label                                                          --
--------------------------------------------------------------------------------

CREATE TABLE "menu_label"
(
    "id"        SERIAL              NOT NULL,
    "menuId"    INTEGER             NOT NULL,
    "locale"    CHARACTER VARYING   NOT NULL,
    "label"     CHARACTER VARYING   NOT NULL,

    PRIMARY KEY ( "id" ),
    UNIQUE ( "menuId", "locale" ),
    FOREIGN KEY ( "menuId" )
     REFERENCES "menu" ( "id" )
      ON UPDATE CASCADE
      ON DELETE CASCADE
);

--------------------------------------------------------------------------------
-- table: menu_property                                                       --
--------------------------------------------------------------------------------

CREATE TABLE "menu_property"
(
    "menuId"  INTEGER             NOT NULL,
    "name"    CHARACTER VARYING   NOT NULL,
    "value"   TEXT,

    PRIMARY KEY ( "menuId", "name" ),
    FOREIGN KEY ( "menuId" )
     REFERENCES "menu" ( "id" )
      ON UPDATE CASCADE
      ON DELETE CASCADE
);

CREATE INDEX ON "menu_property" ( "value" );

--------------------------------------------------------------------------------
-- table: mail_template                                                       --
--------------------------------------------------------------------------------

CREATE TABLE "mail_template"
(
    "id"            SERIAL              NOT NULL,
    "name"          CHARACTER VARYING   NOT NULL,
    "locale"        CHARACTER VARYING   NOT NULL,
    "fromAddress"   CHARACTER VARYING,
    "fromName"      CHARACTER VARYING,
    "subject"       CHARACTER VARYING   NOT NULL,
    "bodyHtml"      TEXT                NOT NULL,
    "bodyText"      TEXT,

    PRIMARY KEY ( "id" ),
    UNIQUE ( "name", "locale" )
);

--------------------------------------------------------------------------------
-- table: mail_template_param                                                 --
--------------------------------------------------------------------------------

CREATE TABLE "mail_template_param"
(
    "id"            SERIAL              NOT NULL,
    "templateName"  CHARACTER VARYING   NOT NULL,
    "paramName"     CHARACTER VARYING   NOT NULL,
    "required"      BOOLEAN             NOT NULL    DEFAULT FALSE,

    PRIMARY KEY ( "id" ),
    UNIQUE ( "templateName", "paramName" )
);

--------------------------------------------------------------------------------
-- function: settings__clear_cache()                                          --
--------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION "settings__clear_cache"()
                   RETURNS TRIGGER
                       SET search_path FROM CURRENT
                  LANGUAGE plpgsql
                        AS $$
DECLARE
    "v_record"          RECORD;
    "v_delete_cache"    BOOLEAN DEFAULT FALSE;
BEGIN

    IF TG_OP = 'INSERT' THEN
        "v_record" = NEW;

        IF NEW."type" = 'ini' THEN
            "v_delete_cache" = true;
        END IF;
    END IF;

    IF TG_OP = 'UPDATE' THEN
        "v_record" = NEW;

        IF OLD."type" = 'ini' OR NEW."type" = 'ini' THEN
            "v_delete_cache" = true;
        END IF;
    END IF;

    IF TG_OP = 'DELETE' THEN
        "v_record" = OLD;

        IF OLD."type" = 'ini' THEN
            "v_delete_cache" = true;
        END IF;
    END IF;

    IF "v_delete_cache" THEN
        -- TODO: maybe update enough
        DELETE FROM "settings"
              WHERE "key" = 'ini-cache'
                AND "type" = 'ini-cache';
    END IF;

    RETURN "v_record";

END $$;

--------------------------------------------------------------------------------
-- function: user_group__safe_delete()                                        --
--------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION "user_group__safe_delete"()
                   RETURNS TRIGGER
                       SET search_path FROM CURRENT
                  LANGUAGE plpgsql
                        AS $$
DECLARE
    "v_default" INTEGER;
BEGIN

    IF OLD."predefined" THEN
        RAISE EXCEPTION 'predefined user-groups cannot be deleted';
    END IF;

    IF OLD."default" THEN
        RAISE EXCEPTION 'default user-group cannot be deleted';
    END IF;

    SELECT "id"
      INTO "v_default"
      FROM "user_group"
     WHERE "default"
     LIMIT 1;

    UPDATE "user"
       SET "groupId" = "v_default"
     WHERE "groupId" = OLD."id";

    RETURN OLD;

END $$;

--------------------------------------------------------------------------------
-- function: user_group__single_default()                                     --
--------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION "user_group__single_default"()
                   RETURNS TRIGGER
                       SET search_path FROM CURRENT
                  LANGUAGE plpgsql
                        AS $$
BEGIN

    CASE TG_OP

        WHEN 'INSERT' THEN

            IF NEW."default" THEN

                UPDATE "user_group"
                   SET "default" = FALSE
                 WHERE "default"
                   AND "id" <> NEW."id";

            END IF;

            RETURN NEW;

        WHEN 'UPDATE' THEN

            IF NOT NEW."default" AND OLD."default" THEN

                IF NOT EXISTS( SELECT *
                                 FROM "user_group"
                                WHERE "default"
                                  AND "id" <> NEW."id" ) THEN

                    RAISE EXCEPTION '%: COUNT(user_group.default) should be 1', TG_OP;

                END IF;

            END IF;

            IF NEW."default" AND NOT OLD."default" THEN

                UPDATE "user_group"
                   SET "default" = FALSE
                 WHERE "default"
                   AND "id" <> NEW."id";

            END IF;

            RETURN NEW;

        WHEN 'DELETE' THEN

            IF OLD."default" THEN

                RAISE EXCEPTION '%: COUNT(user_group.default) should be 1', TG_OP;

            END IF;

            RETURN NEW;

        ELSE RAISE EXCEPTION 'Unexpected TG_OP: %', TG_OP;

    END CASE;

END $$;

--------------------------------------------------------------------------------
-- function: user_group__user_right_cascade()                                 --
--------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION "user_group__user_right_cascade"()
                   RETURNS TRIGGER
                       SET search_path FROM CURRENT
                  LANGUAGE plpgsql
                        AS $$
BEGIN

    CASE TG_OP

        WHEN 'INSERT' THEN

            IF NOT NEW."predefined" THEN

                INSERT INTO "user_right" ( "label", "group", "resource", "privilege", "optional" )
                     SELECT NEW."name" AS "label",
                            "group",
                            'user.group.' || TEXT( NEW."id" ) AS "resource",
                            "privilege",
                            "optional"
                       FROM "user_right"
                      WHERE "resource" = 'user.group.' || (
                               SELECT TEXT( "id" )
                                 FROM "user_group"
                                WHERE "default"
                                LIMIT 1
                            );

            END IF;

        WHEN 'UPDATE' THEN

            IF NOT OLD."predefined" AND NOT NEW."predefined" THEN

                IF OLD."id" <> NEW."id" THEN

                    UPDATE "user_right"
                       SET "resource"   = 'user.group.' || TEXT( NEW."id" )
                     WHERE "resource"   = 'user.group.' || TEXT( OLD."id" );

                END IF;

                IF OLD."name" <> NEW."name" THEN

                    UPDATE "user_right"
                       SET "label"      = NEW."name"
                     WHERE "resource"   = 'user.group.' || TEXT( NEW."id" );

                END IF;

            END IF;

        WHEN 'DELETE' THEN

            IF NOT OLD."predefined" THEN

                DELETE FROM "user_right"
                      WHERE "resource" = 'user.group.' || TEXT( OLD."id" );

            END IF;

    END CASE;

    RETURN NEW;

END $$;

--------------------------------------------------------------------------------
-- function: user__insert_without_group()                                     --
--------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION "user__insert_without_group"()
                   RETURNS TRIGGER
                       SET search_path FROM CURRENT
                  LANGUAGE plpgsql
                        AS $$
BEGIN

    IF NEW."groupId" IS NULL THEN

        NEW."groupId" = (
            SELECT "id"
              FROM "user_group"
             WHERE "default"
             LIMIT 1
        );

    END IF;

    RETURN NEW;

END $$;

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

    DROP TABLE IF EXISTS "paragraph_clone_tmp";
    DROP TABLE IF EXISTS "paragraph_property_clone_tmp";
    DROP TABLE IF EXISTS "customize_rule_clone_tmp";
    DROP TABLE IF EXISTS "customize_property_clone_tmp";

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
        'WHERE "paragraphId" IN ( SELECT "id" FROM "paragraph_clone_tmp" )',
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


    UPDATE "paragraph_clone_tmp" SET "id" = -"id", "rootId" = -"rootId";
    UPDATE "paragraph_property_clone_tmp" SET "paragraphId" = -"paragraphId";
    UPDATE "customize_rule_clone_tmp" SET "id" = -"id", "paragraphId" = -"paragraphId", "selector" = '[tmp_clone] ' || "selector";
    UPDATE "customize_property_clone_tmp" SET "id" = -"id", "ruleId" = -"ruleId";


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

    IF "v_copy_tags" THEN

        INSERT INTO "paragraph_x_tag" ( "paragraphId", "tagId" )
             SELECT -"paragraphId" AS "paragraphId", "tagId"
               FROM "paragraph_x_tag"
              WHERE "paragraphId" IN (
                        SELECT -"id"
                          FROM "paragraph_clone_tmp"
                    );

    END IF;


    "v_new_rootId" = nextval( 'paragraph_id_seq' );

    UPDATE "paragraph" SET "id" = "v_new_rootId" WHERE "id" = -"p_rootId";
    UPDATE "paragraph" SET "rootId" = "v_new_rootId" WHERE "id" < 0;
    UPDATE "paragraph" SET "id" = nextval( 'paragraph_id_seq' ) WHERE "id" < 0;
    UPDATE "customize_rule" SET "selector" = regexp_replace( "selector", '#paragraph-\d+' , '#paragraph-' || "paragraphId", 'g' ) WHERE "id" < 0;
    UPDATE "customize_rule" SET "selector" = regexp_replace( "selector", '^\[tmp_clone\] ' , '' ) WHERE "id" < 0;
    UPDATE "customize_rule" SET "id" = nextval( 'customize_rule_id_seq' ) WHERE "id" < 0;
    UPDATE "customize_property" SET "id" = nextval( 'customize_property_id_seq' ) WHERE "id" < 0;


    DROP TABLE IF EXISTS "paragraph_clone_tmp";
    DROP TABLE IF EXISTS "paragraph_property_clone_tmp";
    DROP TABLE IF EXISTS "customize_rule_clone_tmp";
    DROP TABLE IF EXISTS "customize_property_clone_tmp";


    RETURN "v_new_rootId";
END $$;

--------------------------------------------------------------------------------
-- function: paragraph_clone( int )                                           --
--------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION "paragraph_clone"( INTEGER )
     RETURNS INTEGER
         SET search_path FROM CURRENT
    LANGUAGE SQL
          AS 'SELECT "paragraph_clone"( NULL, $1 );';

--------------------------------------------------------------------------------
-- function: paragraph_move( int, varchar, int )                              --
--------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION "paragraph_move"(
    "p_source_node"     INTEGER,
    "p_position"        CHARACTER VARYING,
    "p_related_node"    INTEGER
)
     RETURNS BOOLEAN
         SET search_path FROM CURRENT
    LANGUAGE plpgsql
          AS $$
DECLARE
    "v_source_root_id"  INTEGER;
    "v_source_left"     INTEGER;
    "v_source_right"    INTEGER;
    "v_target_root_id"  INTEGER;
    "v_target_left"     INTEGER;
 -- "v_target_right"    INTEGER;
BEGIN

    -- get original data

    SELECT "rootId",
           "left",
           "right"
      INTO "v_source_root_id",
           "v_source_left",
           "v_source_right"
      FROM "paragraph"
     WHERE "id" = "p_source_node";

    -- cut original node(s)

    UPDATE "paragraph"
       SET "left"   = "left"  - "v_source_right",
           "right"  = "right" - "v_source_right"
     WHERE "rootId" = "v_source_root_id"
       AND "left" BETWEEN "v_source_left" AND "v_source_right";


    UPDATE "paragraph"
       SET "left"   = "left" - "v_source_right" - 1 + "v_source_left"
     WHERE "rootId" = "v_source_root_id"
       AND "left"   > "v_source_left";

    UPDATE "paragraph"
       SET "right"  = "right" - "v_source_right" - 1 + "v_source_left"
     WHERE "rootId" = "v_source_root_id"
       AND "right"  > "v_source_left";


    CASE REGEXP_REPLACE( LOWER( "p_position" ), '[\\s_-](of|at|to|in)$', '' )

        WHEN 'prepend', 'first' THEN

            SELECT "rootId",
                   "left" + 1
              INTO "v_target_root_id",
                   "v_target_left"
              FROM "paragraph"
             WHERE "id" = "p_related_node";

        WHEN 'before', 'above' THEN

            SELECT "rootId",
                   "left"
              INTO "v_target_root_id",
                   "v_target_left"
              FROM "paragraph"
             WHERE "id" = "p_related_node";

        WHEN 'after', 'below', 'under' THEN

            SELECT "rootId",
                   "right" + 1
              INTO "v_target_root_id",
                   "v_target_left"
              FROM "paragraph"
             WHERE "id" = "p_related_node";

        WHEN 'append', 'last' THEN

            SELECT "rootId",
                   "right"
              INTO "v_target_root_id",
                   "v_target_left"
              FROM "paragraph"
             WHERE "id" = "p_related_node";

        ELSE
            RAISE EXCEPTION 'Unknown position: %', "p_position"
               USING HINT = 'Valid positions: before, after, append, prepend';

    END CASE;

    -- shift nodes at target

    UPDATE "paragraph"
       SET "left"   = "left" + "v_source_right" + 1 - "v_source_left"
     WHERE "rootId" = "v_target_root_id"
       AND "left"  >= "v_target_left";


    UPDATE "paragraph"
       SET "right"  = "right" + "v_source_right" + 1 - "v_source_left"
     WHERE "rootId" = "v_target_root_id"
       AND "right" >= "v_target_left";

     -- move nodes to empty place

    UPDATE "paragraph"
       SET "rootId" = "v_target_root_id",
           "left"   = "left"  + "v_target_left" + "v_source_right" - "v_source_left",
           "right"  = "right" + "v_target_left" + "v_source_right" - "v_source_left"
     WHERE "rootId" = "v_source_root_id"
       AND "left"  <= 0;

    RETURN TRUE;

END $$;

--------------------------------------------------------------------------------
-- function: paragraph_set_property( int, varchar, varchar, varchar )         --
--------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION "paragraph_set_property"(
    "p_paragraph_id"    INTEGER,
    "p_locale"          CHARACTER VARYING,
    "p_name"            CHARACTER VARYING,
    "p_value"           CHARACTER VARYING
)
     RETURNS BOOLEAN
         SET search_path FROM CURRENT
    LANGUAGE plpgsql
          AS $$
BEGIN

    IF NOT EXISTS ( SELECT "id"
                      FROM "paragraph"
                     WHERE "id" = "p_paragraph_id" ) THEN

        -- returns false on failure
        RETURN FALSE;

    END IF;

     -- try update property
    UPDATE "paragraph_property"
       SET "value"          = "p_value"
     WHERE "paragraphId"    = "p_paragraph_id"
       AND "locale"         = "p_locale"
       AND "name"           = "p_name";

    -- if not success, try insert property
    IF NOT FOUND THEN

        INSERT INTO "paragraph_property" ( "paragraphId", "locale", "name", "value" )
             VALUES ( "p_paragraph_id", "p_locale", "p_name", "p_value" );

    END IF;

    -- returns true on success
    RETURN TRUE;

END $$;

--------------------------------------------------------------------------------
-- function: paragraph_delete_children_trigger()                              --
--------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION "paragraph_delete_children_trigger"()
                   RETURNS TRIGGER
                       SET search_path FROM CURRENT
                  LANGUAGE plpgsql
                        AS $$
BEGIN

    -- if this is a root, "rootId_fkey" constraint will do the tricks
    -- if this is descendant of a deleted element, ascendant's trigger did it

    IF OLD."id" = OLD."rootId" OR OLD."left" < 0 THEN
        RETURN OLD;
    END IF;

    -- delete descendants

    UPDATE "paragraph"
       SET "left"   = - "left",
           "right"  = - "right"
     WHERE "rootId" = OLD."rootId"
       AND "left" BETWEEN OLD."left" + 1 AND OLD."right" - 1;

    DELETE FROM "paragraph"
          WHERE "rootId" = OLD."rootId"
            AND - "left" BETWEEN OLD."left" + 1 AND OLD."right" - 1;

    -- fill empty space left behind

    UPDATE "paragraph"
       SET "left"   = "left" - OLD."right" - 1 + OLD."left"
     WHERE "rootId" = OLD."rootId"
       AND "left"   > OLD."left";

    UPDATE "paragraph"
       SET "right"  = "right" - OLD."right" - 1 + OLD."left"
     WHERE "rootId" = OLD."rootId"
       AND "right"  > OLD."left";

    RETURN OLD;

END $$;

--------------------------------------------------------------------------------
-- function: paragraph_insert_trigger()                                       --
--------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION "paragraph_insert_trigger"()
                   RETURNS TRIGGER
                       SET search_path FROM CURRENT
                  LANGUAGE plpgsql
                        AS $$
BEGIN

    IF NEW."rootId" IS NULL THEN
        NEW."rootId" = NEW."id";
    END IF;

    IF NEW."left" IS NULL OR NEW."left" = 0 THEN
        NEW."left" = 1;
    END IF;

    IF NEW."right" IS NULL OR NEW."right" = 0 THEN
        NEW."right" = NEW."left" + 1;
    END IF;

    RETURN NEW;

END $$;

--------------------------------------------------------------------------------
-- function: paragraph_layout_delete_trigger()                                --
--------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION "paragraph_layout_delete_trigger"()
                   RETURNS TRIGGER
                       SET search_path FROM CURRENT
                  LANGUAGE plpgsql
                        AS $$
BEGIN

    UPDATE "paragraph_property"
       SET "value"  = NULL
     WHERE "name"   = 'layoutId'
       AND "value"  = TEXT( OLD."id" );

    RETURN NULL;

END $$;

--------------------------------------------------------------------------------
-- function: paragraph_update_root_lastModified_trigger()                     --
--------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION "paragraph_update_root_lastModified_trigger"()
                   RETURNS TRIGGER
                       SET search_path FROM CURRENT
                  LANGUAGE plpgsql
                        AS $$
DECLARE
    "v_root_id" INTEGER;
BEGIN

    IF TG_OP = 'DELETE' THEN
        -- on delete whole root skip all
        IF OLD."rootId" = OLD."id" THEN
            RETURN OLD;
        END IF;

        -- on delete use old rootId
        "v_root_id" = OLD."rootId";
    ELSE
        -- on else use new rootId
        "v_root_id" = NEW."rootId";
    END IF;

    PERFORM "paragraph_set_property"(
        "v_root_id",
        '*',
        'lastModified',
        TEXT( NOW() )
    );

    -- on update & relocate, do the same on old rootId only if id is not changed
    IF TG_OP = 'UPDATE' AND NEW."id" = OLD."id" AND NEW."rootId" != OLD."rootId" THEN

        PERFORM "paragraph_set_property"(
            OLD."rootId",
            '*',
            'lastModified',
            TEXT( NOW() )
        );

    END IF;

    -- return success
    IF TG_OP = 'DELETE' THEN
        RETURN OLD;
    ELSE
        RETURN NEW;
    END IF;

END $$;

--------------------------------------------------------------------------------
-- function: paragraph_property_update_root_lastModified_trigger()            --
--------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION "paragraph_property_update_root_lastModified_trigger"()
                   RETURNS TRIGGER
                       SET search_path FROM CURRENT
                  LANGUAGE plpgsql
                        AS $$
DECLARE
    "v_paragraph_id"    INTEGER;
    "v_root_id"         INTEGER;
BEGIN

    IF TG_OP = 'DELETE' THEN

        IF OLD."locale" = '*' AND OLD."name" = 'lastModified' THEN
            RETURN OLD;
        END IF;

        "v_paragraph_id" = OLD."paragraphId";

    ELSE

        IF NEW."locale" = '*' AND NEW."name" = 'lastModified' THEN
            RETURN NEW;
        END IF;

        "v_paragraph_id" = NEW."paragraphId";

    END IF;

    SELECT "rootId"
      INTO "v_root_id"
      FROM "paragraph"
     WHERE "id" = "v_paragraph_id";

    PERFORM "paragraph_set_property"(
        "v_root_id",
        '*',
        'lastModified',
        TEXT( NOW() )
    );

    IF TG_OP = 'DELETE' THEN
        RETURN OLD;
    ELSE
        RETURN NEW;
    END IF;

END $$;

--------------------------------------------------------------------------------
-- function: content_uri_after_insert_trigger()                               --
--------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION "content_uri_after_insert_trigger"()
                   RETURNS TRIGGER
                       SET search_path FROM CURRENT
                  LANGUAGE plpgsql
                        AS $$
DECLARE
    "v_count"   INTEGER;
BEGIN

    SELECT COUNT( * )
      INTO "v_count"
      FROM "content_uri"
     WHERE "id"            != NEW."id"
       AND "subdomainId"    = NEW."subdomainId"
       AND "contentId"      = NEW."contentId"
       AND "locale"         = NEW."locale"
       AND "default";

    IF "v_count" < 1 THEN

        IF NOT NEW."default" THEN

            UPDATE "content_uri"
               SET "default"    = TRUE
             WHERE "id"         = NEW."id";

        END IF;

    ELSIF "v_count" > 0 THEN

        IF NEW."default" THEN

            UPDATE "content_uri"
               SET "default"        = FALSE
             WHERE "id"            != NEW."id"
               AND "subdomainId"    = NEW."subdomainId"
               AND "contentId"      = NEW."contentId"
               AND "locale"         = NEW."locale"
               AND "default";

        END IF;

    END IF;

    RETURN NEW;

END $$;

--------------------------------------------------------------------------------
-- function: content_uri_after_update_default_trigger()                       --
--------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION "content_uri_after_update_default_trigger"()
                   RETURNS TRIGGER
                       SET search_path FROM CURRENT
                  LANGUAGE plpgsql
                        AS $$
BEGIN

    IF NEW."default" AND EXISTS (
        SELECT COUNT( * )
          FROM "content_uri"
         WHERE "id"            != NEW."id"
           AND "subdomainId"    = NEW."subdomainId"
           AND "contentId"      = NEW."contentId"
           AND "locale"         = NEW."locale"
           AND "default"
    ) THEN

        UPDATE "content_uri"
           SET "default"        = FALSE
         WHERE "id"            != NEW."id"
           AND "subdomainId"    = NEW."subdomainId"
           AND "contentId"      = NEW."contentId"
           AND "locale"         = NEW."locale"
           AND "default";

    END IF;

    RETURN NEW;

END $$;

--------------------------------------------------------------------------------
-- function: content_uri_after_update_uri_trigger()                           --
--------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION "content_uri_after_update_uri_trigger"()
                   RETURNS TRIGGER
                       SET search_path FROM CURRENT
                  LANGUAGE plpgsql
                        AS $$
BEGIN

    IF NEW."uri" != OLD."uri" THEN

        INSERT INTO "content_uri" (
            "subdomainId",
            "contentId",
            "locale",
            "uri",
            "default"
        ) VALUES (
            OLD."subdomainId",
            OLD."contentId",
            OLD."locale",
            OLD."uri",
            FALSE
        );

    END IF;

    RETURN NEW;

END $$;

--------------------------------------------------------------------------------
-- function: content_uri_before_update_default_trigger()                      --
--------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION "content_uri_before_update_default_trigger"()
                   RETURNS TRIGGER
                       SET search_path FROM CURRENT
                  LANGUAGE plpgsql
                        AS $$
BEGIN

    IF NOT NEW."default" AND NOT EXISTS(
        SELECT *
          FROM "content_uri"
         WHERE "id"            != NEW."id"
           AND "subdomainId"    = NEW."subdomainId"
           AND "contentId"      = NEW."contentId"
           AND "locale"         = NEW."locale"
           AND "default"
    ) THEN

        NEW."default" = TRUE;

    END IF;

    RETURN NEW;

END $$;

--------------------------------------------------------------------------------
-- function: customize_changes_trigger()                                      --
--------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION "customize_changes_trigger"()
                   RETURNS TRIGGER
                       SET search_path FROM CURRENT
                  LANGUAGE plpgsql
                        AS $$
DECLARE
    "v_new"  TEXT;
BEGIN

    SELECT "value"
      INTO "v_new"
      FROM "settings"
     WHERE "key" = 'modules.Grid\Customize.fileTemplate'
     LIMIT 1;

    "v_new" = REPLACE( "v_new", ':SCHEMA', CURRENT_SCHEMA() );
    "v_new" = REPLACE( "v_new", ':HASH', MD5( TEXT( CURRENT_TIMESTAMP ) ) );

    UPDATE "settings"
       SET "value"  = "v_new"
     WHERE "key"    = 'view_manager.head_defaults.headLink.customize.href';

    IF NOT FOUND THEN

        INSERT INTO "settings" ( "key", "value", "type" )
             VALUES ( 'view_manager.head_defaults.headLink.customize.href',
                      "v_new", 'ini'::"_common"."settings_type" );

    END IF;

    RETURN NULL;

END $$;

--------------------------------------------------------------------------------
-- function: menu_interleave_paragraph( int, int )                            --
--------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION "menu_interleave_paragraph"(
    "p_update_node" INTEGER,
    "p_like_node"   INTEGER
)
     RETURNS BOOLEAN
         SET search_path FROM CURRENT
    LANGUAGE plpgsql
          AS $$
DECLARE
    "v_fallback_menu"   INTEGER;
    "v_setto_menu"      INTEGER;

    "r_update"          "paragraph_property"%ROWTYPE;
    "c_update"          NO SCROLL
                    CURSOR FOR
                    SELECT "paragraph_property".*
                      FROM "paragraph_property"
                INNER JOIN "paragraph"
                        ON "paragraph"."id" = "paragraph_property"."paragraphId"
                INNER JOIN "paragraph" AS "ascendant"
                        ON "paragraph"."rootId" = "ascendant"."rootId"
                       AND "paragraph"."left" BETWEEN "ascendant"."left"
                                                  AND "ascendant"."right"
                     WHERE "ascendant"."id"             = "p_update_node"
                       AND "paragraph"."type"           = 'menu'
                       AND "paragraph_property"."name"  = 'menuId'
                  ORDER BY "paragraph"."left" ASC,
                           "paragraph"."right" DESC
                       FOR UPDATE
                        OF "paragraph_property";

    "r_like"            "paragraph_property"%ROWTYPE;
    "c_like"            NO SCROLL
                    CURSOR FOR
                    SELECT "paragraph_property".*
                      FROM "paragraph_property"
                INNER JOIN "paragraph"
                        ON "paragraph"."id" = "paragraph_property"."paragraphId"
                INNER JOIN "paragraph" AS "ascendant"
                        ON "paragraph"."rootId" = "ascendant"."rootId"
                       AND "paragraph"."left" BETWEEN "ascendant"."left"
                                                  AND "ascendant"."right"
                     WHERE "ascendant"."id"             = "p_like_node"
                       AND "paragraph"."type"           = 'menu'
                       AND "paragraph_property"."name"  = 'menuId'
                  ORDER BY "paragraph"."left" ASC,
                           "paragraph"."right" DESC;
BEGIN

      SELECT "id"
        INTO "v_fallback_menu"
        FROM "menu"
    ORDER BY "left" ASC
       LIMIT 1;

    IF NOT FOUND THEN
        RETURN FALSE;
    END IF;

    OPEN "c_like";

    FOR "r_update" IN "c_update" LOOP

        FETCH "c_like" INTO "r_like";

        IF FOUND THEN
            "v_setto_menu" = "r_like"."value";
        ELSE
            "v_setto_menu" = "v_fallback_menu";
        END IF;

        UPDATE "paragraph_property"
           SET "value" = "v_setto_menu"
         WHERE CURRENT OF "c_update";

    END LOOP;

    CLOSE "c_like";

    RETURN TRUE;

END $$;

--------------------------------------------------------------------------------
-- function: menu_move( int, varchar, int )                                   --
--------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION "menu_move"(
    "p_source_node"     INTEGER,
    "p_position"        CHARACTER VARYING,
    "p_related_node"    INTEGER
)
     RETURNS BOOLEAN
         SET search_path FROM CURRENT
    LANGUAGE plpgsql
          AS $$
DECLARE
    "v_source_left"     INTEGER;
    "v_source_right"    INTEGER;
    "v_target_left"     INTEGER;
 -- "v_target_right"    INTEGER;
BEGIN

    -- get original data

    SELECT "left",
           "right"
      INTO "v_source_left",
           "v_source_right"
      FROM "menu"
     WHERE "id" = "p_source_node";

    -- cut original node(s)

    UPDATE "menu"
       SET "left"   = "left"  - "v_source_right",
           "right"  = "right" - "v_source_right"
     WHERE "left" BETWEEN "v_source_left" AND "v_source_right";


    UPDATE "menu"
       SET "left"   = "left" - "v_source_right" - 1 + "v_source_left"
     WHERE "left"   > "v_source_left";

    UPDATE "menu"
       SET "right"  = "right" - "v_source_right" - 1 + "v_source_left"
     WHERE "right"  > "v_source_left";


    CASE REGEXP_REPLACE( LOWER( "p_position" ), '[\\s_-](of|at|to|in)$', '' )

        WHEN 'prepend', 'first' THEN

            SELECT "left" + 1
              INTO "v_target_left"
              FROM "menu"
             WHERE "id" = "p_related_node";

        WHEN 'before', 'above' THEN

            SELECT "left"
              INTO "v_target_left"
              FROM "menu"
             WHERE "id" = "p_related_node";

        WHEN 'after', 'below', 'under' THEN

            SELECT "right" + 1
              INTO "v_target_left"
              FROM "menu"
             WHERE "id" = "p_related_node";

        WHEN 'append', 'last' THEN

            SELECT "right"
              INTO "v_target_left"
              FROM "menu"
             WHERE "id" = "p_related_node";

        ELSE

            RAISE EXCEPTION 'Unknown position: %', "p_position"
               USING HINT = 'Valid positions: before, after, append, prepend';

    END CASE;

    -- shift nodes at target

    UPDATE "menu"
       SET "left"   = "left" + "v_source_right" + 1 - "v_source_left"
     WHERE "left"  >= "v_target_left";


    UPDATE "menu"
       SET "right"  = "right" + "v_source_right" + 1 - "v_source_left"
     WHERE "right" >= "v_target_left";

     -- move nodes to empty place

    UPDATE "menu"
       SET "left"   = "left"  + "v_target_left" + "v_source_right" - "v_source_left",
           "right"  = "right" + "v_target_left" + "v_source_right" - "v_source_left"
     WHERE "left"  <= 0;

    RETURN TRUE;

END $$;

--------------------------------------------------------------------------------
-- function: menu_delete_children_trigger()                                   --
--------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION "menu_delete_children_trigger"()
                   RETURNS TRIGGER
                       SET search_path FROM CURRENT
                  LANGUAGE plpgsql
                        AS $$
BEGIN

    -- if this is descendant of a deleted element, ascendant's trigger will do the tricks

    IF OLD."left" < 0 THEN
        RETURN OLD;
    END IF;

    -- delete descendants

    UPDATE "menu"
       SET "left"   = - "left",
           "right"  = - "right"
     WHERE "left" BETWEEN OLD."left" + 1 AND OLD."right" - 1;

    DELETE FROM "menu"
          WHERE - "left" BETWEEN OLD."left" + 1 AND OLD."right" - 1;

    -- fill empty space left behind

    UPDATE "menu"
       SET "left"   = "left" - OLD."right" - 1 + OLD."left"
     WHERE "left"   > OLD."left";

    UPDATE "menu"
       SET "right"  = "right" - OLD."right" - 1 + OLD."left"
     WHERE "right"  > OLD."left";

    RETURN OLD;

END $$;

--------------------------------------------------------------------------------
-- function: menu_insert_trigger()                                            --
--------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION "menu_insert_trigger"()
                   RETURNS TRIGGER
                       SET search_path FROM CURRENT
                  LANGUAGE plpgsql
                        AS $$
BEGIN

    IF NEW."left" IS NULL OR NEW."left" = 0 THEN

        IF ( SELECT COUNT( * ) FROM "menu" ) > 0 THEN

            NEW."left" = ( SELECT MAX( "right" ) FROM "menu" ) + 1;

        ELSE

            NEW."left" = 1;

        END IF;

    END IF;

    IF NEW."right" IS NULL OR NEW."right" = 0 THEN

        NEW."right" = NEW."left" + 1;

    END IF;

    RETURN NEW;

END $$;

--------------------------------------------------------------------------------
-- function: menu_paragraph_delete_trigger()                                  --
--------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION "menu_paragraph_delete_trigger"()
                   RETURNS TRIGGER
                       SET search_path FROM CURRENT
                  LANGUAGE plpgsql
                        AS $$
BEGIN

    UPDATE "paragraph_property"
       SET "value"  = NULL
     WHERE "name"   = 'menu'
       AND "value"  = TEXT( OLD."id" );

    RETURN NULL;

END $$;

--------------------------------------------------------------------------------
-- trigger: settings.1000__clear_ini_cache                                    --
--------------------------------------------------------------------------------

CREATE TRIGGER "1000__clear_ini_cache"
         AFTER INSERT
            OR UPDATE
            OR DELETE
            ON "settings"
           FOR EACH ROW
       EXECUTE PROCEDURE "settings__clear_cache"();

--------------------------------------------------------------------------------
-- trigger: user_group.1000__safe_delete                                      --
--------------------------------------------------------------------------------

CREATE TRIGGER "1000__safe_delete"
        BEFORE DELETE
            ON "user_group"
           FOR EACH ROW
       EXECUTE PROCEDURE "user_group__safe_delete"();

--------------------------------------------------------------------------------
-- trigger: user_group.1000__single_default                                   --
--------------------------------------------------------------------------------

CREATE TRIGGER "1000__single_default"
         AFTER INSERT
            OR UPDATE OF "default"
            OR DELETE
            ON "user_group"
           FOR EACH ROW
       EXECUTE PROCEDURE "user_group__single_default"();

--------------------------------------------------------------------------------
-- trigger: user_group.2000__user_right_cascade                               --
--------------------------------------------------------------------------------

CREATE TRIGGER "2000__user_right_cascade"
         AFTER INSERT
            OR UPDATE
            OR DELETE
            ON "user_group"
           FOR EACH ROW
       EXECUTE PROCEDURE "user_group__user_right_cascade"();

--------------------------------------------------------------------------------
-- trigger: user.0001__insert_without_group                                   --
--------------------------------------------------------------------------------

CREATE TRIGGER "0001__insert_without_group"
        BEFORE INSERT
            ON "user"
           FOR EACH ROW
       EXECUTE PROCEDURE "user__insert_without_group"();

--------------------------------------------------------------------------------
-- trigger: paragraph.1000__paragraph_delete_children                         --
--------------------------------------------------------------------------------

CREATE TRIGGER "1000__paragraph_delete_children"
         AFTER DELETE
            ON "paragraph"
           FOR EACH ROW
       EXECUTE PROCEDURE "paragraph_delete_children_trigger"();

--------------------------------------------------------------------------------
-- trigger: paragraph.1000__insert_before                                     --
--------------------------------------------------------------------------------

CREATE TRIGGER "1000__insert_before"
        BEFORE INSERT
            ON "paragraph"
           FOR EACH ROW
       EXECUTE PROCEDURE "paragraph_insert_trigger"();

--------------------------------------------------------------------------------
-- trigger: paragraph.1000__layout_delete                                     --
--------------------------------------------------------------------------------

CREATE TRIGGER "1000__layout_delete"
         AFTER DELETE
            ON "paragraph"
           FOR EACH ROW
          WHEN ( OLD."type" = 'layout' )
       EXECUTE PROCEDURE "paragraph_layout_delete_trigger"();

--------------------------------------------------------------------------------
-- trigger: paragraph.9000__update_root_lastModified                          --
--------------------------------------------------------------------------------

CREATE TRIGGER "9000__update_root_lastModified"
         AFTER INSERT
            OR UPDATE
            OR DELETE
            ON "paragraph"
           FOR EACH ROW
       EXECUTE PROCEDURE "paragraph_update_root_lastModified_trigger"();

--------------------------------------------------------------------------------
-- trigger: paragraph_property.9000__update_root_lastModified                 --
--------------------------------------------------------------------------------

CREATE TRIGGER "9000__update_root_lastModified"
         AFTER INSERT
            OR UPDATE
            OR DELETE
            ON "paragraph_property"
           FOR EACH ROW
       EXECUTE PROCEDURE "paragraph_property_update_root_lastModified_trigger"();

--------------------------------------------------------------------------------
-- trigger: customize_rule.9000__customize_changes                            --
--------------------------------------------------------------------------------

CREATE TRIGGER "9000__customize_changes"
         AFTER INSERT
            OR UPDATE
            OR DELETE
            OR TRUNCATE
            ON "customize_rule"
           FOR EACH STATEMENT
       EXECUTE PROCEDURE "customize_changes_trigger"();

--------------------------------------------------------------------------------
-- trigger: customize_property.9000__customize_changes                        --
--------------------------------------------------------------------------------

CREATE TRIGGER "9000__customize_changes"
         AFTER INSERT
            OR UPDATE
            OR DELETE
            OR TRUNCATE
            ON "customize_property"
           FOR EACH STATEMENT
       EXECUTE PROCEDURE "customize_changes_trigger"();

--------------------------------------------------------------------------------
-- trigger: content_uri.1000__after_insert                                    --
--------------------------------------------------------------------------------

CREATE TRIGGER "1000__after_insert"
         AFTER INSERT
            ON "content_uri"
           FOR EACH ROW
       EXECUTE PROCEDURE "content_uri_after_insert_trigger"();

--------------------------------------------------------------------------------
-- trigger: content_uri.1000__after_update_default                            --
--------------------------------------------------------------------------------

CREATE TRIGGER "1000__after_update_default"
         AFTER UPDATE OF "default"
            ON "content_uri"
           FOR EACH ROW
       EXECUTE PROCEDURE "content_uri_after_update_default_trigger"();

--------------------------------------------------------------------------------
-- trigger: content_uri.1000__after_update_uri                                --
--------------------------------------------------------------------------------

CREATE TRIGGER "1000__after_update_uri"
         AFTER UPDATE OF "uri"
            ON "content_uri"
           FOR EACH ROW
       EXECUTE PROCEDURE "content_uri_after_update_uri_trigger"();

--------------------------------------------------------------------------------
-- trigger: content_uri.1000__before_update_default                           --
--------------------------------------------------------------------------------

CREATE TRIGGER "1000__before_update_default"
        BEFORE UPDATE OF "default"
            ON "content_uri"
           FOR EACH ROW
       EXECUTE PROCEDURE "content_uri_before_update_default_trigger"();

--------------------------------------------------------------------------------
-- trigger: menu.1000__delete_children                                        --
--------------------------------------------------------------------------------

CREATE TRIGGER "1000__delete_children"
         AFTER DELETE
            ON "menu"
           FOR EACH ROW
       EXECUTE PROCEDURE "menu_delete_children_trigger"();

--------------------------------------------------------------------------------
-- trigger: menu.1000__insert                                                 --
--------------------------------------------------------------------------------

CREATE TRIGGER "1000__insert"
        BEFORE INSERT
            ON "menu"
           FOR EACH ROW
       EXECUTE PROCEDURE "menu_insert_trigger"();

--------------------------------------------------------------------------------
-- trigger: menu.1000__paragraph_delete                                       --
--------------------------------------------------------------------------------

CREATE TRIGGER "1000__paragraph_delete"
         AFTER DELETE
            ON "menu"
           FOR EACH ROW
       EXECUTE PROCEDURE "menu_paragraph_delete_trigger"();
