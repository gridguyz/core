<?php

return array(
    'router' => array(
        'routes' => array(
            'Grid\Image\Thumbnail\Render' => array(
                'type' => 'Zend\Mvc\Router\Http\Regex',
                'options' => array(
                    'regex'     => '/thumbnails/(?P<pathname>.*)',
                    'spec'      => '/thumbnails/%pathname%',
                    'defaults'  => array(
                        'controller' => 'Grid\Image\Controller\Thumbnail',
                        'action'     => 'render',
                    ),
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Grid\Image\Controller\Thumbnail' => 'Grid\Image\Controller\ThumbnailController',
        ),
    ),
    'acl' => array(
        'resources' => array(
            'uploads'   => null,
        ),
    ),
    'factory' => array(
        'Grid\Paragraph\Model\Paragraph\StructureFactory' => array(
            'adapter' => array(
                'image' => 'Grid\Image\Model\Paragraph\Structure\Image',
            ),
        ),
    ),
    'translator' => array(
        'translation_file_patterns' => array(
            'image' => array(
                'type'          => 'phpArray',
                'base_dir'      => __DIR__ . '/../languages/image',
                'pattern'       => '%s.php',
                'text_domain'   => 'image',
            ),
        ),
    ),
    'modules' => array(
        'Grid\Paragraph' => array(
            'customizeSelectors' => array(
                'imageImg'      => '#paragraph-%id%.paragraph.paragraph-%type% img',
                'imageCaption'  => '#paragraph-%id%.paragraph.paragraph-%type% figcaption',
            ),
            'customizeMapForms' => array(
                'image' => array(
                    'element'       => 'general',
                    'imageImg'      => 'general',
                    'imageCaption'  => 'general',
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
                                        'image' => 'paragraph.type.image',
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
                'image' => array(
                    'spec' => array(
                        'name'      => 'image',
                        'options'   => array(
                            'label'     => 'paragraph.type.image',
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
                            'url'   => array(
                                'spec'  => array(
                                    'type'      => 'Zork\Form\Element\PathSelect',
                                    'name'      => 'url',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.image.url',
                                        'required'  => false,
                                    ),
                                ),
                            ),
                            'caption'   => array(
                                'spec'  => array(
                                    'type'      => 'Zork\Form\Element\Html',
                                    'name'      => 'caption',
                                    'options'   => array(
                                        'label'             => 'paragraph.form.image.caption',
                                        'required'          => false,
                                        'html_buttonset'    => 'simple',
                                    ),
                                ),
                            ),
                            'alternate' => array(
                                'spec'  => array(
                                    'type'      => 'Zork\Form\Element\Text',
                                    'name'      => 'alternate',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.image.alternate',
                                        'required'  => false,
                                    ),
                                ),
                            ),
                            'width'     => array(
                                'spec'  => array(
                                    'type'      => 'Zork\Form\Element\Number',
                                    'name'      => 'width',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.image.width',
                                        'required'  => false,
                                    ),
                                    'attributes'    => array(
                                        'min'       => 25,
                                        'max'       => 800,
                                    ),
                                ),
                            ),
                            'height'    => array(
                                'spec'  => array(
                                    'type'      => 'Zork\Form\Element\Number',
                                    'name'      => 'height',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.image.height',
                                        'required'  => false,
                                    ),
                                    'attributes'    => array(
                                        'min'       => 25,
                                        'max'       => 600,
                                    ),
                                ),
                            ),
                            'method'    => array(
                                'spec'  => array(
                                    'type'      => 'Zork\Form\Element\Select',
                                    'name'      => 'method',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.image.method',
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
                            'bgColor'   => array(
                                'spec'  => array(
                                    'type'      => 'Zork\Form\Element\Color',
                                    'name'      => 'bgColor',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.image.bgColor',
                                        'required'  => false,
                                    ),
                                ),
                            ),
                            'linkTo'    => array(
                                'spec'  => array(
                                    'type'      => 'Zork\Form\Element\Text',
                                    'name'      => 'linkTo',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.image.linkTo',
                                        'required'  => false,
                                    ),
                                ),
                            ),
                            'linkTarget' => array(
                                'spec'  => array(
                                    'type'      => 'Zork\Form\Element\Select',
                                    'name'      => 'linkTarget',
                                    'options'   => array(
                                        'label'         => 'paragraph.form.image.linkTarget',
                                        'required'      => false,
                                        'text_domain'   => 'default',
                                        'options'       => array(
                                            ''          => 'default.link.target.default',
                                            '_self'     => 'default.link.target.self',
                                            '_blank'    => 'default.link.target.blank',
                                            '_parent'   => 'default.link.target.parent',
                                            '_top'      => 'default.link.target.top',
                                        ),
                                    ),
                                ),
                            ),
                            'lightBox'  => array(
                                'spec'  => array(
                                    'type'      => 'Zork\Form\Element\Checkbox',
                                    'name'      => 'lightBox',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.image.lightBox',
                                        'required'  => false,
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_map' => array(
            'grid/paragraph/render/image' => __DIR__ . '/../view/grid/paragraph/render/image.phtml'
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);
