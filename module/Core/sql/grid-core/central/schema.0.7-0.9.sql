--------------------------------------------------------------------------------
-- table: customize_rule                                                      --
--------------------------------------------------------------------------------

ALTER TABLE "_central"."customize_rule"
       DROP CONSTRAINT "customize_rule_paragraphId_fkey" CASCADE;

ALTER TABLE "_central"."customize_rule"
     RENAME "paragraphId"
         TO "rootParagraphId";

UPDATE "_central"."customize_rule"
   SET "rootParagraphId" = (
           SELECT "rootId"
             FROM "_central"."paragraph"
            WHERE "id" = "rootParagraphId"
       );

ALTER TABLE "_central"."customize_rule"
        ADD FOREIGN KEY ( "rootParagraphId" )
             REFERENCES "_central"."paragraph" ( "id" )
               ON UPDATE CASCADE
               ON DELETE CASCADE;

ALTER TABLE "_central"."customize_rule"
       DROP CONSTRAINT IF EXISTS "customize_rule_selector_media_key" CASCADE;

CREATE UNIQUE INDEX "customize_rule_selector_media_rootParagraphId_idx"
                 ON "_central"."customize_rule" (
                        "selector",
                        "media",
                        COALESCE( "rootParagraphId", 0 )
                    );

--------------------------------------------------------------------------------
-- function: customize_insert_update_rootParagraphId_trigger()                --
--------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION "_central"."customize_insert_update_rootParagraphId_trigger"()
                   RETURNS TRIGGER
                       SET search_path FROM CURRENT
                  LANGUAGE plpgsql
                        AS $$
BEGIN

    IF NEW."rootParagraphId" IS NOT NULL THEN

        NEW."rootParagraphId" = (
            SELECT "rootId"
              FROM "_central"."paragraph"
             WHERE "id" = NEW."rootParagraphId"
        );

    END IF;

    RETURN NEW;

END $$;

--------------------------------------------------------------------------------
-- trigger: customize_rule_x_paragraph.1000__insert_update_rootParagraphId    --
--------------------------------------------------------------------------------

CREATE TRIGGER "1000__insert_update_rootParagraphId"
         AFTER INSERT
            OR UPDATE OF "rootParagraphId"
            ON "_central"."customize_rule"
           FOR EACH ROW
       EXECUTE PROCEDURE "_central"."customize_insert_update_rootParagraphId_trigger"();

--------------------------------------------------------------------------------
-- table: customize_extra                                                     --
--------------------------------------------------------------------------------

CREATE TABLE "_central"."customize_extra"
(
    "id"                SERIAL                      NOT NULL,
    "rootParagraphId"   INTEGER                     NOT NULL,
    "extra"             TEXT                        NOT NULL,
    "updated"           TIMESTAMP WITH TIME ZONE    NOT NULL    DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY ( "id" ),
    UNIQUE ( "rootParagraphId" ),
    FOREIGN KEY ( "rootParagraphId" )
     REFERENCES "_central"."paragraph" ( "id" )
      ON UPDATE CASCADE
      ON DELETE CASCADE
);

--------------------------------------------------------------------------------
-- trigger: customize_extra.1000__insert_update_rootParagraphId               --
--------------------------------------------------------------------------------

CREATE TRIGGER "1000__insert_update_rootParagraphId"
         AFTER INSERT
            OR UPDATE OF "rootParagraphId"
            ON "_central"."customize_extra"
           FOR EACH ROW
       EXECUTE PROCEDURE "_central"."customize_insert_update_rootParagraphId_trigger"();
