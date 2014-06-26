<?php

return array(
    'router' => array(
        'routes' => array(
            'Grid\Paragraph\Render\Paragraph' => array(
                'type'      => 'Zend\Mvc\Router\Http\Segment',
                'options'   => array(
                    'route'         => '/app/:locale/paragraph/render/:paragraphId',
                    'constraints'   => array(
                        'locale'        => '\w+',
                        'paragraphId'   => '[1-9][0-9]*',
                    ),
                    'defaults'      => array(
                        'controller'    => 'Grid\Paragraph\Controller\Render',
                        'action'        => 'paragraph',
                    ),
                ),
            ),
            'Grid\Paragraph\Dashboard\Edit' => array(
                'type'      => 'Zend\Mvc\Router\Http\Segment',
                'options'   => array(
                    'route'         => '/app/:locale/paragraph/edit/:paragraphId',
                    'constraints'   => array(
                        'locale'        => '\w+',
                        'paragraphId'   => '[1-9][0-9]*',
                    ),
                    'defaults'      => array(
                        'controller'    => 'Grid\Paragraph\Controller\Dashboard',
                        'action'        => 'edit',
                    ),
                ),
            ),
            'Grid\Paragraph\Content\Create' => array(
                'type'      => 'Zend\Mvc\Router\Http\Segment',
                'options'   => array(
                    'route'     => '/app/:locale/admin/content/create',
                    'constraints'   => array(
                        'locale'    => '\w+',
                    ),
                    'defaults'  => array(
                        'controller'    => 'Grid\Paragraph\Controller\Content',
                        'action'        => 'edit',
                    ),
                ),
            ),
            'Grid\Paragraph\Content\Edit' => array(
                'type'      => 'Zend\Mvc\Router\Http\Segment',
                'options'   => array(
                    'route'         => '/app/:locale/admin/content/edit/:id',
                    'constraints'   => array(
                        'locale'    => '\w+',
                        'id'        => '[1-9][0-9]*',
                    ),
                    'defaults'      => array(
                        'controller'    => 'Grid\Paragraph\Controller\Content',
                        'action'        => 'edit',
                    ),
                ),
            ),
            'Grid\Paragraph\Content\List' => array(
                'type'      => 'Zend\Mvc\Router\Http\Segment',
                'options'   => array(
                    'route'     => '/app/:locale/admin/content/list',
                    'constraints'   => array(
                        'locale'    => '\w+',
                    ),
                    'defaults'  => array(
                        'controller'    => 'Grid\Paragraph\Controller\Content',
                        'action'        => 'list',
                    ),
                ),
            ),
            'Grid\Paragraph\Content\Clone' => array(
                'type'      => 'Zend\Mvc\Router\Http\Segment',
                'options'   => array(
                    'route'         => '/app/:locale/admin/content/clone/:id',
                    'constraints'   => array(
                        'locale'    => '\w+',
                        'id'        => '[1-9][0-9]*',
                    ),
                    'defaults'      => array(
                        'controller'    => 'Grid\Paragraph\Controller\Content',
                        'action'        => 'clone',
                    ),
                ),
            ),
            'Grid\Paragraph\Content\Delete' => array(
                'type'      => 'Zend\Mvc\Router\Http\Segment',
                'options'   => array(
                    'route'         => '/app/:locale/admin/content/delete/:id',
                    'constraints'   => array(
                        'locale'    => '\w+',
                        'id'        => '[1-9][0-9]*',
                    ),
                    'defaults'      => array(
                        'controller'    => 'Grid\Paragraph\Controller\Content',
                        'action'        => 'delete',
                    ),
                ),
            ),
            'Grid\Paragraph\MetaContent\Edit' => array(
                'type'      => 'Zend\Mvc\Router\Http\Segment',
                'options'   => array(
                    'route'         => '/app/:locale/admin/meta-content/edit/:id',
                    'constraints'   => array(
                        'locale'    => '\w+',
                        'id'        => '[1-9][0-9]*',
                    ),
                    'defaults'      => array(
                        'controller'    => 'Grid\Paragraph\Controller\MetaContent',
                        'action'        => 'edit',
                    ),
                ),
            ),
            'Grid\Paragraph\MetaContent\List' => array(
                'type'      => 'Zend\Mvc\Router\Http\Segment',
                'options'   => array(
                    'route'     => '/app/:locale/admin/meta-content/list',
                    'constraints'   => array(
                        'locale'    => '\w+',
                    ),
                    'defaults'  => array(
                        'controller'    => 'Grid\Paragraph\Controller\MetaContent',
                        'action'        => 'list',
                    ),
                ),
            ),
            'Grid\Paragraph\Layout\Create' => array(
                'type'      => 'Zend\Mvc\Router\Http\Segment',
                'options'   => array(
                    'route'     => '/app/:locale/admin/layout/create',
                    'constraints'   => array(
                        'locale'    => '\w+',
                    ),
                    'defaults'  => array(
                        'controller'    => 'Grid\Paragraph\Controller\Layout',
                        'action'        => 'edit',
                    ),
                ),
            ),
            'Grid\Paragraph\Layout\Edit' => array(
                'type'      => 'Zend\Mvc\Router\Http\Segment',
                'options'   => array(
                    'route'         => '/app/:locale/admin/layout/edit/:id',
                    'constraints'   => array(
                        'locale'    => '\w+',
                        'id'        => '[1-9][0-9]*',
                    ),
                    'defaults'      => array(
                        'controller'    => 'Grid\Paragraph\Controller\Layout',
                        'action'        => 'edit',
                    ),
                ),
            ),
            'Grid\Paragraph\Layout\List' => array(
                'type'      => 'Zend\Mvc\Router\Http\Segment',
                'options'   => array(
                    'route'     => '/app/:locale/admin/layout/list',
                    'constraints'   => array(
                        'locale'    => '\w+',
                    ),
                    'defaults'  => array(
                        'controller'    => 'Grid\Paragraph\Controller\Layout',
                        'action'        => 'list',
                    ),
                ),
            ),
            'Grid\Paragraph\Layout\Clone' => array(
                'type'      => 'Zend\Mvc\Router\Http\Segment',
                'options'   => array(
                    'route'     => '/app/:locale/admin/layout/clone/:id',
                    'constraints'   => array(
                        'locale'    => '\w+',
                        'id'        => '[1-9][0-9]*',
                    ),
                    'defaults'      => array(
                        'controller'    => 'Grid\Paragraph\Controller\Layout',
                        'action'        => 'clone',
                    ),
                ),
            ),
            'Grid\Paragraph\Layout\Delete' => array(
                'type'      => 'Zend\Mvc\Router\Http\Segment',
                'options'   => array(
                    'route'         => '/app/:locale/admin/layout/delete/:id',
                    'constraints'   => array(
                        'locale'    => '\w+',
                        'id'        => '[1-9][0-9]*',
                    ),
                    'defaults'      => array(
                        'controller'    => 'Grid\Paragraph\Controller\Layout',
                        'action'        => 'delete',
                    ),
                ),
            ),
            'Grid\Paragraph\CreateWizard\Step' => array(
                'type'      => 'Zend\Mvc\Router\Http\Segment',
                'options'   => array(
                    'route'     => '/app/:locale/paragraph/create[/[:step]]',
                    'constraints'   => array(
                        'locale'    => '\w+',
                        'step'      => '[\w\.-]+',
                    ),
                    'defaults'  => array(
                        'controller'    => 'Grid\Paragraph\Controller\CreateWizard',
                        'action'        => 'step',
                        'step'          => 'start',
                    ),
                ),
            ),
            'Grid\Paragraph\ChangeLayout\Local' => array(
                'type'      => 'Zend\Mvc\Router\Http\Segment',
                'options'   => array(
                    'route'         => '/app/:locale/paragraph/change-layout[/:paragraphId]',
                    'constraints'   => array(
                        'locale'        => '\w+',
                        'paragraphId'   => '[1-9][0-9]*',
                    ),
                    'defaults'      => array(
                        'controller'    => 'Grid\Paragraph\Controller\ChangeLayout',
                        'action'        => 'local',
                    ),
                ),
            ),
            'Grid\Paragraph\ChangeLayout\Import' => array(
                'type'      => 'Zend\Mvc\Router\Http\Segment',
                'options'   => array(
                    'route'         => '/app/:locale/paragraph/import-layout[/:paragraphId]',
                    'constraints'   => array(
                        'locale'        => '\w+',
                        'paragraphId'   => '[1-9][0-9]*',
                    ),
                    'defaults'      => array(
                        'controller'    => 'Grid\Paragraph\Controller\ChangeLayout',
                        'action'        => 'import',
                    ),
                ),
            ),
            'Grid\Paragraph\ImportContent\Import' => array(
                'type'      => 'Zend\Mvc\Router\Http\Segment',
                'options'   => array(
                    'route'     => '/app/:locale/paragraph/import-content',
                    'constraints'   => array(
                        'locale'    => '\w+',
                    ),
                    'defaults'  => array(
                        'controller'    => 'Grid\Paragraph\Controller\ImportContent',
                        'action'        => 'import',
                    ),
                ),
            ),
            'Grid\Paragraph\Snippet\Create' => array(
                'type'      => 'Zend\Mvc\Router\Http\Segment',
                'options'   => array(
                    'route'     => '/app/:locale/admin/snippet/create',
                    'constraints'   => array(
                        'locale'    => '\w+',
                    ),
                    'defaults'  => array(
                        'controller'    => 'Grid\Paragraph\Controller\Snippet',
                        'action'        => 'create',
                    ),
                ),
            ),
            'Grid\Paragraph\Snippet\Upload' => array(
                'type'      => 'Zend\Mvc\Router\Http\Segment',
                'options'   => array(
                    'route'     => '/app/:locale/admin/snippet/upload',
                    'constraints'   => array(
                        'locale'    => '\w+',
                    ),
                    'defaults'  => array(
                        'controller'    => 'Grid\Paragraph\Controller\Snippet',
                        'action'        => 'upload',
                    ),
                ),
            ),
            'Grid\Paragraph\Snippet\List' => array(
                'type'      => 'Zend\Mvc\Router\Http\Segment',
                'options'   => array(
                    'route'     => '/app/:locale/admin/snippet/list',
                    'constraints'   => array(
                        'locale'    => '\w+',
                    ),
                    'defaults'  => array(
                        'controller'    => 'Grid\Paragraph\Controller\Snippet',
                        'action'        => 'list',
                    ),
                ),
            ),
            'Grid\Paragraph\Snippet\Edit' => array(
                'type'      => 'Zend\Mvc\Router\Http\Segment',
                'options'   => array(
                    'route'     => '/app/:locale/admin/snippet/edit/:name',
                    'constraints'   => array(
                        'locale'    => '\w+',
                        'name'      => '[\w\.\-]+',
                    ),
                    'defaults'  => array(
                        'controller'    => 'Grid\Paragraph\Controller\Snippet',
                        'action'        => 'edit',
                    ),
                ),
            ),
            'Grid\Paragraph\Snippet\Delete' => array(
                'type'      => 'Zend\Mvc\Router\Http\Segment',
                'options'   => array(
                    'route'     => '/app/:locale/admin/snippet/delete/:name',
                    'constraints'   => array(
                        'locale'    => '\w+',
                        'name'      => '[\w\.\-]+',
                    ),
                    'defaults'  => array(
                        'controller'    => 'Grid\Paragraph\Controller\Snippet',
                        'action'        => 'delete',
                    ),
                ),
            ),
            'Grid\Paragraph\Widget\List' => array(
                'type'      => 'Zend\Mvc\Router\Http\Segment',
                'options'   => array(
                    'route'     => '/app/:locale/admin/widget/list',
                    'constraints'   => array(
                        'locale'    => '\w+',
                    ),
                    'defaults'  => array(
                        'controller'    => 'Grid\Paragraph\Controller\Widget',
                        'action'        => 'list',
                    ),
                ),
            ),
            'Grid\Paragraph\Widget\Edit' => array(
                'type'      => 'Zend\Mvc\Router\Http\Segment',
                'options'   => array(
                    'route'         => '/app/:locale/admin/widget/edit/:id',
                    'constraints'   => array(
                        'locale'        => '\w+',
                        'paragraphId'   => '[1-9][0-9]*',
                    ),
                    'defaults'      => array(
                        'controller'    => 'Grid\Paragraph\Controller\Widget',
                        'action'        => 'edit',
                    ),
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Grid\Paragraph\Controller\Render'           => 'Grid\Paragraph\Controller\RenderController',
            'Grid\Paragraph\Controller\Widget'           => 'Grid\Paragraph\Controller\WidgetController',
            'Grid\Paragraph\Controller\Layout'           => 'Grid\Paragraph\Controller\LayoutController',
            'Grid\Paragraph\Controller\Content'          => 'Grid\Paragraph\Controller\ContentController',
            'Grid\Paragraph\Controller\Snippet'          => 'Grid\Paragraph\Controller\SnippetController',
            'Grid\Paragraph\Controller\Dashboard'        => 'Grid\Paragraph\Controller\DashboardController',
            'Grid\Paragraph\Controller\MetaContent'      => 'Grid\Paragraph\Controller\MetaContentController',
            'Grid\Paragraph\Controller\CreateWizard'     => 'Grid\Paragraph\Controller\CreateWizardController',
            'Grid\Paragraph\Controller\ChangeLayout'     => 'Grid\Paragraph\Controller\ChangeLayoutController',
            'Grid\Paragraph\Controller\ImportContent'    => 'Grid\Paragraph\Controller\ImportContentController',
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'Grid\Paragraph\Model\Dashboard\Customization' => 'Grid\Paragraph\Model\Dashboard\CustomizationServiceFactory',
        ),
    ),
    'acl' => array(
        'resources' => array(
            'paragraph'             => null,
            'paragraph.widget'      => null,
            'paragraph.layout'      => null,
            'paragraph.content'     => null,
            'paragraph.snippet'     => null,
            'paragraph.metaContent' => null,
        ),
    ),
    'factory' => array(
        'Grid\Core\Model\ContentUri\Factory' => array(
            'adapter'   => array(
                'paragraph'     => 'Grid\Paragraph\Model\ContentUri\Paragraph',
            ),
        ),
        'Grid\ApplicationLog\Model\Log\StructureFactory' => array(
            'adapter'    => array(
                'content-view'  => 'Grid\Paragraph\Model\Log\Structure\ContentView',
            ),
        ),
        'Grid\Paragraph\Model\Paragraph\StructureFactory' => array(
            'dependency' => 'Grid\Paragraph\Model\Paragraph\StructureInterface',
            'adapter'    => array(
                ''                      => 'Grid\Paragraph\Model\Paragraph\Structure\DefaultFallback',
                'box'                   => 'Grid\Paragraph\Model\Paragraph\Structure\Box',
                'boxes'                 => 'Grid\Paragraph\Model\Paragraph\Structure\Boxes',
                'column'                => 'Grid\Paragraph\Model\Paragraph\Structure\Column',
                'columns'               => 'Grid\Paragraph\Model\Paragraph\Structure\Columns',
                'content'               => 'Grid\Paragraph\Model\Paragraph\Structure\Content',
                'metaContent'           => 'Grid\Paragraph\Model\Paragraph\Structure\MetaContent',
                'contentPlaceholder'    => 'Grid\Paragraph\Model\Paragraph\Structure\ContentPlaceholder',
                'html'                  => 'Grid\Paragraph\Model\Paragraph\Structure\Html',
                'language'              => 'Grid\Paragraph\Model\Paragraph\Structure\Language',
                'layout'                => 'Grid\Paragraph\Model\Paragraph\Structure\Layout',
                'lead'                  => 'Grid\Paragraph\Model\Paragraph\Structure\Lead',
                'infobar'               => 'Grid\Paragraph\Model\Paragraph\Structure\Infobar',
                'title'                 => 'Grid\Paragraph\Model\Paragraph\Structure\Title',
                'widget'                => 'Grid\Paragraph\Model\Paragraph\Structure\Widget',
            ),
        ),
    ),
    'modules' => array(
        'Grid\Core' => array(
            'dashboardIcons' => array(
                'pagelist' => array(
                    'order'         => 4,
                    'label'         => 'admin.navTop.page',
                    'textDomain'    => 'admin',
                    'resource'      => 'paragraph.content.some',
                    'privilege'     => 'edit',
                    'route'         => 'Grid\Paragraph\Content\List',
                ),
            ),
            'navigation'    => array(
                'content'   => array(
                    'pages'     => array(
                        'page'      => array(
                            'label'         => 'admin.navTop.page',
                            'textDomain'    => 'admin',
                            'order'         => 2,
                            'route'         => 'Grid\Paragraph\Content\List',
                            'parentOnly'    => true,
                            'pages'         => array(
                                'list'      =>array(
                                    'label'         => 'admin.navTop.pageList',
                                    'textDomain'    => 'admin',
                                    'order'         => 1,
                                    'route'         => 'Grid\Paragraph\Content\List',
                                    'resource'      => 'paragraph.content.some',
                                    'privilege'     => 'edit',
                                ),
                                'create'    => array(
                                    'label'         => 'admin.navTop.pageCreate',
                                    'textDomain'    => 'admin',
                                    'order'         => 2,
                                    'route'         => 'Grid\Paragraph\Content\Create',
                                    'resource'      => 'paragraph.content',
                                    'privilege'     => 'create',
                                ),
                            ),
                        ),
                        'metaContent'   => array(
                            'label'         => 'admin.navTop.metaContent',
                            'textDomain'    => 'admin',
                            'order'         => 5,
                            'route'         => 'Grid\Paragraph\MetaContent\List',
                            'resource'      => 'paragraph.metaContent.some',
                            'privilege'     => 'edit',
                        ),
                        'layout'    => array(
                            'label'         => 'admin.navTop.layout',
                            'textDomain'    => 'admin',
                            'order'         => 6,
                            'route'         => 'Grid\Paragraph\Layout\List',
                            'parentOnly'    => true,
                            'pages'         => array(
                                'list'      => array(
                                    'label'         => 'admin.navTop.layoutList',
                                    'textDomain'    => 'admin',
                                    'order'         => 1,
                                    'route'         => 'Grid\Paragraph\Layout\List',
                                    'resource'      => 'paragraph.layout.some',
                                    'privilege'     => 'edit',
                                ),
                                'create'    => array(
                                    'label'         => 'admin.navTop.layoutCreate',
                                    'textDomain'    => 'admin',
                                    'order'         => 2,
                                    'route'         => 'Grid\Paragraph\Layout\Create',
                                    'resource'      => 'paragraph.layout',
                                    'privilege'     => 'create',
                                ),
                            ),
                        ),
                        'snippet'   => array(
                            'label'         => 'admin.navTop.snippet',
                            'textDomain'    => 'admin',
                            'order'         => 7,
                            'route'         => 'Grid\Paragraph\Snippet\List',
                            'parentOnly'    => true,
                            'pages'         => array(
                                'list'      => array(
                                    'label'         => 'admin.navTop.snippetList',
                                    'textDomain'    => 'admin',
                                    'order'         => 1,
                                    'route'         => 'Grid\Paragraph\Snippet\List',
                                    'resource'      => 'paragraph.snippet',
                                    'privilege'     => 'view',
                                ),
                                'create'    => array(
                                    'label'         => 'admin.navTop.snippetCreate',
                                    'textDomain'    => 'admin',
                                    'order'         => 2,
                                    'route'         => 'Grid\Paragraph\Snippet\Create',
                                    'resource'      => 'paragraph.snippet',
                                    'privilege'     => 'create',
                                ),
                                'upload'    => array(
                                    'label'         => 'admin.navTop.snippetUpload',
                                    'textDomain'    => 'admin',
                                    'order'         => 3,
                                    'route'         => 'Grid\Paragraph\Snippet\Upload',
                                    'resource'      => 'paragraph.snippet',
                                    'privilege'     => 'create',
                                ),
                            ),
                        ),
                        'widget'    => array(
                            'label'         => 'admin.navTop.widget',
                            'textDomain'    => 'admin',
                            'order'         => 8,
                            'route'         => 'Grid\Paragraph\Widget\List',
                            'resource'      => 'paragraph.widget',
                            'privilege'     => 'view',
                        ),
                    ),
                ),
            ),
        ),
        'Grid\Paragraph' => array(
            'customizeSelectors' => array(
                'container'             => '#paragraph-%id%-container.paragraph-container.paragraph-%type%-container',
                'element'               => '#paragraph-%id%.paragraph.paragraph-%type%',
                'links'                 => '#paragraph-%id%.paragraph.paragraph-%type% a',
                'linksVisited'          => '#paragraph-%id%.paragraph.paragraph-%type% a:visited',
                'linksHover'            => '#paragraph-%id%.paragraph.paragraph-%type% a:active, #paragraph-%id%.paragraph.paragraph-%type% a:hover, #paragraph-%id%.paragraph.paragraph-%type% a:focus',
                'boxTitle'              => '#paragraph-%id%.paragraph.paragraph-%type% .box-title',
                'languageUlLi'          => '#paragraph-%id%.paragraph.paragraph-%type% ul li a, #paragraph-%id%.paragraph.paragraph-%type% ul li a:visited',
                'languageUlLiHover'     => '#paragraph-%id%.paragraph.paragraph-%type% ul li a:active, #paragraph-%id%.paragraph.paragraph-%type% ul li a:hover, #paragraph-%id%.paragraph.paragraph-%type% ul li a:focus',
                'languageUlLiActive'    => '#paragraph-%id%.paragraph.paragraph-%type% ul li.active a',
                'leadImage'             => '#paragraph-%id%.paragraph.paragraph-%type% .lead-image',
            ),
            'customizeMapForms' => array(
                'box' => array(
                    'element'       => 'general',
                    'boxTitle'      => 'general',
                ),
                'boxes' => array(
                    'element'       => 'general',
                ),
                'content' => array(
                    'element'       => 'general',
                ),
                'columns' => array(
                    'element'       => 'general',
                ),
                'column' => array(
                    'element'       => 'general',
                ),
                'html' => array(
                    'element'       => 'general',
                ),
                'language' => array(
                    'element'               => 'general',
                    'languageUlLi'          => 'general',
                    'languageUlLiHover'     => 'basic',
                    'languageUlLiActive'    => 'basic',
                ),
                'layout' => array(
                    'container'     => 'basic',
                    'element'       => 'layout',
                    'links'         => 'basic',
                    'linksVisited'  => 'basic',
                    'linksHover'    => 'basic',
                ),
                'lead' => array(
                    'element'       => 'general',
                    'leadImage'     => 'image',
                ),
                'infobar' => array(
                    'element'       => 'general',
                ),
                'metaContent' => array(
                    'element'       => 'general',
                ),
                'title' => array(
                    'element'       => 'general',
                ),
                'widget' => array(
                    'element'       => 'general',
                ),
            ),
        ),
    ),
    'form' => array(
        'Grid\Paragraph\Content' => array(
            'attributes'    => array(
                'data-js-type' => 'js.form.fieldsetTabs',
            ),
            'elements'  => array(
                'name' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\Text',
                        'name'      => 'name',
                        'options'   => array(
                            'label'         => 'paragraph.form.abstract.name',
                            'required'      => false,
                            'display_group' => 'paragraph.form.content.basics',
                        ),
                    ),
                ),
                'title' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\Text',
                        'name'      => 'title',
                        'options'   => array(
                            'label'         => 'paragraph.form.content.contentTitle',
                            'required'      => false,
                            'display_group' => 'paragraph.form.content.display',
                        ),
                    ),
                ),
                'leadImage' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\PathSelect',
                        'name'      => 'leadImage',
                        'options'   => array(
                            'label'         => 'paragraph.form.content.leadImage',
                            'required'      => false,
                            'display_group' => 'paragraph.form.content.display',
                        ),
                    ),
                ),
                'leadText' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\Html',
                        'name'      => 'leadText',
                        'options'   => array(
                            'label'         => 'paragraph.form.content.leadText',
                            'required'      => false,
                            'display_group' => 'paragraph.form.content.display',
                        ),
                    ),
                ),
                'userId' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\SelectModel',
                        'name'      => 'userId',
                        'options'   => array(
                            'label'         => 'paragraph.form.content.user',
                            'required'      => false,
                            'display_group' => 'paragraph.form.content.basics',
                            'model'         => 'Grid\User\Model\User\Model',
                            'method'        => 'findOptionsExcludeGroups',
                            'empty_option'  => '',
                        ),
                        'attributes'    => array(
                            'data-js-type'  => 'js.user.select',
                        ),
                    ),
                ),
                'tags' => array(
                    'spec' => array(
                        'type'      => 'Grid\Tag\Form\Element\TagList',
                        'name'      => 'tags',
                        'options'   => array(
                            'label'         => 'paragraph.form.content.tags',
                            'required'      => false,
                            'display_group' => 'paragraph.form.content.basics',
                        ),
                    ),
                ),
                'published' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\Checkbox',
                        'name'      => 'published',
                        'options'   => array(
                            'label'         => 'paragraph.form.content.published',
                            'required'      => false,
                            'display_group' => 'paragraph.form.content.basics',
                        ),
                    ),
                ),
                'publishedFrom' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\DateTime',
                        'name'      => 'publishedFrom',
                        'options'   => array(
                            'label'         => 'paragraph.form.content.publishedFrom',
                            'required'      => false,
                            'display_group' => 'paragraph.form.content.basics',
                        ),
                    ),
                ),
                'publishedTo' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\DateTime',
                        'name'      => 'publishedTo',
                        'options'   => array(
                            'label'         => 'paragraph.form.content.publishedTo',
                            'required'      => false,
                            'display_group' => 'paragraph.form.content.basics',
                        ),
                    ),
                ),
                'allAccess' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\Checkbox',
                        'name'      => 'allAccess',
                        'options'   => array(
                            'label'         => 'paragraph.form.content.allAccess',
                            'required'      => false,
                            'display_group' => 'paragraph.form.content.rights',
                        ),
                    ),
                ),
                'accessGroups' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\SelectModel',
                        'name'      => 'accessGroups',
                        'options'   => array(
                            'multiple'      => true,
                            'label'         => 'paragraph.form.content.accessGroups',
                            'required'      => false,
                            'display_group' => 'paragraph.form.content.rights',
                            'model'         => 'Grid\User\Model\User\Group\Model',
                            'method'        => 'findOptions',
                            'empty_option'  => '',
                        ),
                        'attributes'    => array(
                            'data-js-type'  => 'js.user.multiSelectGroup',
                        ),
                    ),
                ),
                'accessUsers' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\SelectModel',
                        'name'      => 'accessUsers',
                        'options'   => array(
                            'multiple'      => true,
                            'label'         => 'paragraph.form.content.accessUsers',
                            'required'      => false,
                            'display_group' => 'paragraph.form.content.rights',
                            'model'         => 'Grid\User\Model\User\Model',
                            'method'        => 'findOptions',
                        ),
                        'attributes'    => array(
                            'data-js-type'  => 'js.user.multiSelect',
                        ),
                    ),
                ),
                'editGroups' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\SelectModel',
                        'name'      => 'editGroups',
                        'options'   => array(
                            'multiple'      => true,
                            'label'         => 'paragraph.form.content.editGroups',
                            'required'      => false,
                            'display_group' => 'paragraph.form.content.rights',
                            'model'         => 'Grid\User\Model\User\Group\Model',
                            'method'        => 'findOptions',
                        ),
                        'attributes'    => array(
                            'data-js-type'  => 'js.user.multiSelectGroup',
                        ),
                    ),
                ),
                'editUsers' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\SelectModel',
                        'name'      => 'editUsers',
                        'options'   => array(
                            'multiple'      => true,
                            'label'         => 'paragraph.form.content.editUsers',
                            'required'      => false,
                            'display_group' => 'paragraph.form.content.rights',
                            'model'         => 'Grid\User\Model\User\Model',
                            'method'        => 'findOptions',
                        ),
                        'attributes'    => array(
                            'data-js-type'  => 'js.user.multiSelect',
                        ),
                    ),
                ),
                'created' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\DateTime',
                        'name'      => 'created',
                        'options'   => array(
                            'label'         => 'paragraph.form.content.created',
                            'required'      => false,
                            'display_group' => 'paragraph.form.content.basics',
                        ),
                    ),
                ),
                'seoUri' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\Text',
                        'name'      => 'seoUri',
                        'options'   => array(
                            'label'         => 'paragraph.form.content.seoUri',
                            'required'      => false,
                            'display_group' => 'paragraph.form.content.seo',
                        ),
                    ),
                ),
                'metaRobots' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\Select',
                        'name'      => 'metaRobots',
                        'options'   => array(
                            'label'         => 'paragraph.form.content.metaRobots',
                            'required'      => false,
                            'text_domain'   => 'default',
                            'display_group' => 'paragraph.form.content.seo',
                            'options'       => array(
                                ''          => 'default.robots.default',
                                'all'       => 'default.robots.all',
                                'noindex'   => 'default.robots.noindex',
                                'nofollow'  => 'default.robots.nofollow',
                                'none'      => 'default.robots.none',
                            ),
                        ),
                    ),
                ),
                'metaKeywords' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\Textarea',
                        'name'      => 'metaKeywords',
                        'options'   => array(
                            'label'         => 'paragraph.form.content.metaKeywords',
                            'required'      => false,
                            'display_group' => 'paragraph.form.content.seo',
                        ),
                    ),
                ),
                'metaDescription' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\Textarea',
                        'name'      => 'metaDescription',
                        'options'   => array(
                            'label'         => 'paragraph.form.content.metaDescription',
                            'required'      => false,
                            'display_group' => 'paragraph.form.content.seo',
                        ),
                    ),
                ),
                'save' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\Submit',
                        'name'      => 'save',
                        'attributes'    => array(
                            'value'     => 'paragraph.form.content.submit',
                        ),
                    ),
                ),
            ),
        ),
        'Grid\Paragraph\MetaContent' => array(
            'elements'  => array(
                'title' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\Text',
                        'name'      => 'title',
                        'options'   => array(
                            'label'     => 'paragraph.form.content.contentTitle',
                            'required'  => false,
                        ),
                    ),
                ),
                'metaRobots' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\Select',
                        'name'      => 'metaRobots',
                        'options'   => array(
                            'label'         => 'paragraph.form.content.metaRobots',
                            'required'      => false,
                            'text_domain'   => 'default',
                            'options'       => array(
                                ''          => 'default.robots.default',
                                'all'       => 'default.robots.all',
                                'noindex'   => 'default.robots.noindex',
                                'nofollow'  => 'default.robots.nofollow',
                                'none'      => 'default.robots.none',
                            ),
                        ),
                    ),
                ),
                'metaKeywords' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\Textarea',
                        'name'      => 'metaKeywords',
                        'options'   => array(
                            'label'     => 'paragraph.form.content.metaKeywords',
                            'required'  => false,
                        ),
                    ),
                ),
                'metaDescription' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\Textarea',
                        'name'      => 'metaDescription',
                        'options'   => array(
                            'label'     => 'paragraph.form.content.metaDescription',
                            'required'  => false,
                        ),
                    ),
                ),
                'save' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\Submit',
                        'name'      => 'save',
                        'attributes'    => array(
                            'value'     => 'paragraph.form.content.submit',
                        ),
                    ),
                ),
            ),
        ),
        'Grid\Paragraph\Layout' => array(
            'elements'  => array(
                'name' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\Text',
                        'name'      => 'name',
                        'options'   => array(
                            'label'     => 'paragraph.form.abstract.name',
                            'required'  => false,
                        ),
                    ),
                ),
             /* 'tags' => array(
                    'spec' => array(
                        'type'      => 'Grid\Tag\Form\Element\TagList',
                        'name'      => 'tags',
                        'options'   => array(
                            'label'     => 'paragraph.form.layout.tags',
                            'required'  => false,
                        ),
                    ),
                ), */
                'save' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\Submit',
                        'name'      => 'save',
                        'attributes'    => array(
                            'value'     => 'paragraph.form.layout.submit',
                        ),
                    ),
                ),
            ),
        ),
        'Grid\Paragraph\Widget' => array(
            'elements'  => array(
                'name' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\Text',
                        'name'      => 'name',
                        'options'   => array(
                            'label'     => 'paragraph.form.abstract.name',
                            'required'  => false,
                        ),
                    ),
                ),
                'snippets'  => array(
                    'spec'  => array(
                        'type'      => 'Zork\Form\Element\MultiCheckboxGroupModel',
                        'name'      => 'snippets',
                        'options'   => array(
                            'label'     => 'paragraph.form.widget.snippets',
                            'required'  => false,
                            'model'     => 'Grid\Paragraph\Model\Snippet\Model',
                            'method'    => 'findOptions',
                        ),
                    ),
                ),
                'code'  => array(
                    'spec'  => array(
                        'type'      => 'Zork\Form\Element\Textarea',
                        'name'      => 'code',
                        'options'   => array(
                            'label'     => 'paragraph.form.widget.code',
                            'required'  => true,
                        ),
                        'attributes'    => array(
                            'data-js-type'              => 'js.form.element.codeEditor',
                            'data-js-codeeditor-mode'   => 'text/html',
                        ),
                    ),
                ),
                'save' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\Submit',
                        'name'      => 'save',
                        'attributes'    => array(
                            'value'     => 'paragraph.form.widget.submit',
                        ),
                    ),
                ),
            ),
        ),
        'Grid\Paragraph\CreateWizard\Start' => array(
            'elements'  => array(
                'type'  => array(
                    'spec'  => array(
                        'type'      => 'Zork\Form\Element\RadioGroup',
                        'name'      => 'type',
                        'options'   => array(
                         // 'label'     => 'paragraph.form.start.type',
                            'required'  => true,
                            'options'   => array(
                                'basic'         => array(
                                    'label'     => 'paragraph.type-group.basic',
                                    'order'     => 1,
                                    'options'   => array(
                                        'html'      => 'paragraph.type.html',
                                        'title'     => 'paragraph.type.title',
                                        'columns'   => 'paragraph.type.columns',
                                        'widget'    => 'paragraph.type.widget',
                                    ),
                                ),
                             /* 'container'     => array(
                                    'label'     => 'paragraph.type-group.container',
                                    'order'     => 2,
                                    'options'   => array(
                                    ),
                                ), */
                             /* 'code'          => array(
                                    'label'     => 'paragraph.type-group.code',
                                    'order'     => 3,
                                    'options'   => array(
                                    ),
                                ), */
                                'functions'     => array(
                                    'label'     => 'paragraph.type-group.functions',
                                    'order'     => 4,
                                    'options'   => array(
                                        'boxes'     => 'paragraph.type.boxes',
                                        'lead'      => 'paragraph.type.lead',
                                        'infobar'   => 'paragraph.type.infobar',
                                        'language'  => 'paragraph.type.language',
                                    ),
                                ),
                                'media'         => array(
                                    'label'     => 'paragraph.type-group.media',
                                    'order'     => 5,
                                    'options'   => array(),
                                ),
                                'social'        => array(
                                    'label'     => 'paragraph.type-group.social',
                                    'order'     => 6,
                                    'options'   => array(),
                                ),
                            ),
                        ),
                        'attributes'    => array(
                            'data-js-type'  => 'js.paragraphCreateWizard',
                            'data-js-imageradiogroup-itemsperrow' => '8',
                            'data-js-imageradiogroup-class' => 'default',
                            'data-js-imageradiogroup-imagesrc' => '/images/common/admin/paragraph-type/[value].png',
                            'data-js-imageradiogroup-descriptionkey' => 'paragraph.type.[value].description',
                         // optional attributes
                         // 'data-js-imageradiogroup-fieldsettabs' => 'false',
                         // 'data-js-imageradiogroup-vslider' => 'false',
                        ),
                    ),
                ),
            ),
            'attributes' => array(
                'class' => 'paragraph-create-type',
            ),
        ),
        'Grid\Paragraph\Meta\Customize' => array(
            'fieldsets' => array(
                'basic' => array(
                    'spec' => array(
                        'name'          => 'basic',
                        'attributes'    => array(
                         /* 'data-js-type'  => 'js.form.fieldsetTabs',
                            'data-js-tabs-placement' => 'left', */
                        ),
                        'options'       => array(
                            'label'     => 'paragraph.form.customize.basic.legend',
                        ),
                        'elements'      => array(
                            'color'     => array(
                                'spec'  => array(
                                    'type'      => 'Zork\Form\Element\Color',
                                    'name'      => 'color',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.customize.css.color',
                                        'required'  => false,
                                    ),
                                ),
                            ),
                            'fontSize'  => array(
                                'spec'  => array(
                                    'type'      => 'Zork\Form\Element\CssUnit',
                                    'name'      => 'fontSize',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.customize.css.fontSize',
                                        'required'  => false,
                                    ),
                                ),
                            ),
                            'fontFamily' => array(
                                'spec'   => array(
                                    'type'      => 'Zork\Form\Element\CssFontFamily',
                                    'name'      => 'fontFamily',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.customize.css.fontFamily',
                                        'required'  => false,
                                    ),
                                ),
                            ),
                            'fontStyle' => array(
                                'spec'   => array(
                                    'type'      => 'Zork\Form\Element\CssFontStyle',
                                    'name'      => 'fontStyle',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.customize.css.fontStyle',
                                        'required'  => false,
                                    ),
                                ),
                            ),
                            'fontWeight' => array(
                                'spec'   => array(
                                    'type'      => 'Zork\Form\Element\CssFontWeight',
                                    'name'      => 'fontWeight',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.customize.css.fontWeight',
                                        'required'  => false,
                                    ),
                                ),
                            ),
                            'textAlign' => array(
                                'spec'  => array(
                                    'type'      => 'Zork\Form\Element\CssTextAlign',
                                    'name'      => 'textAlign',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.customize.css.textAlign',
                                        'required'      => false,
                                    ),
                                ),
                            ),
                            'textTransform' => array(
                                'spec'  => array(
                                    'type'      => 'Zork\Form\Element\CssTextTransform',
                                    'name'      => 'textTransform',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.customize.css.textTransform',
                                        'required'  => false,
                                    ),
                                ),
                            ),
                            'fontVariant' => array(
                                'spec'  => array(
                                    'type'      => 'Zork\Form\Element\CssFontVariant',
                                    'name'      => 'fontVariant',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.customize.css.fontVariant',
                                        'required'  => false,
                                    ),
                                ),
                            ),
                            'backgroundColor'   => array(
                                'spec'  => array(
                                    'type'      => 'Zork\Form\Element\Color',
                                    'name'      => 'backgroundColor',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.customize.css.backgroundColor',
                                        'required'  => false,
                                    ),
                                ),
                            ),
                            'backgroundImage'   => array(
                                'spec'  => array(
                                    'type'      => 'Zork\Form\Element\CssImage',
                                    'name'      => 'backgroundImage',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.customize.css.backgroundImage',
                                        'required'  => false,
                                    ),
                                ),
                            ),
                            'backgroundRepeat'  => array(
                                'spec'  => array(
                                    'type'      => 'Zork\Form\Element\CssRepeat',
                                    'name'      => 'backgroundRepeat',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.customize.css.backgroundRepeat',
                                        'required'  => false,
                                    ),
                                ),
                            ),
                            'backgroundPositionX'  => array(
                                'spec'  => array(
                                    'type'      => 'Zork\Form\Element\CssUnit',
                                    'name'      => 'backgroundPositionX',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.customize.css.backgroundPositionX',
                                        'required'  => false,
                                    ),
                                ),
                            ),
                            'backgroundPositionY'  => array(
                                'spec'  => array(
                                    'type'      => 'Zork\Form\Element\CssUnit',
                                    'name'      => 'backgroundPositionY',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.customize.css.backgroundPositionY',
                                        'required'  => false,
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
                'layout'   => array(
                    'spec'  => array(
                        'name'          => 'layout',
                        'attributes'    => array(
                         /* 'data-js-type'  => 'js.form.fieldsetTabs',
                            'data-js-tabs-placement' => 'left', */
                        ),
                        'options'       => array(
                            'label'     => 'paragraph.form.customize.layout.legend',
                        ),
                        'elements'      => array(
                            'width'     => array(
                                'spec'  => array(
                                    'type'      => 'Zork\Form\Element\CssUnit',
                                    'name'      => 'width',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.customize.css.width',
                                        'required'  => false,
                                    ),
                                ),
                            ),
                            'paddingTop'    => array(
                                'spec'      => array(
                                    'type'      => 'Zork\Form\Element\CssUnit',
                                    'name'      => 'paddingTop',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.customize.css.paddingTop',
                                        'required'  => false,
                                    ),
                                ),
                            ),
                            'paddingLeft'   => array(
                                'spec'      => array(
                                    'type'      => 'Zork\Form\Element\CssUnit',
                                    'name'      => 'paddingLeft',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.customize.css.paddingLeft',
                                        'required'  => false,
                                    ),
                                ),
                            ),
                            'paddingRight'  => array(
                                'spec'      => array(
                                    'type'      => 'Zork\Form\Element\CssUnit',
                                    'name'      => 'paddingRight',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.customize.css.paddingRight',
                                        'required'  => false,
                                    ),
                                ),
                            ),
                            'paddingBottom' => array(
                                'spec'      => array(
                                    'type'      => 'Zork\Form\Element\CssUnit',
                                    'name'      => 'paddingBottom',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.customize.css.paddingBottom',
                                        'required'  => false,
                                    ),
                                ),
                            ),
                            'backgroundColor'   => array(
                                'spec'  => array(
                                    'type'      => 'Zork\Form\Element\Color',
                                    'name'      => 'backgroundColor',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.customize.css.backgroundColor',
                                        'required'  => false,
                                    ),
                                ),
                            ),
                            'backgroundImage'   => array(
                                'spec'  => array(
                                    'type'      => 'Zork\Form\Element\CssImage',
                                    'name'      => 'backgroundImage',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.customize.css.backgroundImage',
                                        'required'  => false,
                                    ),
                                ),
                            ),
                            'backgroundRepeat'  => array(
                                'spec'  => array(
                                    'type'      => 'Zork\Form\Element\CssRepeat',
                                    'name'      => 'backgroundRepeat',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.customize.css.backgroundRepeat',
                                        'required'  => false,
                                    ),
                                ),
                            ),
                            'backgroundPositionX'  => array(
                                'spec'  => array(
                                    'type'      => 'Zork\Form\Element\CssUnit',
                                    'name'      => 'backgroundPositionX',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.customize.css.backgroundPositionX',
                                        'required'  => false,
                                    ),
                                ),
                            ),
                            'backgroundPositionY'  => array(
                                'spec'  => array(
                                    'type'      => 'Zork\Form\Element\CssUnit',
                                    'name'      => 'backgroundPositionY',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.customize.css.backgroundPositionY',
                                        'required'  => false,
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
                'image'     => array(
                    'spec'  => array(
                        'name'          => 'image',
                        'attributes'    => array(
                            'data-js-type'  => 'js.form.fieldsetTabs',
                            'data-js-tabs-placement' => 'left',
                        ),
                        'options'       => array(
                            'label'     => 'paragraph.form.customize.image.legend',
                        ),
                        'elements'      => array(
                            'float'     => array(
                                'spec'  => array(
                                    'type'      => 'Zork\Form\Element\CssFloat',
                                    'name'      => 'float',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.customize.css.float',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.customize.general.alignment',
                                    ),
                                ),
                            ),
                            'marginTop'    => array(
                                'spec'      => array(
                                    'type'      => 'Zork\Form\Element\CssUnit',
                                    'name'      => 'marginTop',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.customize.css.marginTop',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.customize.general.margin',
                                    ),
                                ),
                            ),
                            'marginLeft'    => array(
                                'spec'      => array(
                                    'type'      => 'Zork\Form\Element\CssUnit',
                                    'name'      => 'marginLeft',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.customize.css.marginLeft',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.customize.general.margin',
                                    ),
                                ),
                            ),
                            'marginRight'   => array(
                                'spec'      => array(
                                    'type'      => 'Zork\Form\Element\CssUnit',
                                    'name'      => 'marginRight',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.customize.css.marginRight',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.customize.general.margin',
                                    ),
                                ),
                            ),
                            'marginBottom'  => array(
                                'spec'      => array(
                                    'type'      => 'Zork\Form\Element\CssUnit',
                                    'name'      => 'marginBottom',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.customize.css.marginBottom',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.customize.general.margin',
                                    ),
                                ),
                            ),
                            'borderTopWidth'    => array(
                                'spec'          => array(
                                    'type'      => 'Zork\Form\Element\CssUnit',
                                    'name'      => 'borderTopWidth',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.customize.css.borderTopWidth',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.customize.general.borderUnits',
                                    ),
                                ),
                            ),
                            'borderLeftWidth'   => array(
                                'spec'          => array(
                                    'type'      => 'Zork\Form\Element\CssUnit',
                                    'name'      => 'borderLeftWidth',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.customize.css.borderLeftWidth',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.customize.general.borderUnits',
                                    ),
                                ),
                            ),
                            'borderRightWidth'  => array(
                                'spec'          => array(
                                    'type'      => 'Zork\Form\Element\CssUnit',
                                    'name'      => 'borderRightWidth',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.customize.css.borderRightWidth',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.customize.general.borderUnits',
                                    ),
                                ),
                            ),
                            'borderBottomWidth' => array(
                                'spec'          => array(
                                    'type'      => 'Zork\Form\Element\CssUnit',
                                    'name'      => 'borderBottomWidth',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.customize.css.borderBottomWidth',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.customize.general.borderUnits',
                                    ),
                                ),
                            ),
                            'borderTopLeftRadius' => array(
                                'spec'          => array(
                                    'type'      => 'Zork\Form\Element\CssUnit',
                                    'name'      => 'borderTopLeftRadius',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.customize.css.borderTopLeftRadius',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.customize.general.borderUnits',
                                    ),
                                ),
                            ),
                            'borderTopRightRadius' => array(
                                'spec'          => array(
                                    'type'      => 'Zork\Form\Element\CssUnit',
                                    'name'      => 'borderTopRightRadius',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.customize.css.borderTopRightRadius',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.customize.general.borderUnits',
                                    ),
                                ),
                            ),
                            'borderBottomLeftRadius' => array(
                                'spec'          => array(
                                    'type'      => 'Zork\Form\Element\CssUnit',
                                    'name'      => 'borderBottomLeftRadius',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.customize.css.borderBottomLeftRadius',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.customize.general.borderUnits',
                                    ),
                                ),
                            ),
                            'borderBottomRightRadius' => array(
                                'spec'          => array(
                                    'type'      => 'Zork\Form\Element\CssUnit',
                                    'name'      => 'borderBottomRightRadius',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.customize.css.borderBottomRightRadius',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.customize.general.borderUnits',
                                    ),
                                ),
                            ),
                            'borderTopStyle'    => array(
                                'spec'          => array(
                                    'type'      => 'Zork\Form\Element\CssBorderStyle',
                                    'name'      => 'borderTopStyle',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.customize.css.borderTopStyle',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.customize.general.borderStyles',
                                    ),
                                ),
                            ),
                            'borderLeftStyle'   => array(
                                'spec'          => array(
                                    'type'      => 'Zork\Form\Element\CssBorderStyle',
                                    'name'      => 'borderLeftStyle',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.customize.css.borderLeftStyle',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.customize.general.borderStyles',
                                    ),
                                ),
                            ),
                            'borderRightStyle'  => array(
                                'spec'          => array(
                                    'type'      => 'Zork\Form\Element\CssBorderStyle',
                                    'name'      => 'borderRightStyle',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.customize.css.borderRightStyle',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.customize.general.borderStyles',
                                    ),
                                ),
                            ),
                            'borderBottomStyle' => array(
                                'spec'          => array(
                                    'type'      => 'Zork\Form\Element\CssBorderStyle',
                                    'name'      => 'borderBottomStyle',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.customize.css.borderBottomStyle',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.customize.general.borderStyles',
                                    ),
                                ),
                            ),
                            'borderTopColor'    => array(
                                'spec'          => array(
                                    'type'      => 'Zork\Form\Element\Color',
                                    'name'      => 'borderTopColor',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.customize.css.borderTopColor',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.customize.general.borderStyles',
                                    ),
                                ),
                            ),
                            'borderLeftColor'   => array(
                                'spec'          => array(
                                    'type'      => 'Zork\Form\Element\Color',
                                    'name'      => 'borderLeftColor',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.customize.css.borderLeftColor',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.customize.general.borderStyles',
                                    ),
                                ),
                            ),
                            'borderRightColor'  => array(
                                'spec'          => array(
                                    'type'      => 'Zork\Form\Element\Color',
                                    'name'      => 'borderRightColor',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.customize.css.borderRightColor',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.customize.general.borderStyles',
                                    ),
                                ),
                            ),
                            'borderBottomColor' => array(
                                'spec'          => array(
                                    'type'      => 'Zork\Form\Element\Color',
                                    'name'      => 'borderBottomColor',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.customize.css.borderBottomColor',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.customize.general.borderStyles',
                                    ),
                                ),
                            ),
                            'paddingTop'    => array(
                                'spec'      => array(
                                    'type'      => 'Zork\Form\Element\CssUnit',
                                    'name'      => 'paddingTop',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.customize.css.paddingTop',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.customize.general.padding',
                                    ),
                                ),
                            ),
                            'paddingLeft'   => array(
                                'spec'      => array(
                                    'type'      => 'Zork\Form\Element\CssUnit',
                                    'name'      => 'paddingLeft',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.customize.css.paddingLeft',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.customize.general.padding',
                                    ),
                                ),
                            ),
                            'paddingRight'  => array(
                                'spec'      => array(
                                    'type'      => 'Zork\Form\Element\CssUnit',
                                    'name'      => 'paddingRight',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.customize.css.paddingRight',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.customize.general.padding',
                                    ),
                                ),
                            ),
                            'paddingBottom' => array(
                                'spec'      => array(
                                    'type'      => 'Zork\Form\Element\CssUnit',
                                    'name'      => 'paddingBottom',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.customize.css.paddingBottom',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.customize.general.padding',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
                'general'   => array(
                    'spec'  => array(
                        'name'          => 'general',
                        'attributes'    => array(
                            'data-js-type'  => 'js.form.fieldsetTabs',
                            'data-js-tabs-placement' => 'left',
                        ),
                        'options'       => array(
                            'label'     => 'paragraph.form.customize.general.legend',
                        ),
                        'elements' => array(
                            'color'     => array(
                                'spec'  => array(
                                    'type'      => 'Zork\Form\Element\Color',
                                    'name'      => 'color',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.customize.css.color',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.customize.general.text',
                                    ),
                                ),
                            ),
                            'fontSize'  => array(
                                'spec'  => array(
                                    'type'      => 'Zork\Form\Element\CssUnit',
                                    'name'      => 'fontSize',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.customize.css.fontSize',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.customize.general.text',
                                    ),
                                ),
                            ),
                            'fontFamily' => array(
                                'spec'   => array(
                                    'type'      => 'Zork\Form\Element\CssFontFamily',
                                    'name'      => 'fontFamily',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.customize.css.fontFamily',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.customize.general.text',
                                    ),
                                ),
                            ),
                            'fontStyle' => array(
                                'spec'   => array(
                                    'type'      => 'Zork\Form\Element\CssFontStyle',
                                    'name'      => 'fontStyle',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.customize.css.fontStyle',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.customize.general.text',
                                    ),
                                ),
                            ),
                            'fontWeight' => array(
                                'spec'   => array(
                                    'type'      => 'Zork\Form\Element\CssFontWeight',
                                    'name'      => 'fontWeight',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.customize.css.fontWeight',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.customize.general.text',
                                    ),
                                ),
                            ),
                            'textAlign' => array(
                                'spec'  => array(
                                    'type'      => 'Zork\Form\Element\CssTextAlign',
                                    'name'      => 'textAlign',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.customize.css.textAlign',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.customize.general.text',
                                    ),
                                ),
                            ),
                            'textTransform' => array(
                                'spec'  => array(
                                    'type'      => 'Zork\Form\Element\CssTextTransform',
                                    'name'      => 'textTransform',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.customize.css.textTransform',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.customize.general.text',
                                    ),
                                ),
                            ),
                            'fontVariant' => array(
                                'spec'  => array(
                                    'type'      => 'Zork\Form\Element\CssFontVariant',
                                    'name'      => 'fontVariant',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.customize.css.fontVariant',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.customize.general.text',
                                    ),
                                ),
                            ),
                            'marginTop'    => array(
                                'spec'      => array(
                                    'type'      => 'Zork\Form\Element\CssUnit',
                                    'name'      => 'marginTop',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.customize.css.marginTop',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.customize.general.margin',
                                    ),
                                ),
                            ),
                            'marginLeft'    => array(
                                'spec'      => array(
                                    'type'      => 'Zork\Form\Element\CssUnit',
                                    'name'      => 'marginLeft',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.customize.css.marginLeft',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.customize.general.margin',
                                    ),
                                ),
                            ),
                            'marginRight'   => array(
                                'spec'      => array(
                                    'type'      => 'Zork\Form\Element\CssUnit',
                                    'name'      => 'marginRight',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.customize.css.marginRight',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.customize.general.margin',
                                    ),
                                ),
                            ),
                            'marginBottom'  => array(
                                'spec'      => array(
                                    'type'      => 'Zork\Form\Element\CssUnit',
                                    'name'      => 'marginBottom',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.customize.css.marginBottom',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.customize.general.margin',
                                    ),
                                ),
                            ),
                            'borderTopWidth'    => array(
                                'spec'          => array(
                                    'type'      => 'Zork\Form\Element\CssUnit',
                                    'name'      => 'borderTopWidth',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.customize.css.borderTopWidth',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.customize.general.borderUnits',
                                    ),
                                ),
                            ),
                            'borderLeftWidth'   => array(
                                'spec'          => array(
                                    'type'      => 'Zork\Form\Element\CssUnit',
                                    'name'      => 'borderLeftWidth',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.customize.css.borderLeftWidth',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.customize.general.borderUnits',
                                    ),
                                ),
                            ),
                            'borderRightWidth'  => array(
                                'spec'          => array(
                                    'type'      => 'Zork\Form\Element\CssUnit',
                                    'name'      => 'borderRightWidth',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.customize.css.borderRightWidth',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.customize.general.borderUnits',
                                    ),
                                ),
                            ),
                            'borderBottomWidth' => array(
                                'spec'          => array(
                                    'type'      => 'Zork\Form\Element\CssUnit',
                                    'name'      => 'borderBottomWidth',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.customize.css.borderBottomWidth',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.customize.general.borderUnits',
                                    ),
                                ),
                            ),
                            'borderTopLeftRadius' => array(
                                'spec'          => array(
                                    'type'      => 'Zork\Form\Element\CssUnit',
                                    'name'      => 'borderTopLeftRadius',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.customize.css.borderTopLeftRadius',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.customize.general.borderUnits',
                                    ),
                                ),
                            ),
                            'borderTopRightRadius' => array(
                                'spec'          => array(
                                    'type'      => 'Zork\Form\Element\CssUnit',
                                    'name'      => 'borderTopRightRadius',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.customize.css.borderTopRightRadius',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.customize.general.borderUnits',
                                    ),
                                ),
                            ),
                            'borderBottomLeftRadius' => array(
                                'spec'          => array(
                                    'type'      => 'Zork\Form\Element\CssUnit',
                                    'name'      => 'borderBottomLeftRadius',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.customize.css.borderBottomLeftRadius',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.customize.general.borderUnits',
                                    ),
                                ),
                            ),
                            'borderBottomRightRadius' => array(
                                'spec'          => array(
                                    'type'      => 'Zork\Form\Element\CssUnit',
                                    'name'      => 'borderBottomRightRadius',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.customize.css.borderBottomRightRadius',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.customize.general.borderUnits',
                                    ),
                                ),
                            ),
                            'borderTopStyle'    => array(
                                'spec'          => array(
                                    'type'      => 'Zork\Form\Element\CssBorderStyle',
                                    'name'      => 'borderTopStyle',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.customize.css.borderTopStyle',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.customize.general.borderStyles',
                                    ),
                                ),
                            ),
                            'borderLeftStyle'   => array(
                                'spec'          => array(
                                    'type'      => 'Zork\Form\Element\CssBorderStyle',
                                    'name'      => 'borderLeftStyle',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.customize.css.borderLeftStyle',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.customize.general.borderStyles',
                                    ),
                                ),
                            ),
                            'borderRightStyle'  => array(
                                'spec'          => array(
                                    'type'      => 'Zork\Form\Element\CssBorderStyle',
                                    'name'      => 'borderRightStyle',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.customize.css.borderRightStyle',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.customize.general.borderStyles',
                                    ),
                                ),
                            ),
                            'borderBottomStyle' => array(
                                'spec'          => array(
                                    'type'      => 'Zork\Form\Element\CssBorderStyle',
                                    'name'      => 'borderBottomStyle',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.customize.css.borderBottomStyle',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.customize.general.borderStyles',
                                    ),
                                ),
                            ),
                            'borderTopColor'    => array(
                                'spec'          => array(
                                    'type'      => 'Zork\Form\Element\Color',
                                    'name'      => 'borderTopColor',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.customize.css.borderTopColor',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.customize.general.borderStyles',
                                    ),
                                ),
                            ),
                            'borderLeftColor'   => array(
                                'spec'          => array(
                                    'type'      => 'Zork\Form\Element\Color',
                                    'name'      => 'borderLeftColor',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.customize.css.borderLeftColor',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.customize.general.borderStyles',
                                    ),
                                ),
                            ),
                            'borderRightColor'  => array(
                                'spec'          => array(
                                    'type'      => 'Zork\Form\Element\Color',
                                    'name'      => 'borderRightColor',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.customize.css.borderRightColor',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.customize.general.borderStyles',
                                    ),
                                ),
                            ),
                            'borderBottomColor' => array(
                                'spec'          => array(
                                    'type'      => 'Zork\Form\Element\Color',
                                    'name'      => 'borderBottomColor',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.customize.css.borderBottomColor',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.customize.general.borderStyles',
                                    ),
                                ),
                            ),
                            'paddingTop'    => array(
                                'spec'      => array(
                                    'type'      => 'Zork\Form\Element\CssUnit',
                                    'name'      => 'paddingTop',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.customize.css.paddingTop',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.customize.general.padding',
                                    ),
                                ),
                            ),
                            'paddingLeft'   => array(
                                'spec'      => array(
                                    'type'      => 'Zork\Form\Element\CssUnit',
                                    'name'      => 'paddingLeft',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.customize.css.paddingLeft',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.customize.general.padding',
                                    ),
                                ),
                            ),
                            'paddingRight'  => array(
                                'spec'      => array(
                                    'type'      => 'Zork\Form\Element\CssUnit',
                                    'name'      => 'paddingRight',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.customize.css.paddingRight',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.customize.general.padding',
                                    ),
                                ),
                            ),
                            'paddingBottom' => array(
                                'spec'      => array(
                                    'type'      => 'Zork\Form\Element\CssUnit',
                                    'name'      => 'paddingBottom',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.customize.css.paddingBottom',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.customize.general.padding',
                                    ),
                                ),
                            ),
                            'backgroundColor'   => array(
                                'spec'  => array(
                                    'type'      => 'Zork\Form\Element\Color',
                                    'name'      => 'backgroundColor',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.customize.css.backgroundColor',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.customize.general.background',
                                    ),
                                ),
                            ),
                            'backgroundImage'   => array(
                                'spec'  => array(
                                    'type'      => 'Zork\Form\Element\CssImage',
                                    'name'      => 'backgroundImage',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.customize.css.backgroundImage',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.customize.general.background',
                                    ),
                                ),
                            ),
                            'backgroundRepeat'  => array(
                                'spec'  => array(
                                    'type'      => 'Zork\Form\Element\CssRepeat',
                                    'name'      => 'backgroundRepeat',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.customize.css.backgroundRepeat',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.customize.general.background',
                                    ),
                                ),
                            ),
                            'backgroundPositionX'  => array(
                                'spec'  => array(
                                    'type'      => 'Zork\Form\Element\CssUnit',
                                    'name'      => 'backgroundPositionX',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.customize.css.backgroundPositionX',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.customize.general.background',
                                    ),
                                ),
                            ),
                            'backgroundPositionY'  => array(
                                'spec'  => array(
                                    'type'      => 'Zork\Form\Element\CssUnit',
                                    'name'      => 'backgroundPositionY',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.customize.css.backgroundPositionY',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.customize.general.background',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ),
        'Grid\Paragraph\Meta\Create' => array(
            'fieldsets' => array(
                'columns'   => array(
                    'spec'  => array(
                        'name'      => 'columns',
                        'options'   => array(
                            'label'     => 'paragraph.type.columns',
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
                            'columnCount' => array(
                                'spec' => array(
                                    'type'      => 'Zork\Form\Element\Number',
                                    'name'      => 'columnCount',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.columns.columnCount',
                                        'required'  => true,
                                        'min'       => 1,
                                        'max'       => 8,
                                    ),
                                    'attributes'    => array(
                                        'value'     => 2,
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
                'lead'      => array(
                    'spec'  => array(
                        'name'      => 'lead',
                        'options'   => array(
                            'label'     => 'paragraph.type.lead',
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
                        ),
                    ),
                ),
                'infobar'   => array(
                    'spec'  => array(
                        'name'      => 'infobar',
                        'options'   => array(
                            'label'     => 'paragraph.type.infobar',
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
                            'skin' => array(
                                'spec' => array(
                                    'type'      => 'Zork\Form\Element\Select',
                                    'name'      => 'skin',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.infobar.skin',
                                        'required'  => false,
                                        'options'   => array(
                                            'left'  => 'paragraph.form.infobar.skin.left',
                                            'right' => 'paragraph.form.infobar.skin.right',
                                        ),
                                    ),
                                ),
                            ),
                            'displayUserAvatar' => array(
                                'spec' => array(
                                    'type'      => 'Zork\Form\Element\Checkbox',
                                    'name'      => 'displayUserAvatar',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.infobar.displayUserAvatar',
                                        'required'  => false,
                                    ),
                                    'attributes'    => array(
                                        'checked'   => true,
                                    ),
                                ),
                            ),
                            'displayUserDisplayName' => array(
                                'spec' => array(
                                    'type'      => 'Zork\Form\Element\Checkbox',
                                    'name'      => 'displayUserDisplayName',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.infobar.displayUserDisplayName',
                                        'required'  => false,
                                    ),
                                    'attributes'    => array(
                                        'checked'   => true,
                                    ),
                                ),
                            ),
                            'displayPublishedDate' => array(
                                'spec' => array(
                                    'type'      => 'Zork\Form\Element\Checkbox',
                                    'name'      => 'displayPublishedDate',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.infobar.displayPublishedDate',
                                        'required'  => false,
                                    ),
                                    'attributes'    => array(
                                        'checked'   => true,
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
                'title'     => array(
                    'spec'  => array(
                        'name'      => 'title',
                        'options'   => array(
                            'label'     => 'paragraph.type.title',
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
                                        'label'         => 'paragraph.form.title.separator',
                                        'required'      => false,
                                        'empty_option'  => '',
                                        'options'       => include 'config/separators.php',
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
                'box'   => array(
                    'spec' => array(
                        'name'      => 'box',
                        'options'   => array(
                            'label'     => 'paragraph.type.box',
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
                            'title' => array(
                                'spec' => array(
                                    'type'      => 'Zork\Form\Element\Text',
                                    'name'      => 'title',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.box.title',
                                        'required'  => false,
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
                'boxes' => array(
                    'spec' => array(
                        'name'      => 'boxes',
                        'options'   => array(
                            'label'     => 'paragraph.type.boxes',
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
                        ),
                    ),
                ),
                'column'    => array(
                    'spec' => array(
                        'name'      => 'column',
                        'options'   => array(
                            'label'     => 'paragraph.type.column',
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
                        ),
                    ),
                ),
                'columns'   => array(
                    'spec'  => array(
                        'name'      => 'columns',
                        'options'   => array(
                            'label'     => 'paragraph.type.columns',
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
                            'columnWidths' => array(
                                'spec' => array(
                                    'type'      => 'Grid\Paragraph\Form\Element\ColumnsPercentages',
                                    'name'      => 'columnWidths',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.columns.columnWidths',
                                        'required'  => false,
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
                'content'   => array(
                    'spec'  => array(
                        'name'      => 'content',
                        'options'   => array(
                            'label'     => 'paragraph.type.content',
                            'required'  => true,
                        ),
                        'attributes'    => array(
                            'data-js-type'              => 'js.form.fieldsetTabs',
                            'data-js-tabs-placement'    => 'left',
                        ),
                        'elements'  => array(
                            'name'  => array(
                                'spec'  => array(
                                    'type'      => 'Zork\Form\Element\Text',
                                    'name'      => 'name',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.abstract.name',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.content.basics',
                                    ),
                                ),
                            ),
                            'title' => array(
                                'spec' => array(
                                    'type'      => 'Zork\Form\Element\Text',
                                    'name'      => 'title',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.content.contentTitle',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.content.display',
                                    ),
                                ),
                            ),
                            'leadImage' => array(
                                'spec' => array(
                                    'type'      => 'Zork\Form\Element\PathSelect',
                                    'name'      => 'leadImage',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.content.leadImage',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.content.display',
                                    ),
                                ),
                            ),
                            'leadText' => array(
                                'spec' => array(
                                    'type'      => 'Zork\Form\Element\Html',
                                    'name'      => 'leadText',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.content.leadText',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.content.display',
                                    ),
                                ),
                            ),
                            'userId' => array(
                                'spec' => array(
                                    'type'      => 'Zork\Form\Element\SelectModel',
                                    'name'      => 'userId',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.content.user',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.content.basics',
                                        'model'         => 'Grid\User\Model\User\Model',
                                        'method'        => 'findOptionsExcludeGroups',
                                        'empty_option'  => '',
                                    ),
                                    'attributes'    => array(
                                        'data-js-type'  => 'js.user.select',
                                    ),
                                ),
                            ),
                            'tags' => array(
                                'spec' => array(
                                    'type'      => 'Grid\Tag\Form\Element\TagList',
                                    'name'      => 'tags',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.content.tags',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.content.basics',
                                    ),
                                ),
                            ),
                            'published' => array(
                                'spec' => array(
                                    'type'      => 'Zork\Form\Element\Checkbox',
                                    'name'      => 'published',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.content.published',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.content.basics',
                                    ),
                                ),
                            ),
                            'publishedFrom' => array(
                                'spec' => array(
                                    'type'      => 'Zork\Form\Element\DateTime',
                                    'name'      => 'publishedFrom',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.content.publishedFrom',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.content.basics',
                                    ),
                                ),
                            ),
                            'publishedTo' => array(
                                'spec' => array(
                                    'type'      => 'Zork\Form\Element\DateTime',
                                    'name'      => 'publishedTo',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.content.publishedTo',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.content.basics',
                                    ),
                                ),
                            ),
                            'allAccess' => array(
                                'spec' => array(
                                    'type'      => 'Zork\Form\Element\Checkbox',
                                    'name'      => 'allAccess',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.content.allAccess',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.content.rights',
                                    ),
                                ),
                            ),
                            'accessGroups' => array(
                                'spec' => array(
                                    'type'      => 'Zork\Form\Element\SelectModel',
                                    'name'      => 'accessGroups',
                                    'options'   => array(
                                        'multiple'      => true,
                                        'label'         => 'paragraph.form.content.accessGroups',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.content.rights',
                                        'model'         => 'Grid\User\Model\User\Group\Model',
                                        'method'        => 'findOptions',
                                        'empty_option'  => '',
                                    ),
                                    'attributes'    => array(
                                        'data-js-type'  => 'js.user.multiSelectGroup',
                                    ),
                                ),
                            ),
                            'accessUsers' => array(
                                'spec' => array(
                                    'type'      => 'Zork\Form\Element\SelectModel',
                                    'name'      => 'accessUsers',
                                    'options'   => array(
                                        'multiple'      => true,
                                        'label'         => 'paragraph.form.content.accessUsers',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.content.rights',
                                        'model'         => 'Grid\User\Model\User\Model',
                                        'method'        => 'findOptions',
                                    ),
                                    'attributes'    => array(
                                        'data-js-type'  => 'js.user.multiSelect',
                                    ),
                                ),
                            ),
                         /* 'editGroups' => array(
                                'spec' => array(
                                    'type'      => 'Zork\Form\Element\SelectModel',
                                    'name'      => 'editGroups',
                                    'options'   => array(
                                        'multiple'      => true,
                                        'label'         => 'paragraph.form.content.editGroups',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.content.rights',
                                        'model'         => 'Grid\User\Model\User\Group\Model',
                                        'method'        => 'findOptions',
                                    ),
                                    'attributes'    => array(
                                        'data-js-type'  => 'js.user.multiSelectGroup',
                                    ),
                                ),
                            ),
                            'editUsers' => array(
                                'spec' => array(
                                    'type'      => 'Zork\Form\Element\SelectModel',
                                    'name'      => 'editUsers',
                                    'options'   => array(
                                        'multiple'      => true,
                                        'label'         => 'paragraph.form.content.editUsers',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.content.rights',
                                        'model'         => 'Grid\User\Model\User\Model',
                                        'method'        => 'findOptions',
                                    ),
                                    'attributes'    => array(
                                        'data-js-type'  => 'js.user.multiSelect',
                                    ),
                                ),
                            ), */
                            'created' => array(
                                'spec' => array(
                                    'type'      => 'Zork\Form\Element\DateTime',
                                    'name'      => 'created',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.content.created',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.content.basics',
                                    ),
                                ),
                            ),
                            'seoUri' => array(
                                'spec' => array(
                                    'type'      => 'Zork\Form\Element\Text',
                                    'name'      => 'seoUri',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.content.seoUri',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.content.seo',
                                    ),
                                ),
                            ),
                            'metaRobots' => array(
                                'spec' => array(
                                    'type'      => 'Zork\Form\Element\Select',
                                    'name'      => 'metaRobots',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.content.metaRobots',
                                        'text_domain'   => 'default',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.content.seo',
                                        'options'       => array(
                                            ''          => 'default.robots.default',
                                            'all'       => 'default.robots.all',
                                            'noindex'   => 'default.robots.noindex',
                                            'nofollow'  => 'default.robots.nofollow',
                                            'none'      => 'default.robots.none',
                                        ),
                                    ),
                                ),
                            ),
                            'metaKeywords' => array(
                                'spec' => array(
                                    'type'      => 'Zork\Form\Element\Textarea',
                                    'name'      => 'metaKeywords',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.content.metaKeywords',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.content.seo',
                                    ),
                                ),
                            ),
                            'metaDescription' => array(
                                'spec' => array(
                                    'type'      => 'Zork\Form\Element\Textarea',
                                    'name'      => 'metaDescription',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.content.metaDescription',
                                        'required'      => false,
                                        'display_group' => 'paragraph.form.content.seo',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
                'metaContent'   => array(
                    'spec'  => array(
                        'name'      => 'metaContent',
                        'options'   => array(
                            'label'     => 'paragraph.type.metaContent',
                            'required'  => true,
                        ),
                        'elements'  => array(
                            'title' => array(
                                'spec' => array(
                                    'type'      => 'Zork\Form\Element\Text',
                                    'name'      => 'title',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.content.contentTitle',
                                        'required'  => false,
                                    ),
                                ),
                            ),
                            'metaRobots' => array(
                                'spec' => array(
                                    'type'      => 'Zork\Form\Element\Select',
                                    'name'      => 'metaRobots',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.content.metaRobots',
                                        'text_domain'   => 'default',
                                        'required'      => false,
                                        'options'       => array(
                                            ''          => 'default.robots.default',
                                            'all'       => 'default.robots.all',
                                            'noindex'   => 'default.robots.noindex',
                                            'nofollow'  => 'default.robots.nofollow',
                                            'none'      => 'default.robots.none',
                                        ),
                                    ),
                                ),
                            ),
                            'metaKeywords' => array(
                                'spec' => array(
                                    'type'      => 'Zork\Form\Element\Textarea',
                                    'name'      => 'metaKeywords',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.content.metaKeywords',
                                        'required'  => false,
                                    ),
                                ),
                            ),
                            'metaDescription' => array(
                                'spec' => array(
                                    'type'      => 'Zork\Form\Element\Textarea',
                                    'name'      => 'metaDescription',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.content.metaDescription',
                                        'required'  => false,
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
                'language'  => array(
                    'spec'  => array(
                        'name'      => 'language',
                        'options'   => array(
                            'label'     => 'paragraph.type.language',
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
                            'locales'   => array(
                                'spec'  => array(
                                    'type'      => 'Zork\Form\Element\Locales',
                                    'name'      => 'locales',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.language.locales',
                                        'required'  => false,
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
                'layout'  => array(
                    'spec'  => array(
                        'name'      => 'layout',
                        'options'   => array(
                            'label'     => 'paragraph.type.layout',
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
                         /* 'tags' => array(
                                'spec' => array(
                                    'type'      => 'Grid\Tag\Form\Element\TagList',
                                    'name'      => 'tags',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.layout.tags',
                                        'required'  => false,
                                    ),
                                ),
                            ), */
                        ),
                    ),
                ),
                'html'      => array(
                    'spec'  => array(
                        'name'      => 'html',
                        'options'   => array(
                            'label'     => 'paragraph.type.html',
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
                            'html'  => array(
                                'spec'  => array(
                                    'type'      => 'Zork\Form\Element\Html',
                                    'name'      => 'html',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.html.html',
                                        'required'  => false,
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
                'lead'      => array(
                    'spec'  => array(
                        'name'      => 'lead',
                        'options'   => array(
                            'label'     => 'paragraph.type.lead',
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
                            'rootText' => array(
                                'spec' => array(
                                    'type'      => 'Zork\Form\Element\Html',
                                    'name'      => 'rootText',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.content.leadText',
                                        'required'  => false,
                                    ),
                                ),
                            ),
                            'rootImage' => array(
                                'spec' => array(
                                    'type'      => 'Zork\Form\Element\PathSelect',
                                    'name'      => 'rootImage',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.content.leadImage',
                                        'required'  => false,
                                    ),
                                ),
                            ),
                            'imageWidth' => array(
                                'spec'  => array(
                                    'type'      => 'Zork\Form\Element\Number',
                                    'name'      => 'width',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.lead.imageWidth',
                                        'required'  => false,
                                    ),
                                    'attributes'    => array(
                                        'min'       => 25,
                                        'max'       => 800,
                                    ),
                                ),
                            ),
                            'imageHeight' => array(
                                'spec'  => array(
                                    'type'      => 'Zork\Form\Element\Number',
                                    'name'      => 'height',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.lead.imageHeight',
                                        'required'  => false,
                                    ),
                                    'attributes'    => array(
                                        'min'       => 25,
                                        'max'       => 600,
                                    ),
                                ),
                            ),
                            'imageMethod' => array(
                                'spec'  => array(
                                    'type'      => 'Zork\Form\Element\Select',
                                    'name'      => 'method',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.lead.imageMethod',
                                        'required'      => false,
                                        'text_domain'   => 'image',
                                        'options'       => array(
                                            'fit'       => 'image.method.fit',
                                            'frame'     => 'image.method.frame',
                                            'cut'       => 'image.method.cut',
                                            'stretch'   => 'image.method.stretch',
                                        ),
                                    ),
                                ),
                            ),
                            'imageBgColor' => array(
                                'spec'  => array(
                                    'type'      => 'Zork\Form\Element\Color',
                                    'name'      => 'bgColor',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.lead.imageBgColor',
                                        'required'  => false,
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
                'infobar'   => array(
                    'spec'  => array(
                        'name'      => 'infobar',
                        'options'   => array(
                            'label'     => 'paragraph.type.infobar',
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
                            'skin' => array(
                                'spec' => array(
                                    'type'      => 'Zork\Form\Element\Select',
                                    'name'      => 'skin',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.infobar.skin',
                                        'required'  => false,
                                        'options'   => array(
                                            'left'  => 'paragraph.form.infobar.skin.left',
                                            'right' => 'paragraph.form.infobar.skin.right',
                                        ),
                                    ),
                                ),
                            ),
                            'displayUserAvatar' => array(
                                'spec' => array(
                                    'type'      => 'Zork\Form\Element\Checkbox',
                                    'name'      => 'displayUserAvatar',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.infobar.displayUserAvatar',
                                        'required'  => false,
                                    ),
                                ),
                            ),
                            'displayUserDisplayName' => array(
                                'spec' => array(
                                    'type'      => 'Zork\Form\Element\Checkbox',
                                    'name'      => 'displayUserDisplayName',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.infobar.displayUserDisplayName',
                                        'required'  => false,
                                    ),
                                ),
                            ),
                            'displayPublishedDate' => array(
                                'spec' => array(
                                    'type'      => 'Zork\Form\Element\Checkbox',
                                    'name'      => 'displayPublishedDate',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.infobar.displayPublishedDate',
                                        'required'  => false,
                                    ),
                                ),
                            ),
                            'rootCreated' => array(
                                'spec' => array(
                                    'type'      => 'Zork\Form\Element\DateTime',
                                    'name'      => 'rootCreated',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.content.created',
                                        'required'  => false,
                                    ),
                                ),
                            ),
                            'rootUserId' => array(
                                'spec' => array(
                                    'type'      => 'Zork\Form\Element\SelectModel',
                                    'name'      => 'rootUserId',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.content.user',
                                        'required'      => false,
                                        'model'         => 'Grid\User\Model\User\Model',
                                        'method'        => 'findOptionsExcludeGroups',
                                        'empty_option'  => '',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
                'title'     => array(
                    'spec'  => array(
                        'name'      => 'title',
                        'options'   => array(
                            'label'     => 'paragraph.type.title',
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
                            'rootTitle' => array(
                                'spec'  => array(
                                    'type'      => 'Zork\Form\Element\Text',
                                    'name'      => 'rootTitle',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.content.contentTitle',
                                        'required'  => false,
                                    ),
                                ),
                            ),
                            'separator' => array(
                                'spec'  => array(
                                    'type'      => 'Zork\Form\Element\Select',
                                    'name'      => 'separator',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.title.separator',
                                        'required'      => false,
                                        'empty_option'  => '',
                                        'options'       => include 'config/separators.php',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
                'widget'    => array(
                    'spec'  => array(
                        'name'      => 'widget',
                        'options'   => array(
                            'label'     => 'paragraph.type.widget',
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
                            'snippets'  => array(
                                'spec'  => array(
                                    'type'      => 'Zork\Form\Element\MultiCheckboxGroupModel',
                                    'name'      => 'snippets',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.widget.snippets',
                                        'required'  => false,
                                        'model'     => 'Grid\Paragraph\Model\Snippet\Model',
                                        'method'    => 'findOptions',
                                    ),
                                ),
                            ),
                            'code'  => array(
                                'spec'  => array(
                                    'type'      => 'Zork\Form\Element\Textarea',
                                    'name'      => 'code',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.widget.code',
                                        'required'  => true,
                                    ),
                                    'attributes'    => array(
                                        'data-js-type'              => 'js.form.element.codeEditor',
                                        'data-js-codeeditor-mode'   => 'text/html',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ),
        'Grid\Paragraph\ChangeLayout\Local' => array(
            'elements' => array(
                'returnUri' => array(
                    'spec'  => array(
                        'type'      => 'Zork\Form\Element\Hidden',
                        'name'      => 'returnUri',
                    ),
                ),
                'layoutId'  => array(
                    'spec'  => array(
                        'type'      => 'Zork\Form\Element\RadioGroupModel',
                        'name'      => 'layoutId',
                        'options'   => array(
                         // 'label'         => 'paragraph.form.content.layout',
                         // 'text_domain'   => 'paragraph',
                            'empty_option'  => '', // 'paragraph.form.content.layout.default',
                            'required'      => false,
                            'model'         => 'Grid\Paragraph\Model\Paragraph\Model',
                            'method'        => 'findOptions',
                            'arguments'     => array(
                                'layout',
                            ),
                            'option_attribute_filters'  => array(
                                'data-created'          => 'date',
                                'data-last-modified'    => 'date',
                            ),
                        ),
                        'attributes' => array(
                            'data-js-type'  => 'js.layoutSelect',
                            'data-js-layoutselect-type' => 'local',
                        ),
                    ),
                ),
                'save' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\Submit',
                        'name'      => 'save',
                        'attributes'    => array(
                            'value'     => 'paragraph.form.content.submit',
                        ),
                    ),
                ),
            ),
            'attributes' => array(
                'class' => 'paragraph-changelayout',
            ),
        ),
        'Grid\Paragraph\ChangeLayout\Import' => array(
            'elements' => array(
                'returnUri' => array(
                    'spec'  => array(
                        'type'      => 'Zork\Form\Element\Hidden',
                        'name'      => 'returnUri',
                    ),
                ),
                'importId' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\RadioGroupModel',
                        'name'      => 'importId',
                        'options'   => array(
                            'label'     => '', // 'paragraph.form.content.layout',
                            'required'  => true,
                            'model'     => 'Grid\Paragraph\Model\Paragraph\Model',
                            'method'    => 'findOptions',
                            'arguments' => array(
                                'layout',
                                '_central',
                            ),
                        ),
                        'attributes' => array(
                            'data-js-type'  => 'js.layoutSelect',
                            'data-js-layoutselect-imagesrc' => '/uploads/_central/pages/layouts/[value]/thumb.png',
                            'data-js-layoutselect-type' => 'import',
                        ),
                    ),
                ),
                'save' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\Submit',
                        'name'      => 'save',
                        'attributes'    => array(
                            'value'     => 'paragraph.form.content.submit',
                        ),
                    ),
                ),
            ),
            'attributes' => array(
                'class' => 'paragraph-changelayout',
            ),
        ),
        'Grid\Paragraph\ImportContent\Import' => array(
            'elements' => array(
                'importId'  => array(
                    'spec'  => array(
                        'type'      => 'Zork\Form\Element\RadioGroupModel',
                        'name'      => 'importId',
                        'options'   => array(
                         // 'label'     => 'paragraph.form.content.model',
                            'required'  => true,
                            'model'     => 'Grid\Paragraph\Model\Paragraph\Model',
                            'method'    => 'findOptions',
                            'arguments' => array(
                                'content',
                                '_central',
                                'created',
                            ),
                        ),
                        'attributes'    => array(
                            'data-js-type'                              => 'js.form.imageRadioGroup',
                            'data-js-imageradiogroup-itemsperrow'       => '3',
                            'data-js-imageradiogroup-class'             => 'default align-center',
                            'data-js-imageradiogroup-imagesrc'          => '/images/common/admin/paragraph-content-type/[value].png',
                            'data-js-imageradiogroup-descriptionkey'    => 'paragraph.form.content.model.[value].description',
                            'data-js-imageradiogroup-fieldsettabs'      => 'false',
                        ),
                    ),
                ),
                'menuId'    => array(
                    'spec'  => array(
                        'type'      => 'Zork\Form\Element\SelectModel',
                        'name'      => 'menuId',
                        'options'   => array(
                            'label'     => 'paragraph.form.content.menuId',
                            'required'  => false,
                            'model'     => 'Grid\Menu\Model\Menu\Model',
                            'method'    => 'findOptions',
                        ),
                        'attributes'    => array(
                            'data-js-type'  => 'js.menu.select',
                        ),
                    ),
                ),
                'name'      => array(
                    'spec'  => array(
                        'type'      => 'Zork\Form\Element\Text',
                        'name'      => 'name',
                        'options'   => array(
                            'label'     => 'paragraph.form.abstract.name',
                            'required'  => false,
                        ),
                    ),
                ),
                'title'     => array(
                    'spec'  => array(
                        'type'      => 'Zork\Form\Element\Text',
                        'name'      => 'title',
                        'options'   => array(
                            'label'     => 'paragraph.form.content.contentTitle',
                            'required'  => false,
                        ),
                    ),
                ),
                'save' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\Submit',
                        'name'      => 'save',
                        'attributes'    => array(
                            'value'     => 'paragraph.form.content.submit',
                        ),
                    ),
                ),
            ),
        ),
        'Grid\Paragraph\Snippet\Create' => array(
            'elements' => array(
                'name' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\Text',
                        'name'      => 'name',
                        'options'   => array(
                            'label'             => 'paragraph.form.snippet.name',
                            'required'          => true,
                            'pattern'           => '[\w\.-]{3,20}',
                            'rpc_validators'    => array(
                                'Grid\Paragraph\Model\Snippet\Rpc::isNameAvailable',
                            ),
                        ),
                        'attributes'    => array(
                            'placeholder'   => 'paragraph.form.snippet.name.placeholder',
                        ),
                    ),
                ),
                'type' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\Select',
                        'name'      => 'type',
                        'options'   => array(
                            'label'     => 'paragraph.form.snippet.type',
                            'required'  => true,
                            'options'   => array(
                                'js'    => 'paragraph.snippet.type.js',
                                'css'   => 'paragraph.snippet.type.css',
                            ),
                        ),
                        'attributes'    => array(
                            'data-js-type'  => 'js.paragraph.snippet.type',
                        ),
                    ),
                ),
                'code' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\Textarea',
                        'name'      => 'code',
                        'options'   => array(
                            'label'     => 'paragraph.form.snippet.code',
                            'required'  => true,
                        ),
                        'attributes'    => array(
                            'data-js-type'              => 'js.form.element.codeEditor',
                            'data-js-codeeditor-mode'   => 'text/plain',
                        ),
                    ),
                ),
                'save' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\Submit',
                        'name'      => 'save',
                        'attributes'    => array(
                            'value'     => 'paragraph.form.snippet.submit',
                        ),
                    ),
                ),
            ),
        ),
        'Grid\Paragraph\Snippet\Upload' => array(
            'elements'      => array(
                'file'      => array(
                    'spec'  => array(
                        'type'      => 'Zork\Form\Element\File',
                        'name'      => 'file',
                        'options'   => array(
                            'required'      => true,
                            'label'         => 'paragraph.form.snippet.file',
                            'accept'        => array(
                                'text/javascipt',
                                'text/css',
                                'text/ecmascript',
                                'text/jscript',
                                'application/javascript',
                                'application/ecmascript',
                                'application/x-javascript',
                            ),
                            'validators'    => array(
                             /* 'MimeType'  => array(
                                    'name'      => 'Zend\Validator\File\MimeType',
                                    'options'   => array(
                                        'mimeType' => 'text/javascipt,text/css'
                                    ),
                                ), */
                                'Extension'     => array(
                                    'name'      => 'Zend\Validator\File\Extension',
                                    'options'   => array(
                                        'extension' => 'js,css',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
                'overwrite' => array(
                    'spec'  => array(
                        'type'      => 'Zork\Form\Element\Checkbox',
                        'name'      => 'overwrite',
                        'options'   => array(
                            'label'     => 'paragraph.form.snippet.overwrite',
                            'required'  => false,
                        ),
                    ),
                ),
                'save' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\Submit',
                        'name'      => 'save',
                        'attributes'    => array(
                            'value'     => 'paragraph.form.snippet.submit',
                        ),
                    ),
                ),
            ),
        ),
        'Grid\Paragraph\Snippet\Edit' => array(
            'elements' => array(
                'code' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\Textarea',
                        'name'      => 'code',
                        'options'   => array(
                            'label'     => 'paragraph.form.snippet.code',
                            'required'  => true,
                        ),
                        'attributes'    => array(
                            'data-js-type'              => 'js.form.element.codeEditor',
                            'data-js-codeeditor-mode'   => 'text/plain',
                        ),
                    ),
                ),
                'save' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\Submit',
                        'name'      => 'save',
                        'attributes'    => array(
                            'value'     => 'paragraph.form.snippet.submit',
                        ),
                    ),
                ),
            ),
        ),
    ),
    'translator' => array(
        'translation_file_patterns' => array(
            'paragraph' => array(
                'type'          => 'phpArray',
                'base_dir'      => __DIR__ . '/../languages/paragraph',
                'pattern'       => '%s.php',
                'text_domain'   => 'paragraph',
            ),
        ),
    ),
    'view_manager' => array(
        'mvc_strategies'    => array(
            'Grid\Paragraph\Mvc\View\Http\InjectMetaContentListener',
            'Grid\Paragraph\Mvc\View\Http\MiddleLayoutExceptionStrategy',
        ),
        'template_map'      => array(
            'grid/paragraph/change-layout/import'        => __DIR__ . '/../view/grid/paragraph/change-layout/import.phtml',
            'grid/paragraph/change-layout/local'         => __DIR__ . '/../view/grid/paragraph/change-layout/local.phtml',
            'grid/paragraph/content/edit'                => __DIR__ . '/../view/grid/paragraph/content/edit.phtml',
            'grid/paragraph/content/list'                => __DIR__ . '/../view/grid/paragraph/content/list.phtml',
            'grid/paragraph/create-wizard/cancel'        => __DIR__ . '/../view/grid/paragraph/create-wizard/cancel.phtml',
            'grid/paragraph/create-wizard/finish'        => __DIR__ . '/../view/grid/paragraph/create-wizard/finish.phtml',
            'grid/paragraph/dashboard/edit'              => __DIR__ . '/../view/grid/paragraph/dashboard/edit.phtml',
            'grid/paragraph/import-content/import'       => __DIR__ . '/../view/grid/paragraph/import-content/import.phtml',
            'grid/paragraph/layout/edit'                 => __DIR__ . '/../view/grid/paragraph/layout/edit.phtml',
            'grid/paragraph/layout/list'                 => __DIR__ . '/../view/grid/paragraph/layout/list.phtml',
            'grid/paragraph/snippet/create'              => __DIR__ . '/../view/grid/paragraph/snippet/create.phtml',
            'grid/paragraph/snippet/upload'              => __DIR__ . '/../view/grid/paragraph/snippet/upload.phtml',
            'grid/paragraph/snippet/edit'                => __DIR__ . '/../view/grid/paragraph/snippet/edit.phtml',
            'grid/paragraph/snippet/list'                => __DIR__ . '/../view/grid/paragraph/snippet/list.phtml',
            'grid/paragraph/snippet/delete'              => __DIR__ . '/../view/grid/paragraph/snippet/delete.phtml',
            'grid/paragraph/render/box'                  => __DIR__ . '/../view/grid/paragraph/render/box.phtml',
            'grid/paragraph/render/content'              => __DIR__ . '/../view/grid/paragraph/render/content.phtml',
            'grid/paragraph/render/contentPlaceholder'   => __DIR__ . '/../view/grid/paragraph/render/contentPlaceholder.phtml',
            'grid/paragraph/render/defaultFallback'      => __DIR__ . '/../view/grid/paragraph/render/defaultFallback.phtml',
            'grid/paragraph/render/language'             => __DIR__ . '/../view/grid/paragraph/render/language.phtml',
            'grid/paragraph/render/paragraph'            => __DIR__ . '/../view/grid/paragraph/render/paragraph.phtml',
            'grid/paragraph/render/lead'                 => __DIR__ . '/../view/grid/paragraph/render/lead.phtml',
            'grid/paragraph/render/infobar'              => __DIR__ . '/../view/grid/paragraph/render/infobar.phtml',
            'grid/paragraph/render/title'                => __DIR__ . '/../view/grid/paragraph/render/title.phtml',
            'grid/paragraph/render/widget'               => __DIR__ . '/../view/grid/paragraph/render/widget.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);
