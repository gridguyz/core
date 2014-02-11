-- delete old customize "settings" rows

DELETE FROM "settings"
      WHERE "key" IN (
                'modules.Grid\Customize.fileTemplate',
                'view_manager.head_defaults.headLink.customize.href'
            );
