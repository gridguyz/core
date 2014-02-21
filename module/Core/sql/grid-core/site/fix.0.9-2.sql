--------------------------------------------------------------------------------
-- function: customize_changes_trigger()                                      --
--------------------------------------------------------------------------------

DROP TRIGGER IF EXISTS "customize_property_changes_insert"
                    ON "customize_property" CASCADE;

DROP TRIGGER IF EXISTS "customize_property_changes_update"
                    ON "customize_property" CASCADE;

DROP TRIGGER IF EXISTS "customize_property_changes_delete"
                    ON "customize_property" CASCADE;

DROP TRIGGER IF EXISTS "customize_property_changes_truncate"
                    ON "customize_property" CASCADE;
