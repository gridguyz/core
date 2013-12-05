-- default values for table: paragraph

INSERT INTO "_central"."paragraph" ( "id", "type", "rootId", "left", "right", "name" )
     VALUES ( 1534, 'layout',             1534, 1,  16, 'Blank Canvas' ),
            ( 1535, 'contentPlaceholder', 1534, 10, 11, NULL ),
            ( 1536, 'html',               1534, 2,  3,  'Slogan' ),
            ( 1537, 'columns',            1534, 4,  13, NULL ),
            ( 1538, 'column',             1534, 5,  8,  NULL ),
            ( 1540, 'column',             1534, 9,  12, NULL ),
            ( 1542, 'menu',               1534, 6,  7,  NULL ),
            ( 1543, 'html',               1534, 14, 15, 'Footer' );

-- default values for table: paragraph_property

INSERT INTO "_central"."paragraph_property" ( "paragraphId", "locale", "name", "value" )
     VALUES ( 1534, '*',  'lastModified', '2013-11-13 13:44:13.575869+00' ),
            ( 1536, 'en', 'html',         '<h1>Slogan</h1>' ),
            ( 1542, '*',  'menuId',       '1' ),
            ( 1542, '*',  'horizontal',   '' ),
            ( 1543, 'en', 'html',         '<p>Powered by GridGuyz</p>' );

-- default values for table: customize_rule

INSERT INTO "_central"."customize_rule" ( "id", "selector", "paragraphId", "media" )
     VALUES ( 943, '#paragraph-1538-container.paragraph-container.paragraph-column-container', 1538, '' ),
            ( 944, '#paragraph-1540-container.paragraph-container.paragraph-column-container', 1540, '' );

-- default values for table: customize_property

INSERT INTO "_central"."customize_property" ( "id", "ruleId", "name", "value", "priority" )
     VALUES ( 2184, 943, 'width', '25%', 'important' ),
            ( 2185, 944, 'width', '75%', 'important' );
