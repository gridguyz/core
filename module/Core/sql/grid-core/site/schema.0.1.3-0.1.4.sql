--------------------------------------------------------------------------------
-- table: paragraph_property                                                  --
--------------------------------------------------------------------------------

DROP INDEX IF EXISTS "paragraph_property_value_idx";

CREATE INDEX ON "paragraph_property" USING hash ("value");
