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
          FROM "customize_rule_x_paragraph"
         WHERE "customize_rule_x_paragraph"."ruleId"        = "customize_rule"."id"
           AND "customize_rule_x_paragraph"."paragraphId"   = OLD."id"
           AND "customize_rule"."rootParagraphId"           = OLD."rootId";

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
