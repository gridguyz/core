<?php

return array(
    'router' => array(
        'routes' => array(
            'Grid\Customize\Render\CustomCss' => array(
                'type' => 'Zend\Mvc\Router\Http\Regex',
                'options' => array(
                    'regex'     => '/uploads/(?P<schema>[^/]*)/customize/custom\.(?P<id>[^\./]*)\.(?P<hash>[^\./]*)\.css',
                    'spec'      => '/uploads/%schema%/customize/custom.%id%.%hash%.css',
                    'defaults'  => array(
                        'controller' => 'Grid\Customize\Controller\Render',
                        'action'     => 'custom-css',
                        'id'         => 'global',
                    ),
                ),
            ),
         /* 'Grid\Customize\Admin\Create' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'     => '/app/:locale/admin/customize/create',
                    'constraints'   => array(
                        'locale'    => '\w+',
                    ),
                    'defaults'  => array(
                        'controller' => 'Grid\Customize\Controller\Admin',
                        'action'     => 'edit',
                    ),
                ),
            ),
            'Grid\Customize\Admin\EditExtra' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'         => '/app/:locale/admin/customize/edit-extra',
                    'constraints'   => array(
                        'locale'    => '\w+',
                    ),
                    'defaults'      => array(
                        'controller'    => 'Grid\Customize\Controller\Admin',
                        'action'        => 'edit-extra',
                    ),
                ),
            ), */
            'Grid\Customize\Admin\Edit' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'         => '/app/:locale/admin/customize/edit/:id',
                    'constraints'   => array(
                        'locale'    => '\w+',
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
                    'constraints'   => array(
                        'locale'    => '\w+',
                    ),
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
                        'locale'    => '\w+',
                        'id'        => '[1-9][0-9]*',
                    ),
                    'defaults'      => array(
                        'controller'    => 'Grid\Customize\Controller\Admin',
                        'action'        => 'delete',
                    ),
                ),
            ),
            'Grid\Customize\CssAdmin\Edit' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'         => '/app/:locale/admin/customize-css/edit/:id',
                    'constraints'   => array(
                        'locale'    => '\w+',
                        'id'        => '[1-9][0-9]*',
                    ),
                    'defaults'      => array(
                        'controller'    => 'Grid\Customize\Controller\CssAdmin',
                        'action'        => 'edit',
                    ),
                ),
            ),
            'Grid\Customize\CssAdmin\List' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'     => '/app/:locale/admin/customize-css/list',
                    'constraints'   => array(
                        'locale'    => '\w+',
                    ),
                    'defaults'  => array(
                        'controller' => 'Grid\Customize\Controller\CssAdmin',
                        'action'     => 'list',
                    ),
                ),
            ),
            'Grid\Customize\ImportExport\Import' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'         => '/app/:locale/customize/import/:layoutId[/[:contentId]]',
                    'constraints'   => array(
                        'locale'    => '\w+',
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
                        'locale'    => '\w+',
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
            'Grid\Customize\Controller\CssAdmin'     => 'Grid\Customize\Controller\CssAdminController',
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
                        'cssList'   => array(
                            'order'         => 1,
                            'label'         => 'admin.navTop.customize.cssList',
                            'textDomain'    => 'admin',
                            'route'         => 'Grid\Customize\AdminCss\List',
                            'resource'      => 'customize',
                            'privilege'     => 'create',
                        ),
                        'ruleList'  => array(
                            'order'         => 2,
                            'label'         => 'admin.navTop.customize.ruleList',
                            'textDomain'    => 'admin',
                            'route'         => 'Grid\Customize\Admin\List',
                            'resource'      => 'customize',
                            'privilege'     => 'edit',
                        ),
                     /* 'create'    => array(
                            'order'         => 2,
                            'label'         => 'admin.navTop.customize.ruleCreate',
                            'textDomain'    => 'admin',
                            'route'         => 'Grid\Customize\Admin\Create',
                            'resource'      => 'customize',
                            'privilege'     => 'create',
                        ),
                        'editExtra' => array(
                            'order'         => 3,
                            'label'         => 'admin.navTop.customize.editExtra',
                            'textDomain'    => 'admin',
                            'route'         => 'Grid\Customize\Admin\EditExtra',
                            'resource'      => 'customize',
                            'privilege'     => 'edit',
                        ), */
                    ),
                ),
            ),
        ),
        'Grid\MultisiteCentral'  => array(
            'uploadsFiles'  => array(),
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
                            'label'     => 'customize.form.rules.selector',
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
                            'label'     => 'customize.form.rules.media',
                            'required'  => false,
                        ),
                    ),
                ),
                'properties' => array(
                    'spec'   => array(
                        'type'      => 'Grid\Customize\Form\Element\Properties',
                        'name'      => 'properties',
                        'options'   => array(
                            'label'     => 'customize.form.rules.properties',
                            'required'  => false,
                        ),
                    ),
                ),
                'save'      => array(
                    'spec'  => array(
                        'type'      => 'Zork\Form\Element\Submit',
                        'name'      => 'save',
                        'attributes'    => array(
                            'value'     => 'customize.form.rules.submit',
                        ),
                    ),
                ),
            ),
        ),
        'Grid\Customize\Css' => array(
            'elements' => array(
                'css' => array(
                    'spec'  => array(
                        'type'      => 'Zork\Form\Element\Textarea',
                        'name'      => 'css',
                        'options'   => array(
                            'label'     => 'customize.form.css.css',
                            'required'  => true,
                        ),
                        'attributes'    => array(
                            'data-js-type'  => 'js.form.element.codeEditor',
                        ),
                    ),
                ),
                'save'      => array(
                    'spec'  => array(
                        'type'      => 'Zork\Form\Element\Submit',
                        'name'      => 'save',
                        'attributes'    => array(
                            'value'     => 'customize.form.css.submit',
                        ),
                    ),
                ),
            ),
        ),
     /* 'Grid\Customize\Extra' => array(
            'elements' => array(
                'css' => array(
                    'spec'  => array(
                        'type'      => 'Zork\Form\Element\Textarea',
                        'name'      => 'css',
                        'options'   => array(
                            'label'     => 'customize.form.extra.css',
                            'required'  => true,
                        ),
                        'attributes'    => array(
                            'data-js-type'  => 'js.form.element.codeEditor',
                        ),
                    ),
                ),
                'save'      => array(
                    'spec'  => array(
                        'type'      => 'Zork\Form\Element\Submit',
                        'name'      => 'save',
                        'attributes'    => array(
                            'value'     => 'customize.form.extra.submit',
                        ),
                    ),
                ),
            ),
        ), */
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
    ),
);
