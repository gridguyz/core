<?php

return array(
    'router' => array(
        'routes' => array(
            'Grid\Customize\Render\CustomCss' => array(
                'type' => 'Zend\Mvc\Router\Http\Regex',
                'options' => array(
                    'regex'     => '/uploads/(?P<schema>[^/]*)/customize/custom\.(?P<hash>[^\./]*)\.css',
                    'spec'      => '/uploads/%schema%/customize/custom.%hash%.css',
                    'defaults'  => array(
                        'controller' => 'Grid\Customize\Controller\Render',
                        'action'     => 'custom-css',
                    ),
                ),
            ),
         /* 'Grid\Customize\Render\FileToSql' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'     => '/app/:locale/admin/customize/file-to-sql',
                    'defaults'  => array(
                        'controller' => 'Grid\Customize\Controller\Render',
                        'action'     => 'file-to-sql',
                    ),
                ),
            ), */
         /* 'Grid\Customize\Render\DbToSql' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'     => '/app/:locale/admin/customize/db-to-sql/:id',
                    'defaults'  => array(
                        'controller' => 'Grid\Customize\Controller\Render',
                        'action'     => 'db-to-sql',
                    ),
                ),
            ), */
            'Grid\Customize\Admin\Create' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'     => '/app/:locale/admin/customize/create',
                    'defaults'  => array(
                        'controller' => 'Grid\Customize\Controller\Admin',
                        'action'     => 'edit',
                    ),
                ),
            ),
            'Grid\Customize\Admin\Edit' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'         => '/app/:locale/admin/customize/edit/:id',
                    'constraints'   => array(
                        'id'        => '[1-9][0-9]*',
                    ),
                    'defaults'      => array(
                        'controller'    => 'Grid\Customize\Controller\Admin',
                        'action'        => 'edit',
                    ),
                ),
            ),
            'Grid\Customize\Admin\List' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'     => '/app/:locale/admin/customize/list',
                    'defaults'  => array(
                        'controller' => 'Grid\Customize\Controller\Admin',
                        'action'     => 'list',
                    ),
                ),
            ),
            'Grid\Customize\Admin\Delete' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'         => '/app/:locale/admin/customize/delete/:id',
                    'constraints'   => array(
                        'id'        => '[1-9][0-9]*',
                    ),
                    'defaults'      => array(
                        'controller'    => 'Grid\Customize\Controller\Admin',
                        'action'        => 'delete',
                    ),
                ),
            ),
            'Grid\Customize\ImportExport\Import' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'         => '/app/:locale/customize/import/:layoutId[/[:contentId]]',
                    'constraints'   => array(
                        'layoutId'  => '[1-9][0-9]*',
                        'contentId' => '[1-9][0-9]*',
                    ),
                    'defaults'      => array(
                        'controller'    => 'Grid\Customize\Controller\ImportExport',
                        'action'        => 'import',
                    ),
                ),
            ),
            'Grid\Customize\ImportExport\Export' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'         => '/app/:locale/customize/export/:layoutId[/[:contentId]]',
                    'constraints'   => array(
                        'layoutId'  => '[1-9][0-9]*',
                        'contentId' => '[1-9][0-9]*',
                    ),
                    'defaults'      => array(
                        'controller'    => 'Grid\Customize\Controller\ImportExport',
                        'action'        => 'export',
                    ),
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Grid\Customize\Controller\Admin'        => 'Grid\Customize\Controller\AdminController',
            'Grid\Customize\Controller\Render'       => 'Grid\Customize\Controller\RenderController',
            'Grid\Customize\Controller\ImportExport' => 'Grid\Customize\Controller\ImportExportController',
        ),
    ),
    'modules' => array(
        'Grid\Core' => array(
            'navigation'    => array(
                'customize' => array(
                    'order'         => 7,
                    'label'         => 'admin.navTop.customize',
                    'textDomain'    => 'admin',
                    'uri'           => '#',
                    'parentOnly'    => true,
                    'pages'         => array(
                        'list'      => array(
                            'order'         => 1,
                            'label'         => 'admin.navTop.customize.ruleList',
                            'textDomain'    => 'admin',
                            'route'         => 'Grid\Customize\Admin\List',
                            'resource'      => 'customize',
                            'privilege'     => 'edit',
                        ),
                        'create'    => array(
                            'order'         => 2,
                            'label'         => 'admin.navTop.customize.ruleCreate',
                            'textDomain'    => 'admin',
                            'route'         => 'Grid\Customize\Admin\Create',
                            'resource'      => 'customize',
                            'privilege'     => 'create',
                        ),
                    ),
                ),
            ),
        ),
        'Grid\MultisiteCentral'  => array(
            'uploadsFiles'  => array(
                'customize/extra.css',
            ),
            'uploadsDirs'   => array(
                'customize',
            ),
        ),
    ),
    'form' => array(
        'Grid\Customize\Rule' => array(
            'elements' => array(
                'id'        => array(
                    'spec'  => array(
                        'type'      => 'Zork\Form\Element\Hidden',
                        'name'      => 'id',
                    ),
                ),
                'selector'  => array(
                    'spec'  => array(
                        'type'      => 'Zork\Form\Element\Textarea',
                        'name'      => 'selector',
                        'options'   => array(
                            'label'     => 'customize.form.selector',
                            'required'  => true,
                            'rpc_validators'    => array(
                                'Grid\Customize\Model\Rpc::isSelectorAvailable',
                            ),
                        ),
                    ),
                ),
                'media'     => array(
                    'spec'  => array(
                        'type'      => 'Zork\Form\Element\Textarea',
                        'name'      => 'media',
                        'options'   => array(
                            'label'     => 'customize.form.media',
                            'required'  => false,
                        ),
                    ),
                ),
                'properties' => array(
                    'spec'   => array(
                        'type'      => 'Grid\Customize\Form\Element\Properties',
                        'name'      => 'properties',
                        'options'   => array(
                            'label'     => 'customize.form.properties',
                            'required'  => false,
                        ),
                    ),
                ),
                'save'      => array(
                    'spec'  => array(
                        'type'      => 'Zork\Form\Element\Submit',
                        'name'      => 'save',
                        'attributes'    => array(
                            'value'     => 'customize.form.submit',
                        ),
                    ),
                ),
            ),
        ),
        'Grid\Customize\Import' => array(
            'elements' => array(
                'file' => array(
                    'spec'   => array(
                        'type'      => 'Zork\Form\Element\PathSelect',
                        'name'      => 'file',
                        'options'   => array(
                            'label'     => 'customize.form.import.file',
                            'required'  => true,
                        ),
                    ),
                ),
                'submit'    => array(
                    'spec'  => array(
                        'type'      => 'Zork\Form\Element\Submit',
                        'name'      => 'submit',
                        'attributes'    => array(
                            'value'     => 'customize.form.import.submit',
                        ),
                    ),
                ),
            ),
        ),
    ),
    'translator' => array(
        'translation_file_patterns' => array(
            'customize' => array(
                'type'          => 'phpArray',
                'base_dir'      => __DIR__ . '/../languages/customize',
                'pattern'       => '%s.php',
                'text_domain'   => 'customize',
            ),
        ),
    ),
    'view_manager' => array(
        'template_map' => array(
            'grid/customize/admin/list' => __DIR__ . '/../view/grid/customize/admin/list.phtml',
            'grid/customize/admin/edit' => __DIR__ . '/../view/grid/customize/admin/edit.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'head_defaults'     => array(
            'headLink'      => array(
                'customize' => array(
                    'rel'   => 'stylesheet',
                    'type'  => 'text/css',
                    'id'    => 'customizeStyleSheet',
                    'href'  => '#customizeStyleSheet',
                ),
            ),
        ),
    ),
);
