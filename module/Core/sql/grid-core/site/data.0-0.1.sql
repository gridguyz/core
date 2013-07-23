-- default values for table: settings

INSERT INTO "settings" ( "key", "value", "type" )
     VALUES ( 'timezone.id',
              'UTC', 'ini'::"_common"."settings_type" ),
            ( 'locale.available.en',
              '1', 'ini'::"_common"."settings_type" ),
            ( 'locale.default',
              'en', 'ini'::"_common"."settings_type" ),
            ( 'modules.Grid\Customize.fileTemplate',
              '/uploads/:SCHEMA/customize/custom.:HASH.css',
              'ini'::"_common"."settings_type" ),
            ( 'modules.Grid\User.features.registrationEnabled',
              '1', 'ini'::"_common"."settings_type" ),
            ( 'view_manager.head_defaults.headTitle.content',
              '', 'ini'::"_common"."settings_type" ),
            ( 'view_manager.head_defaults.headTitle.separator',
              '/', 'ini'::"_common"."settings_type" ),
            ( 'view_manager.head_defaults.headMeta.keywords.content',
              '', 'ini'::"_common"."settings_type" ),
            ( 'view_manager.head_defaults.headMeta.description.content',
              '', 'ini'::"_common"."settings_type" ),
            ( 'view_manager.head_defaults.headLink.favicon.href',
              '', 'ini'::"_common"."settings_type" ),
            ( 'view_manager.head_defaults.headLink.favicon.type',
              '', 'ini'::"_common"."settings_type" ),
            ( 'view_manager.head_defaults.headLink.logo.href',
              '', 'ini'::"_common"."settings_type" ),
            ( 'view_manager.head_defaults.headLink.logo.type',
              '', 'ini'::"_common"."settings_type" ),
            ( 'view_manager.head_defaults.headLink.customize.href',
              '', 'ini'::"_common"."settings_type" );

-- insert default values for table: mail_template

INSERT INTO "mail_template" ( "name", "locale", "subject", "bodyHtml" )
     VALUES ( 'user.register', 'en', 'Registration successful',
              '<h1>Dear [DISPLAY_NAME]!</h1>'
              '<p>You have successfully registrated at <a href="[SITE_URL]">[SITE_DOMAIN]</a></p>'
              '<p>You will be able to login with your email: <strong>[EMAIL]</strong></p>'
              '<p>Please confirm your email with this link:<br /><a href="[CONFIRM_URL]">[CONFIRM_URL]</a></p>' ),
            ( 'user.admin-create', 'en', 'Account created',
              '<h1>Dear [DISPLAY_NAME]!</h1>'
              '<p>Our admins created an account for you at <a href="[SITE_URL]">[SITE_DOMAIN]</a></p>'
              '<p>You will be able to login with your email: <strong>[EMAIL]</strong></p>'
              '<p>Please confirm your email with this link:<br /><a href="[CONFIRM_URL]">[CONFIRM_URL]</a></p>' ),
            ( 'user.forgotten-password', 'en', 'Forgotten password',
              '<h1>Dear [DISPLAY_NAME]!</h1>'
              '<p>You have requested a new password at our site: <a href="[SITE_URL]">[SITE_DOMAIN]</a></p>'
              '<p>You can change your password here for some time:<br /><a href="[CHANGE_URL]">[CHANGE_URL]</a></p>' ),
            ( 'user.login-with', 'en', 'First login',
              '<h1>Dear [DISPLAY_NAME]!</h1>'
              '<p>You have successfully logined at <a href="[SITE_URL]">[SITE_URL]</a></p>'
              '<p>We created a user for you, in order to easier access our site next time.</p>'
              '<p>You will be able to login with your email: <strong>[EMAIL]</strong></p>' );

-- insert default values for table: mail_template_param

