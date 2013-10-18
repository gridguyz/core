-- insert basic meta-contents: user.registerSuccess

DO LANGUAGE plpgsql $$
DECLARE
    "vLastId"   INTEGER;
BEGIN

    -- insert user.registerSuccess

    INSERT INTO "paragraph" ( "type", "left", "right", "name" )
         VALUES ( 'metaContent', 1, 6, 'user.registerSuccess' );

    "vLastId" = currval( 'paragraph_id_seq' );

    INSERT INTO "paragraph_property" ( "paragraphId", "locale", "name", "value" )
         VALUES ( "vLastId", 'en', 'title', 'Registration successful' );

    INSERT INTO "paragraph" ( "type", "rootId", "left", "right", "name" )
         VALUES ( 'title', "vLastId", 2, 3, NULL );

    INSERT INTO "paragraph" ( "type", "rootId", "left", "right", "name" )
         VALUES ( 'contentPlaceholder', "vLastId", 4, 5, NULL );

END $$;
