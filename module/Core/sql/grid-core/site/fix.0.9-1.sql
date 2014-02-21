--------------------------------------------------------------------------------
-- function: customize_changes_trigger()                                      --
--------------------------------------------------------------------------------

DROP TRIGGER IF EXISTS "customize_rule_changes"
                    ON "customize_rule" CASCADE;

DROP TRIGGER IF EXISTS "customize_property_changes"
                    ON "customize_property" CASCADE;
