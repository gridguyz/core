<?php

return array(
    'router' => array(
        'routes' => array(
            'Grid\Menu\Admin\Editor' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/app/:locale/admin/menu',
                    'defaults' => array(
                        'controller' => 'Grid\Menu\Controller\Admin',
                        'action'     => 'editor',
                    ),
                ),
            ),
            'Grid\Menu\Admin\Create' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/app/:locale/admin/menu/create/:type[/:parentId]',
                    'constraints'   => array(
                        'parentId'  => '[1-9][0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Grid\Menu\Controller\Admin',
                        'action'     => 'create',
                    ),
                ),
            ),
            'Grid\Menu\Admin\Edit' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'         => '/app/:locale/admin/menu/edit/:menuId',
                    'constraints'   => array(
                        'menuId'    => '[1-9][0-9]*',
                    ),
                    'defaults'      => array(
                        'controller'    => 'Grid\Menu\Controller\Admin',
                        'action'        => 'edit',
                    ),
                ),
            ),
            'Grid\Menu\Navigation\Render' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'         => '/app/:locale/menu/render/:id[/[:class]]',
                    'constraints'   => array(
                        'id'        => '[1-9][0-9]*',
                    ),
                    'defaults'      => array(
                        'controller'    => 'Grid\Menu\Controller\Navigation',
                        'action'        => 'render',
                    ),
                ),
            ),
        ),
    ),
    'acl' => array(
        'resources' => array(
            'menu'  => null,
        ),
    ),
    'factory' => array(
        'Grid\Menu\Model\Menu\StructureFactory' => array(
            'dependency' => 'Grid\Menu\Model\Menu\StructureInterface',
            'adapter'    => array(
                ''          => 'Grid\Menu\Model\Menu\Structure\DefaultFallback',
                'container' => 'Grid\Menu\Model\Menu\Structure\Container',
                'content'   => 'Grid\Menu\Model\Menu\Structure\Content',
                'uri'       => 'Grid\Menu\Model\Menu\Structure\Uri',
            ),
        ),
        'Grid\Paragraph\Model\Paragraph\StructureFactory' => array(
            'adapter' => array(
                'menu'          => 'Grid\Menu\Model\Paragraph\Structure\Menu',
                'breadcrumb'    => 'Grid\Menu\Model\Paragraph\Structure\Breadcrumb',
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Grid\Menu\Controller\Admin'         => 'Grid\Menu\Controller\AdminController',
            'Grid\Menu\Controller\Navigation'    => 'Grid\Menu\Controller\NavigationController',
        ),
    ),
    'modules' => array(
        'Grid\Core' => array(
            'dashboardIcons' => array(
                'menu'  => array(
                    'order'         => 3,
                    'label'         => 'admin.navTop.menu',
                    'textDomain'    => 'admin',
                    'route'         => 'Grid\Menu\Admin\Editor',
                    'resource'      => 'menu',
                    'privilege'     => 'edit',
                ),
            ),
            'navigation'    => array(
                'content'   => array(
                    'pages' => array(
                        'menu'  => array(
                            'order'         => 1,
                            'label'         => 'admin.navTop.menu',
                            'textDomain'    => 'admin',
                            'route'         => 'Grid\Menu\Admin\Editor',
                            'resource'      => 'menu',
                            'privilege'     => 'edit',
                        ),
                    ),
                ),
            ),
        ),
        'Grid\Paragraph' => array(
            'customizeSelectors' => array(
                'menuUl'                => '#paragraph-%id%.paragraph.paragraph-%type% ul',
                'menuUlLi'              => '#paragraph-%id%.paragraph.paragraph-%type% ul li a, #paragraph-%id%.paragraph.paragraph-%type% ul li a:visited',
                'menuUlHover'           => '#paragraph-%id%.paragraph.paragraph-%type% ul:hover',
                'menuUlLiHover'         => '#paragraph-%id%.paragraph.paragraph-%type% ul li a:active, #paragraph-%id%.paragraph.paragraph-%type% ul li a:hover, #paragraph-%id%.paragraph.paragraph-%type% ul li a:focus',
                'menuUlLiActive'        => '#paragraph-%id%.paragraph.paragraph-%type% ul li.active a',
                'breadcrumbSeparator'   => '#paragraph-%id%.paragraph.paragraph-%type% .separator',
            ),
            'customizeMapForms' => array(
                'menu' => array(
                    'element'           => 'general',
                    'menuUl'            => 'general',
                    'menuUlLi'          => 'general',
                    'menuUlHover'       => 'basic',
                    'menuUlLiHover'     => 'basic',
                    'menuUlLiActive'    => 'basic',
                ),
                'breadcrumb' => array(
                    'element'               => 'general',
                    'links'                 => 'basic',
                    'breadcrumbSeparator'   => 'basic',
                ),
            ),
        ),
    ),
    'form' => array(
        'Grid\Menu\Meta\Base' => array(
            'elements' => array(
                'label'     => array(
                    'spec'  => array(
                        'type'      => 'Zork\Form\Element\Text',
                        'name'      => 'label',
                        'options'   => array(
                            'label'     => 'menu.form.label',
                            'required'  => true,
                        ),
                    ),
                ),
                'target'    => array(
                    'spec'  => array(
                        'type'      => 'Zork\Form\Element\Select',
                        'name'      => 'target',
                        'options'   => array(
                            'label'     => 'menu.form.target',
                            'required'  => false,
                            'options'   => array(
                                ''          => 'default.link.target.default',
                                '_self'     => 'default.link.target.self',
                                '_blank'    => 'default.link.target.blank',
                                '_parent'   => 'default.link.target.parent',
                                '_top'      => 'default.link.target.top',
                            ),
                            'text_domain' => 'default',
                        ),
                    ),
                ),
            ),
        ),
        'Grid\Menu\Meta\Type' => array(
            'fieldsets' => array(
                'content' => array(
                    'spec' => array(
                        'name'      => 'content',
                        'options'   => array(
                            'label' => 'menu.type.content',
                        ),
                        'elements'  => array(
                            'contentId' => array(
                                'spec'  => array(
                                    'type'      => 'Zork\Form\Element\SelectModel',
                                    'name'      => 'contentId',
                                    'options'   => array(
                                        'label'     => 'menu.form.content.content',
                                        'required'  => true,
                                        'model'     => 'Grid\Paragraph\Model\Paragraph\Model',
                                        'method'    => 'findOptions',
                                        'arguments' => array(
                                            'content'
                                        ),
                                    ),
                                    'attributes'    => array(
                                        'data-js-type'  => 'js.paragraph.contentSelect',
                                        'placeholder'   => 'default.autoCompletePlaceholder',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
                'uri' => array(
                    'spec' => array(
                        'name'      => 'uri',
                        'options'   => array(
                            'label' => 'menu.type.uri',
                        ),
                        'elements'  => array(
                            'uri'   => array(
                                'spec'  => array(
                                    'type'      => 'Zork\Form\Element\Text',
                                    'name'      => 'uri',
                                    'options'   => array(
                                        'label'     => 'menu.form.uri.uri',
                                        'required'  => true,
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ),
        'Grid\Paragraph\CreateWizard\Start' => array(
            'elements'  => array(
                'type'  => array(
                    'spec'  => array(
                        'options'   => array(
                            'options'   => array(
                                'basic' => array(
                                    'options'   => array(
                                        'menu'          => 'paragraph.type.menu',
                                        'breadcrumb'    => 'paragraph.type.breadcrumb',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ),
        'Grid\Paragraph\Meta\Edit' => array(
            'fieldsets' => array(
                'menu' => array(
                    'spec' => array(
                        'name'      => 'menu',
                        'options'   => array(
                            'label'     => 'paragraph.type.menu',
                            'required'  => true,
                        ),
                        'elements'  => array(
                            'name'  => array(
                                'spec'  => array(
                                    'type'      => 'Zork\Form\Element\Text',
                                    'name'      => 'name',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.abstract.name',
                                        'required'  => false,
                                    ),
                                ),
                            ),
                            'menuId'    => array(
                                'spec'  => array(
                                    'type'      => 'Zork\Form\Element\SelectModel',
                                    'name'      => 'menuId',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.menu.menu',
                                        'required'  => true,
                                        'model'     => 'Grid\Menu\Model\Menu\Model',
                                        'method'    => 'findOptions',
                                    ),
                                ),
                            ),
                            'horizontal' => array(
                                'spec'  => array(
                                    'type'      => 'Zork\Form\Element\Checkbox',
                                    'name'      => 'horizontal',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.menu.horizontal',
                                        'required'  => false,
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
                'breadcrumb' => array(
                    'spec' => array(
                        'name'      => 'breadcrumb',
                        'options'   => array(
                            'label'     => 'paragraph.type.breadcrumb',
                            'required'  => false,
                        ),
                        'elements'  => array(
                            'name'  => array(
                                'spec'  => array(
                                    'type'      => 'Zork\Form\Element\Text',
                                    'name'      => 'name',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.abstract.name',
                                        'required'  => false,
                                    ),
                                ),
                            ),
                            'separator' => array(
                                'spec'  => array(
                                    'type'      => 'Zork\Form\Element\Select',
                                    'name'      => 'separator',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.breadcrumb.separator',
                                        'required'      => false,
                                        'options'       => include 'config/separators.php',
                                        'translatable'  => false,
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'translator' => array(
        'translation_file_patterns' => array(
            'menu' => array(
                'type'          => 'phpArray',
                'base_dir'      => __DIR__ . '/../languages/menu',
                'pattern'       => '%s.php',
                'text_domain'   => 'menu',
            ),
        ),
    ),
    'view_manager' => array(
        'template_map' => array(
            'grid/menu/admin/create'             => __DIR__ . '/../view/grid/menu/admin/create.phtml',
            'grid/menu/admin/edit'               => __DIR__ . '/../view/grid/menu/admin/edit.phtml',
            'grid/menu/admin/editor'             => __DIR__ . '/../view/grid/menu/admin/editor.phtml',
            'grid/menu/navigation/render'        => __DIR__ . '/../view/grid/menu/navigation/render.phtml',
            'grid/paragraph/render/breadcrumb'   => __DIR__ . '/../view/grid/paragraph/render/breadcrumb.phtml',
            'grid/paragraph/render/menu'         => __DIR__ . '/../view/grid/paragraph/render/menu.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);
