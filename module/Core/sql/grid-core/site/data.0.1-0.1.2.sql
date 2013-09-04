-- default values for table: table_plugin_trigger

INSERT INTO "table_plugin_trigger" ( "tables", "create", "drop" )
     VALUES ( ARRAY['search', 'paragraph'], 'paragraph_update_all_search_content', NULL );

-- default values for plugins of table: paragraph

SELECT "table_plugin_run_create_triggers"( 'paragraph' );
