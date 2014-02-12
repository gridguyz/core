-- delete old customize "settings" rows

DELETE FROM "settings"
      WHERE "key" IN (
                'modules.Grid\Customize.fileTemplate',
                'view_manager.head_defaults.headLink.customize.href'
            );

-- insert default values to "customize_extra"

INSERT INTO "customize_extra" ( "rootParagraphId", "extra" )
     SELECT DISTINCT "rootParagraphId", '' AS "extra"
       FROM "customize_rule"
     EXCEPT ALL
     SELECT "rootParagraphId", "extra"
       FROM "customize_extra";