INSERT INTO "mail_template_param" ( "templateName", "paramName", "required" )
     VALUES ( 'user.register', 'DISPLAY_NAME', FALSE ),
            ( 'user.register', 'SITE_URL', FALSE ),
            ( 'user.register', 'SITE_DOMAIN', FALSE ),
            ( 'user.register', 'EMAIL', FALSE ),
            ( 'user.register', 'CONFIRM_URL', TRUE ),

            ( 'user.admin-create', 'DISPLAY_NAME', FALSE ),
            ( 'user.admin-create', 'SITE_URL', FALSE ),
            ( 'user.admin-create', 'SITE_DOMAIN', FALSE ),
            ( 'user.admin-create', 'EMAIL', FALSE ),
            ( 'user.admin-create', 'CONFIRM_URL', TRUE ),

            ( 'user.forgotten-password', 'DISPLAY_NAME', FALSE ),
            ( 'user.forgotten-password', 'SITE_URL', FALSE ),
            ( 'user.forgotten-password', 'SITE_DOMAIN', FALSE ),
            ( 'user.forgotten-password', 'EMAIL', FALSE ),
            ( 'user.forgotten-password', 'CHANGE_URL', TRUE ),

            ( 'user.login-with', 'DISPLAY_NAME', FALSE ),
            ( 'user.login-with', 'SITE_URL', FALSE ),
            ( 'user.login-with', 'SITE_DOMAIN', FALSE ),
            ( 'user.login-with', 'EMAIL', FALSE );

-- insert default values for table: user_group

INSERT INTO "user_group" ( "name", "predefined", "default" )
     VALUES ( 'Developer', TRUE, FALSE ),
            ( 'Site-owner', TRUE, FALSE ),
            ( 'Admin', TRUE, FALSE ),
            ( 'Registered', TRUE, TRUE );

-- insert default values for table: user_right

INSERT INTO "user_right" ( "label", "group", "resource", "privilege", "optional" )
     VALUES ( NULL, 'user.group', 'admin', 'ui', FALSE ),
            ( NULL, 'user.group', 'user.group', 'view', FALSE ),
            ( NULL, 'user.group', 'user.group', 'create', TRUE ),
            ( NULL, 'user.group', 'user.group', 'edit', TRUE ),
            ( NULL, 'user.group', 'user.group', 'delete', TRUE ),
            ( NULL, 'user.group', 'user.group', 'grant', TRUE ),

            ( NULL, 'user.identity', 'admin', 'ui', FALSE ),
            ( 'Admin', 'user.identity', 'user.group.3', 'view', TRUE ),
            ( 'Admin', 'user.identity', 'user.group.3', 'edit', TRUE ),
            ( 'Admin', 'user.identity', 'user.group.3', 'create', TRUE ),
            ( 'Admin', 'user.identity', 'user.group.3', 'delete', TRUE ),
            ( 'Admin', 'user.identity', 'user.group.3', 'grant', TRUE ),
            ( 'Registered', 'user.identity', 'user.group.4', 'view', TRUE ),
            ( 'Registered', 'user.identity', 'user.group.4', 'edit', TRUE ),
            ( 'Registered', 'user.identity', 'user.group.4', 'create', TRUE ),
            ( 'Registered', 'user.identity', 'user.group.4', 'delete', TRUE ),
            ( 'Registered', 'user.identity', 'user.group.4', 'grant', TRUE ),

            ( NULL, 'settings', 'admin', 'ui', FALSE ),
            ( NULL, 'settings', 'settings.site-definition', 'edit', TRUE ),
            ( NULL, 'settings', 'settings.locale', 'edit', TRUE ),
            ( NULL, 'settings', 'settings.mail', 'edit', TRUE ),
            ( NULL, 'settings', 'mail.template', 'view', TRUE ),
            ( NULL, 'settings', 'mail.template', 'edit', TRUE ),
            ( NULL, 'settings', 'subDomain', 'view', TRUE ),
            ( NULL, 'settings', 'subDomain', 'create', TRUE ),
            ( NULL, 'settings', 'subDomain', 'edit', TRUE ),
            ( NULL, 'settings', 'subDomain', 'delete', TRUE ),

            ( NULL, 'paragraph.content', 'admin', 'ui', FALSE ),
            ( NULL, 'paragraph.content', 'paragraph.content', 'view', TRUE ),
            ( NULL, 'paragraph.content', 'paragraph.content', 'create', TRUE ),
            ( NULL, 'paragraph.content', 'paragraph.content', 'edit', TRUE ),
            ( NULL, 'paragraph.content', 'paragraph.content', 'delete', TRUE ),
            ( NULL, 'paragraph.content', 'uri', 'view', TRUE ),
            ( NULL, 'paragraph.content', 'uri', 'create', TRUE ),
            ( NULL, 'paragraph.content', 'uri', 'edit', TRUE ),
            ( NULL, 'paragraph.content', 'uri', 'delete', TRUE ),
            ( NULL, 'paragraph.content', 'paragraph.snippet', 'view', TRUE ),
            ( NULL, 'paragraph.content', 'paragraph.snippet', 'create', TRUE ),
            ( NULL, 'paragraph.content', 'paragraph.snippet', 'edit', TRUE ),
            ( NULL, 'paragraph.content', 'paragraph.snippet', 'delete', TRUE ),
            ( NULL, 'paragraph.content', 'paragraph.widget', 'view', TRUE ),
            ( NULL, 'paragraph.content', 'paragraph.widget', 'edit', TRUE ),
            ( NULL, 'paragraph.content', 'paragraph.widget', 'delete', TRUE ),
            ( NULL, 'paragraph.content', 'menu', 'create', TRUE ),
            ( NULL, 'paragraph.content', 'menu', 'edit', TRUE ),
            ( NULL, 'paragraph.content', 'menu', 'delete', TRUE ),

            ( NULL, 'paragraph.layout', 'admin', 'ui', FALSE ),
            ( NULL, 'paragraph.layout', 'paragraph.layout', 'view', TRUE ),
            ( NULL, 'paragraph.layout', 'paragraph.layout', 'create', TRUE ),
            ( NULL, 'paragraph.layout', 'paragraph.layout', 'edit', TRUE ),
            ( NULL, 'paragraph.layout', 'paragraph.layout', 'delete', TRUE ),
            ( NULL, 'paragraph.layout', 'paragraph.customize', 'edit', TRUE ),
            ( NULL, 'paragraph.layout', 'customize', 'view', TRUE ),
            ( NULL, 'paragraph.layout', 'customize', 'create', TRUE ),
            ( NULL, 'paragraph.layout', 'customize', 'edit', TRUE ),
            ( NULL, 'paragraph.layout', 'customize', 'delete', TRUE );

