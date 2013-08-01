<?php

return array(
    'router' => array(
        'routes' => array(
            'Grid\Tag\Admin\List' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/app/:locale/admin/tag/list',
                    'defaults' => array(
                        'controller' => 'Grid\Tag\Controller\Admin',
                        'action'     => 'list',
                    ),
                ),
            ),
            'Grid\Tag\Admin\Create' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/app/:locale/admin/tag/create',
                    'defaults' => array(
                        'controller' => 'Grid\Tag\Controller\Admin',
                        'action'     => 'edit',
                    ),
                ),
            ),
            'Grid\Tag\Admin\Edit' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'         => '/app/:locale/admin/tag/edit/:id',
                    'constraints'   => array(
                        'id'        => '[1-9][0-9]*',
                    ),
                    'defaults'      => array(
                        'controller'    => 'Grid\Tag\Controller\Admin',
                        'action'        => 'edit',
                    ),
                ),
            ),
            'Grid\Tag\Admin\Delete' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'         => '/app/:locale/admin/tag/delete/:id',
                    'constraints'   => array(
                        'id'        => '[1-9][0-9]*',
                    ),
                    'defaults'      => array(
                        'controller'    => 'Grid\Tag\Controller\Admin',
                        'action'        => 'delete',
                    ),
                ),
            ),
            'Grid\Tag\Search\Search' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/app/:locale/tag/search',
                    'defaults' => array(
                        'controller' => 'Grid\Tag\Controller\Search',
                        'action'     => 'search',
                    ),
                ),
            ),
            'Grid\Tag\List\List' => array(
                'type' => 'Zend\Mvc\Router\Http\Regex',
                'options' => array(
                    'regex'     => '/app/(?P<locale>[^/]+)/tag/list/(?P<mode>all|some)(/(?P<page>[1-9][0-9]*))?/(?P<tags>.+)',
                    'spec'      => '/app/%locale%/tag/list/%mode%/%page%/%tags%',
                    'defaults'  => array(
                        'controller' => 'Grid\Tag\Controller\List',
                        'action'     => 'list',
                        'page'       => '1',
                    ),
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Grid\Tag\Controller\List'   => 'Grid\Tag\Controller\ListController',
            'Grid\Tag\Controller\Admin'  => 'Grid\Tag\Controller\AdminController',
            'Grid\Tag\Controller\Search' => 'Grid\Tag\Controller\SearchController',
        ),
    ),
    'factory' => array(
        'Grid\Paragraph\Model\Paragraph\StructureFactory' => array(
            'adapter' => array(
                'contentList'   => 'Grid\Tag\Model\Paragraph\Structure\ContentList',
                'tagCloud'      => 'Grid\Tag\Model\Paragraph\Structure\TagCloud',
                'tags'          => 'Grid\Tag\Model\Paragraph\Structure\Tags',
            ),
        ),
    ),
    'modules' => array(
        'Grid\Core' => array(
            'navigation'    => array(
                'content'   => array(
                    'pages'     => array(
                        'tag'   => array(
                            'label'         => 'admin.navTop.tag',
                            'textDomain'    => 'admin',
                            'order'         => 4,
                            'route'         => 'Grid\Tag\Admin\List',
                            'parentOnly'    => true,
                            'pages'         => array(
                                'list'      =>array(
                                    'label'         => 'admin.navTop.tagList',
                                    'textDomain'    => 'admin',
                                    'order'         => 1,
                                    'route'         => 'Grid\Tag\Admin\List',
                                    'resource'      => 'tag.entry',
                                    'privilege'     => 'edit',
                                ),
                                'create'    => array(
                                    'label'         => 'admin.navTop.tagCreate',
                                    'textDomain'    => 'admin',
                                    'order'         => 2,
                                    'route'         => 'Grid\Tag\Admin\Create',
                                    'resource'      => 'tag.entry',
                                    'privilege'     => 'create',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ),
        'Grid\Paragraph' => array(
            'customizeSelectors' => array(
                'tag'   => '#paragraph-%id%.paragraph.paragraph-%type% .tag',
            ),
            'customizeMapForms' => array(
                'contentList'   => array(
                    'element'   => 'general',
                    'leadImage' => 'image',
                ),
                'tagCloud' => array(
                    'element'   => 'general',
                    'tag'       => 'general',
                ),
                'tags' => array(
                    'element'   => 'general',
                    'tag'       => 'general',
                ),
            ),
        ),
    ),
    'form' => array(
        'Grid\Paragraph\CreateWizard\Start' => array(
            'elements'  => array(
                'type'  => array(
                    'spec'  => array(
                        'options'   => array(
                            'options'   => array(
                                'basic' => array(
                                    'options'   => array(
                                        'contentList'   => 'paragraph.type.contentList',
                                        'tagCloud'      => 'paragraph.type.tagCloud',
                                        'tags'          => 'paragraph.type.tags',
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
                'contentList' => array(
                    'spec'    => array(
                        'name'      => 'contentList',
                        'options'   => array(
                            'label'     => 'paragraph.type.contentList',
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
                            'mode'  => array(
                                'spec'  => array(
                                    'type'      => 'Zork\Form\Element\Select',
                                    'name'      => 'mode',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.contentList.mode',
                                        'required'  => false,
                                        'options'   => array(
                                            'some'  => 'paragraph.form.contentList.mode.some',
                                            'all'   => 'paragraph.form.contentList.mode.all',
                                        ),
                                    ),
                                ),
                            ),
                            'itemCount' => array(
                                'spec'  => array(
                                    'type'      => 'Zork\Form\Element\Number',
                                    'name'      => 'itemCount',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.contentList.itemCount',
                                        'required'  => false,
                                    ),
                                    'attributes'    => array(
                                        'value'     => 10,
                                        'min'       => 1,
                                    ),
                                ),
                            ),
                            'displayUser' => array(
                                'spec' => array(
                                    'type'      => 'Zork\Form\Element\Checkbox',
                                    'name'      => 'displayUser',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.contentList.displayUser',
                                        'required'  => false,
                                    ),
                                    'attributes'    => array(
                                        'checked'   => true,
                                    ),
                                ),
                            ),
                            'displayCreated' => array(
                                'spec' => array(
                                    'type'      => 'Zork\Form\Element\Checkbox',
                                    'name'      => 'displayCreated',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.contentList.displayCreated',
                                        'required'  => false,
                                    ),
                                    'attributes'    => array(
                                        'checked'   => true,
                                    ),
                                ),
                            ),
                            'displayLeadImage' => array(
                                'spec' => array(
                                    'type'      => 'Zork\Form\Element\Checkbox',
                                    'name'      => 'displayLeadImage',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.contentList.displayLeadImage',
                                        'required'  => false,
                                    ),
                                    'attributes'    => array(
                                        'checked'   => true,
                                    ),
                                ),
                            ),
                            'displayLeadText' => array(
                                'spec' => array(
                                    'type'      => 'Zork\Form\Element\Checkbox',
                                    'name'      => 'displayLeadText',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.contentList.displayLeadText',
                                        'required'  => false,
                                    ),
                                    'attributes'    => array(
                                        'checked'   => true,
                                    ),
                                ),
                            ),
                            'displayReadMore' => array(
                                'spec' => array(
                                    'type'      => 'Zork\Form\Element\Checkbox',
                                    'name'      => 'displayReadMore',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.contentList.displayReadMore',
                                        'required'  => false,
                                    ),
                                    'attributes'    => array(
                                        'checked'   => true,
                                    ),
                                ),
                            ),
                            'tags' => array(
                                'spec' => array(
                                    'type'      => 'Grid\Tag\Form\Element\TagList',
                                    'name'      => 'tags',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.contentList.tags',
                                        'required'  => false,
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
                'tagCloud' => array(
                    'spec' => array(
                        'name'      => 'tagCloud',
                        'options'   => array(
                            'label'     => 'paragraph.type.tagCloud',
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
                            'mode'  => array(
                                'spec'  => array(
                                    'type'      => 'Zork\Form\Element\Select',
                                    'name'      => 'mode',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.tagCloud.mode',
                                        'required'  => true,
                                        'options'   => array(
                                            'primary'   => 'paragraph.form.tagCloud.mode.primary',
                                            'strict'    => 'paragraph.form.tagCloud.mode.strict',
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
                'tags' => array(
                    'spec' => array(
                        'name'      => 'tags',
                        'options'   => array(
                            'label'     => 'paragraph.type.tags',
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
            ),
        ),
        'Grid\Tag\Edit' => array(
            'elements'  => array(
                'id'    => array(
                    'spec'  => array(
                        'type'      => 'Zork\Form\Element\Hidden',
                        'name'      => 'id',
                        'options'   => array(
                            'required'  => false,
                        ),
                    ),
                ),
                'locale'  => array(
                    'spec'  => array(
                        'type'      => 'Zork\Form\Element\Locale',
                        'name'      => 'locale',
                        'options'   => array(
                            'label'         => 'tag.form.edit.locale',
                            'required'      => false,
                            'empty_option'  => 'tag.locale.all',
                        ),
                    ),
                ),
                'name'  => array(
                    'spec'  => array(
                        'type'      => 'Zork\Form\Element\Text',
                        'name'      => 'name',
                        'options'   => array(
                            'label'             => 'tag.form.edit.name',
                            'required'          => true,
                            'rpc_validators'    => array(
                                'Grid\Tag\Model\Tag\Rpc::isNameAvailable',
                            ),
                        ),
                    ),
                ),
                'save'  => array(
                    'spec'  => array(
                        'type'      => 'Zork\Form\Element\Submit',
                        'name'      => 'save',
                        'options'   => array(
                            'required'  => true,
                        ),
                        'attributes'    => array(
                            'value'     => 'tag.form.edit.submit',
                        ),
                    ),
                ),
            ),
        ),
    ),
    'translator' => array(
        'translation_file_patterns' => array(
            'tag' => array(
                'type'          => 'phpArray',
                'base_dir'      => __DIR__ . '/../languages/tag',
                'pattern'       => '%s.php',
                'text_domain'   => 'tag',
            ),
        ),
    ),
    'view_manager' => array(
        'template_map' => array(
            'grid/tag/contentList'               => __DIR__ . '/../view/grid/tag/contentList.phtml',
            'grid/tag/admin/list'                => __DIR__ . '/../view/grid/tag/admin/list.phtml',
            'grid/tag/admin/edit'                => __DIR__ . '/../view/grid/tag/admin/edit.phtml',
            'grid/tag/list/list'                 => __DIR__ . '/../view/grid/tag/list/list.phtml',
            'grid/paragraph/render/contentList'  => __DIR__ . '/../view/grid/paragraph/render/contentList.phtml',
            'grid/paragraph/render/tagCloud'     => __DIR__ . '/../view/grid/paragraph/render/tagCloud.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);
