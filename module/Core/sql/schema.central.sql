--------------------------------------------------------------------------------
-- table: settings                                                            --
--------------------------------------------------------------------------------

CREATE TABLE "_central"."settings"
(
    "id"    SERIAL              NOT NULL,
    "key"   CHARACTER VARYING   NOT NULL,
    "value" CHARACTER VARYING,

    PRIMARY KEY ( "id" )
);

--------------------------------------------------------------------------------
-- table: paragraph                                                           --
--------------------------------------------------------------------------------

CREATE TABLE "_central"."paragraph"
(
    "id"        SERIAL              NOT NULL,
    "type"      CHARACTER VARYING   NOT NULL,
    "rootId"    INTEGER             NOT NULL,
    "left"      INTEGER             NOT NULL,
    "right"     INTEGER             NOT NULL,
    "name"      CHARACTER VARYING,

    PRIMARY KEY ( "id" ),
    FOREIGN KEY ( "rootId" )
     REFERENCES "_central"."paragraph" ( "id" )
      ON UPDATE CASCADE
      ON DELETE CASCADE
);

CREATE INDEX ON "_central"."paragraph" ( "left"  ASC  );
CREATE INDEX ON "_central"."paragraph" ( "right" DESC );

--------------------------------------------------------------------------------
-- table: paragraph_property                                                  --
--------------------------------------------------------------------------------

CREATE TABLE "_central"."paragraph_property"
(
    "paragraphId"   INTEGER             NOT NULL,
    "locale"        CHARACTER VARYING   NOT NULL,
    "name"          CHARACTER VARYING   NOT NULL,
    "value"         TEXT,

    PRIMARY KEY ( "paragraphId", "locale", "name" ),
    FOREIGN KEY ( "paragraphId" )
     REFERENCES "_central"."paragraph" ( "id" )
      ON UPDATE CASCADE
      ON DELETE CASCADE
);

CREATE INDEX ON "_central"."paragraph_property" ( "value" );

--------------------------------------------------------------------------------
-- table: customize_rule                                                      --
--------------------------------------------------------------------------------

CREATE TABLE "_central"."customize_rule"
(
    "id"            SERIAL              NOT NULL,
    "selector"      CHARACTER VARYING   NOT NULL,
    "media"         CHARACTER VARYING   NOT NULL    DEFAULT '',
    "paragraphId"   INTEGER,

    PRIMARY KEY ( "id" ),
    UNIQUE ( "selector", "media" ),
    FOREIGN KEY ( "paragraphId" )
     REFERENCES "_central"."paragraph" ( "id" )
      ON UPDATE CASCADE
      ON DELETE CASCADE
);

--------------------------------------------------------------------------------
-- table: customize_property                                                  --
--------------------------------------------------------------------------------

CREATE TABLE "_central"."customize_property"
(
    "id"        SERIAL              NOT NULL,
    "ruleId"    INTEGER             NOT NULL,
    "name"      CHARACTER VARYING   NOT NULL,
    "value"     CHARACTER VARYING   NOT NULL,
    "priority"  CHARACTER VARYING,

    PRIMARY KEY ( "id" ),
    UNIQUE ( "ruleId", "name" ),
    FOREIGN KEY ( "ruleId" )
     REFERENCES "_central"."customize_rule" ( "id" )
      ON UPDATE CASCADE
      ON DELETE CASCADE
);

--------------------------------------------------------------------------------
-- function: paragraph_insert_trigger                                         --
--------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION "_central"."paragraph_insert_trigger"()
                   RETURNS TRIGGER
                  LANGUAGE plpgsql
                        AS $$
BEGIN

    IF NEW."rootId" IS NULL THEN
        NEW."rootId" = NEW."id";
    END IF;

    RETURN NEW;

END $$;

--------------------------------------------------------------------------------
-- function: paragraph_layout_delete_trigger                                  --
--------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION "_central"."paragraph_layout_delete_trigger"()
                   RETURNS TRIGGER
                  LANGUAGE plpgsql
                        AS $$
BEGIN

    UPDATE "_central"."paragraph_property"
       SET "value"  = NULL
     WHERE "name"   = 'layoutId'
       AND "value"  = TEXT( OLD."id" );

    RETURN NULL;

END $$;

--------------------------------------------------------------------------------
-- trigger: paragraph.paragraph_insert_before                                 --
--------------------------------------------------------------------------------

CREATE TRIGGER "paragraph_insert_before"
        BEFORE INSERT
            ON "_central"."paragraph"
           FOR EACH ROW
       EXECUTE PROCEDURE "_central"."paragraph_insert_trigger"();

--------------------------------------------------------------------------------
-- trigger: paragraph.paragraph_layout_delete_after                           --
--------------------------------------------------------------------------------

CREATE TRIGGER "paragraph_layout_delete_after"
         AFTER DELETE
            ON "_central"."paragraph"
           FOR EACH ROW
          WHEN ( OLD."type" = 'layout' )
       EXECUTE PROCEDURE "_central"."paragraph_layout_delete_trigger"();