-- update default values for table: user_group

UPDATE "user_group"
   SET "predefined" = FALSE
 WHERE "name" IN ( 'Admin', 'Registered' );

-- insert basic meta-contents: error.404, error.403,
--  user.datasheet, user.manage, user.passwordChangeRequest

DO LANGUAGE plpgsql $$
DECLARE
    "vLastId"   INTEGER;
BEGIN

    -- insert error.404

    INSERT INTO "paragraph" ( "type", "left", "right", "name" )
         VALUES ( 'metaContent', 1, 4, 'error.404' );

    "vLastId" = currval( 'paragraph_id_seq' );

    INSERT INTO "paragraph_property" ( "paragraphId", "locale", "name", "value" )
         VALUES ( "vLastId", 'en', 'title', '404 error' );

    INSERT INTO "paragraph" ( "type", "rootId", "left", "right", "name" )
         VALUES ( 'html', "vLastId", 2, 3, NULL );

    "vLastId" = currval( 'paragraph_id_seq' );

    INSERT INTO "paragraph_property" ( "paragraphId", "locale", "name", "value" )
         VALUES ( "vLastId", 'en', 'html', '<h1>Take a deep breath</h1>'
                  '<h2>We can''t find that page</h2>'
                  '<p>Looks like the page you requested has been moved or removed.<br />'
                  'But don''t worry, instead take a stroll around on the <a href="/">homepage</a>.</p>' );

    INSERT INTO "customize_rule" ( "selector", "paragraphId", "media" )
         VALUES ( '#paragraph-' || TEXT( "vLastId" ) || '.paragraph.paragraph-html', "vLastId", '' );

    "vLastId" = currval( 'customize_rule_id_seq' );

    INSERT INTO "customize_property" ( "ruleId", "name", "value", "priority" )
         VALUES ( "vLastId", 'min-height', '150px', NULL ),
                ( "vLastId", 'padding-left', '150px', NULL ),
                ( "vLastId", 'background-position', '0% 50%', NULL ),
                ( "vLastId", 'background-position-x', '0%', NULL ),
                ( "vLastId", 'background-position-y', '50%', NULL ),
                ( "vLastId", 'background-repeat', 'no-repeat', NULL ),
                ( "vLastId", 'background-image', 'url("/images/common/error/404.jpg")', NULL );


    -- insert error.403

    INSERT INTO "paragraph" ( "type", "left", "right", "name" )
         VALUES ( 'metaContent', 1, 4, 'error.403' );

    "vLastId" = currval( 'paragraph_id_seq' );

    INSERT INTO "paragraph_property" ( "paragraphId", "locale", "name", "value" )
         VALUES ( "vLastId", 'en', 'title', '403 error' );

    INSERT INTO "paragraph" ( "type", "rootId", "left", "right", "name" )
         VALUES ( 'html', "vLastId", 2, 3, NULL );

    "vLastId" = currval( 'paragraph_id_seq' );

    INSERT INTO "paragraph_property" ( "paragraphId", "locale", "name", "value" )
         VALUES ( "vLastId", 'en', 'html', '<h1>Take a deep breath</h1>'
                  '<h2>You haven''t got enough rights</h2>'
                  '<p>Looks like you don''t have the rights to get the page you requested.<br />'
                  'But don''t worry, instead take a stroll around on the <a href="/">homepage</a>.</p>' );

    INSERT INTO "customize_rule" ( "selector", "paragraphId", "media" )
         VALUES ( '#paragraph-' || TEXT( "vLastId" ) || '.paragraph.paragraph-html', "vLastId", '' );

    "vLastId" = currval( 'customize_rule_id_seq' );

    INSERT INTO "customize_property" ( "ruleId", "name", "value", "priority" )
         VALUES ( "vLastId", 'min-height', '150px', NULL ),
                ( "vLastId", 'padding-left', '150px', NULL ),
                ( "vLastId", 'background-position', '0% 50%', NULL ),
                ( "vLastId", 'background-position-x', '0%', NULL ),
                ( "vLastId", 'background-position-y', '50%', NULL ),
                ( "vLastId", 'background-repeat', 'no-repeat', NULL ),
                ( "vLastId", 'background-image', 'url("/images/common/error/403.jpg")', NULL );


    -- insert user.datasheet

    INSERT INTO "paragraph" ( "type", "left", "right", "name" )
         VALUES ( 'metaContent', 1, 6, 'user.datasheet' );

    "vLastId" = currval( 'paragraph_id_seq' );

    INSERT INTO "paragraph_property" ( "paragraphId", "locale", "name", "value" )
         VALUES ( "vLastId", 'en', 'title', 'User' );

    INSERT INTO "paragraph" ( "type", "rootId", "left", "right", "name" )
         VALUES ( 'title', "vLastId", 2, 3, NULL );

    INSERT INTO "paragraph" ( "type", "rootId", "left", "right", "name" )
         VALUES ( 'contentPlaceholder', "vLastId", 4, 5, NULL );


    -- insert user.register

    INSERT INTO "paragraph" ( "type", "left", "right", "name" )
         VALUES ( 'metaContent', 1, 6, 'user.register' );

    "vLastId" = currval( 'paragraph_id_seq' );

    INSERT INTO "paragraph_property" ( "paragraphId", "locale", "name", "value" )
         VALUES ( "vLastId", 'en', 'title', 'Register' );

    INSERT INTO "paragraph" ( "type", "rootId", "left", "right", "name" )
         VALUES ( 'title', "vLastId", 2, 3, NULL );

    INSERT INTO "paragraph" ( "type", "rootId", "left", "right", "name" )
         VALUES ( 'contentPlaceholder', "vLastId", 4, 5, NULL );


    -- insert user.passwordChangeRequest

    INSERT INTO "paragraph" ( "type", "left", "right", "name" )
         VALUES ( 'metaContent', 1, 6, 'user.passwordChangeRequest' );

    "vLastId" = currval( 'paragraph_id_seq' );

    INSERT INTO "paragraph_property" ( "paragraphId", "locale", "name", "value" )
         VALUES ( "vLastId", 'en', 'title', 'Password-change request' );

    INSERT INTO "paragraph" ( "type", "rootId", "left", "right", "name" )
         VALUES ( 'title', "vLastId", 2, 3, NULL );

    INSERT INTO "paragraph" ( "type", "rootId", "left", "right", "name" )
         VALUES ( 'contentPlaceholder', "vLastId", 4, 5, NULL );

END $$;

-- insert default values for table: menu

INSERT INTO "menu" ( "left", "right", "type" )
     VALUES ( 1, 2, 'container' );

-- insert default values for table: menu_label

INSERT INTO "menu_label" ( "menuId", "locale", "label" )
     VALUES ( currval( 'menu_id_seq' ), 'en', 'Main menu' );
