--------------------------------------------------------------------------------
-- table: paragraph_property                                                  --
--------------------------------------------------------------------------------

DROP INDEX IF EXISTS "_central"."paragraph_property_value_idx";

CREATE INDEX ON "_central"."paragraph_property" USING hash ("value");
