<?php

return array(
    'router' => array(
        'routes' => array(
            'Grid\Mail\Template\Index' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/app/:locale/admin/mail',
                    'constraints'   => array(
                        'locale'    => '\w+',
                    ),
                    'defaults' => array(
                        'controller' => 'Grid\Mail\Controller\Template',
                        'action'     => 'index',
                    ),
                ),
            ),
            'Grid\Mail\Template\Edit' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/app/:locale/admin/mail/edit/:templateName',
                    'constraints'   => array(
                        'locale'        => '\w+',
                        'templateName'  => '[\w\.-]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Grid\Mail\Controller\Template',
                        'action'     => 'edit',
                    ),
                ),
            ),
            'Grid\Mail\Template\List' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/app/:locale/admin/mail/list',
                    'constraints'   => array(
                        'locale'    => '\w+',
                    ),
                    'defaults' => array(
                        'controller' => 'Grid\Mail\Controller\Template',
                        'action'     => 'list',
                    ),
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Grid\Mail\Controller\Template' => 'Grid\Mail\Controller\TemplateController'
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'mail'          => 'Zork\Mail\ServiceFactory',
            'mailTransport' => 'Zork\Mail\Transport\ServiceFactory',
        ),
        'aliases' => array(
            'Zork\Mail\Service'                         => 'mail',
            'Zend\Mail\Transport\TransportInterface'    => 'mailTransport',
        ),
    ),
    'factory' => array(
        'Grid\Core\Model\Settings\StructureFactory' => array(
            'adapter'   => array(
                'mail'  => 'Grid\Mail\Model\Settings\Structure\Mail',
            ),
        ),
    ),
    'modules' => array(
        'Grid\Core' => array(
            'dashboardIcons' => array(
                'mail'  => array(
                    'order'         => 7,
                    'label'         => 'admin.navTop.mailTemplateList',
                    'textDomain'    => 'admin',
                    'route'         => 'Grid\Mail\Template\List',
                    'resource'      => 'mail.template',
                    'privilege'     => 'view',
                ),
            ),
            'navigation' => array(
                'settings' => array(
                    'pages'     => array(
                        'mail'  => array(
                            'label'         => 'admin.navTop.settings.mail',
                            'textDomain'    => 'admin',
                            'order'         => 3,
                            'route'         => 'Grid\Core\Settings\Index',
                            'resource'      => 'settings.mail',
                            'privilege'     => 'edit',
                            'params'        => array(
                                'section'   => 'mail',
                            ),
                        ),
                        'mailTemplateList'  => array(
                            'order'         => 4,
                            'label'         => 'admin.navTop.mailTemplateList',
                            'textDomain'    => 'admin',
                            'route'         => 'Grid\Mail\Template\List',
                            'resource'      => 'mail.template',
                            'privilege'     => 'view',
                        ),
                    ),
                ),
            ),
            'settings' => array(
                'mail' => array(
                    'textDomain'  => 'settings',
                    'elements'    => array(
                        'defaultFromName'       => array(
                            'key'   => 'mail.defaultFrom.name',
                            'type'  => 'ini',
                        ),
                        'defaultFromEmail'      => array(
                            'key'   => 'mail.defaultFrom.email',
                            'type'  => 'ini',
                        ),
                        'defaultReplyToName'    => array(
                            'key'   => 'mail.defaultReplyTo.name',
                            'type'  => 'ini',
                        ),
                        'defaultReplyToEmail'   => array(
                            'key'   => 'mail.defaultReplyTo.email',
                            'type'  => 'ini',
                        ),
                    ),
                ),
            ),
        ),
    ),
    'form' => array(
        'Grid\Core\Settings\Mail' => array(
            'type'      => 'Grid\Core\Form\Settings',
            'elements'  => array(
                'defaultFromName' => array(
                    'spec'  => array(
                        'type'  => 'Zork\Form\Element\Text',
                        'name'  => 'defaultFromName',
                        'options'   => array(
                            'required'  => false,
                            'label'     => 'settings.form.mail.defaultFromName',
                        ),
                    ),
                ),
                'defaultFromEmail' => array(
                    'spec'  => array(
                        'type'  => 'Zork\Form\Element\Email',
                        'name'  => 'defaultFromEmail',
                        'options'   => array(
                            'required'  => false,
                            'label'     => 'settings.form.mail.defaultFromEmail',
                        ),
                    ),
                ),
                'defaultReplyToName' => array(
                    'spec'  => array(
                        'type'  => 'Zork\Form\Element\Text',
                        'name'  => 'defaultReplyToName',
                        'options'   => array(
                            'required'  => false,
                            'label'     => 'settings.form.mail.defaultReplyToName',
                        ),
                    ),
                ),
                'defaultReplyToEmail' => array(
                    'spec'  => array(
                        'type'  => 'Zork\Form\Element\Email',
                        'name'  => 'defaultReplyToEmail',
                        'options'   => array(
                            'required'  => false,
                            'label'     => 'settings.form.mail.defaultReplyToEmail',
                        ),
                    ),
                ),
            ),
        ),
        'Grid\Mail\Template' => array(
            'elements'  => array(
             /* 'name' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\Hidden',
                        'name'      => 'name',
                        'options'   => array(
                            'required'  => true,
                        ),
                    ),
                ), */
                'fromAddress' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\Email',
                        'name'      => 'fromAddress',
                        'options'   => array(
                            'label' => 'mail.form.template.fromAddress',
                        ),
                    ),
                ),
                'fromName' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\Text',
                        'name'      => 'fromName',
                        'options'   => array(
                            'label' => 'mail.form.template.fromName',
                        ),
                    ),
                ),
                'subject' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\Text',
                        'name'      => 'subject',
                        'options'   => array(
                            'label'     => 'mail.form.template.subject',
                            'required'  => true,
                        ),
                    ),
                ),
                'bodyHtml' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\Html',
                        'name'      => 'bodyHtml',
                        'options'   => array(
                            'label'     => 'mail.form.template.bodyHtml',
                            'required'  => true,
                        ),
                    ),
                ),
                'bodyText' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\Textarea',
                        'name'      => 'bodyText',
                        'options'   => array(
                            'label' => 'mail.form.template.bodyText',
                        ),
                    ),
                ),
                'save' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\Submit',
                        'name'      => 'save',
                        'attributes'    => array(
                            'value'     => 'mail.form.template.submit',
                        ),
                    ),
                ),
            ),
        ),
    ),
    'mail' => array(
        'transport' => array(
            'type'      => 'Zend\Mail\Transport\Sendmail',
        ),
        'defaultFrom'   => array(
            'name'      => null,
            'email'     => null,
        ),
        'defaultReplyTo'    => array(
            'name'          => null,
            'email'         => null,
        ),
    ),
    'translator' => array(
        'translation_file_patterns' => array(
            'mail' => array(
                'type'          => 'phpArray',
                'base_dir'      => __DIR__ . '/../languages/mail',
                'pattern'       => '%s.php',
                'text_domain'   => 'mail',
            ),
        ),
    ),
    'view_manager' => array(
        'template_map' => array(
            'grid/mail/template/edit'        => __DIR__ . '/../view/grid/mail/template/edit.phtml',
            'grid/mail/template/list'        => __DIR__ . '/../view/grid/mail/template/list.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);
