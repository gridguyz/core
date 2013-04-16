<?php

return array(
    'router' => array(
        'routes' => array(
            'Grid\Core\Index\Index' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'     => '/',
                    'defaults'  => array(
                        'controller' => 'Grid\Core\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
            ),
            'Grid\Core\Favicon\Ico' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'     => '/favicon.ico',
                    'defaults'  => array(
                        'controller' => 'Grid\Core\Controller\Favicon',
                        'action'     => 'ico',
                    ),
                ),
            ),
            'Grid\Core\Index\ContentUri' => array(
                'type' => 'Zend\Mvc\Router\Http\Regex',
                'options' => array(
                    'regex'     => '/(?P<uri>(?!(app|images|scripts|styles|thumbnails|tmp|uploads)\/)(?!favicon\.ico|sitemap\.xml|robots\.txt).+(?!index\.php))',
                    'spec'      => '/%uri%',
                    'defaults'  => array(
                        'controller' => 'Grid\Core\Controller\Index',
                        'action'     => 'content-uri',
                    ),
                ),
            ),
            'Grid\Core\Rpc\Call' => array(
                'type' => 'Zend\Mvc\Router\Http\Regex',
                'options' => array(
                    'regex'     => '/app/(?P<locale>[^/]+)/rpc\.(?P<format>.+)',
                    'spec'      => '/app/%locale%/rpc.%format%',
                    'defaults'  => array(
                        'controller' => 'Grid\Core\Controller\Rpc',
                        'action'     => 'call',
                        'format'     => 'json',
                    ),
                ),
            ),
            'Grid\Core\Admin\Index' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/app/:locale/admin',
                    'defaults' => array(
                        'controller' => 'Grid\Core\Controller\Admin',
                        'action'     => 'index',
                    ),
                ),
            ),
            'Grid\Core\Admin\NotAllowed' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/app/:locale/admin/not-allowed',
                    'defaults' => array(
                        'controller' => 'Grid\Core\Controller\Admin',
                        'action'     => 'not-allowed',
                    ),
                ),
            ),
            'Grid\Core\Admin\Dashboard' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/app/:locale/admin/dashboard',
                    'defaults' => array(
                        'controller' => 'Grid\Core\Controller\Admin',
                        'action'     => 'dashboard',
                    ),
                ),
            ),
            'Grid\Core\Admin\System\Module' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/app/:locale/admin/system/modules',
                    'defaults' => array(
                        'controller' => 'Grid\Core\Controller\Module',
                        'action'     => 'index',
                    ),
                ),
            ),
            'Grid\Core\Upload\Index' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'     => '/app/:locale/upload',
                    'defaults'  => array(
                        'controller' => 'Grid\Core\Controller\Upload',
                        'action'     => 'index',
                    ),
                ),
            ),
            'Grid\Core\Upload\Parts' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'     => '/app/:locale/upload-parts',
                    'defaults'  => array(
                        'controller' => 'Grid\Core\Controller\Upload',
                        'action'     => 'parts',
                    ),
                ),
            ),
            'Grid\Core\Settings\Index' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'     => '/app/:locale/admin/settings/:section',
                    'defaults'  => array(
                        'controller' => 'Grid\Core\Controller\Settings',
                        'action'     => 'index',
                    ),
                ),
            ),
            'Grid\Core\Uri\Create' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/app/:locale/admin/uri/create',
                    'defaults' => array(
                        'controller' => 'Grid\Core\Controller\Uri',
                        'action'     => 'edit',
                    ),
                ),
            ),
            'Grid\Core\Uri\Edit' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'         => '/app/:locale/admin/uri/edit/:id',
                    'constraints'   => array(
                        'id'        => '[1-9][0-9]*',
                    ),
                    'defaults'      => array(
                        'controller'    => 'Grid\Core\Controller\Uri',
                        'action'        => 'edit',
                    ),
                ),
            ),
            'Grid\Core\Uri\List' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/app/:locale/admin/uri/list',
                    'defaults' => array(
                        'controller' => 'Grid\Core\Controller\Uri',
                        'action'     => 'list',
                    ),
                ),
            ),
            'Grid\Core\Uri\Delete' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'         => '/app/:locale/admin/uri/delete/:id',
                    'constraints'   => array(
                        'id'        => '[1-9][0-9]*',
                    ),
                    'defaults'      => array(
                        'controller'    => 'Grid\Core\Controller\Uri',
                        'action'        => 'delete',
                    ),
                ),
            ),
            'Grid\Core\Uri\SetDefault' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'         => '/app/:locale/admin/uri/set-default/:id',
                    'constraints'   => array(
                        'id'        => '[1-9][0-9]*',
                    ),
                    'defaults'      => array(
                        'controller'    => 'Grid\Core\Controller\Uri',
                        'action'        => 'set-default',
                    ),
                ),
            ),
            'Grid\Core\SubDomain\Create' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/app/:locale/admin/sub-domain/create',
                    'defaults' => array(
                        'controller' => 'Grid\Core\Controller\SubDomain',
                        'action'     => 'edit',
                    ),
                ),
            ),
            'Grid\Core\SubDomain\Edit' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'         => '/app/:locale/admin/sub-domain/edit/:id',
                    'constraints'   => array(
                        'id'        => '[1-9][0-9]*',
                    ),
                    'defaults'      => array(
                        'controller'    => 'Grid\Core\Controller\SubDomain',
                        'action'        => 'edit',
                    ),
                ),
            ),
            'Grid\Core\SubDomain\List' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/app/:locale/admin/sub-domain/list',
                    'defaults' => array(
                        'controller' => 'Grid\Core\Controller\SubDomain',
                        'action'     => 'list',
                    ),
                ),
            ),
            'Grid\Core\SubDomain\Delete' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'         => '/app/:locale/admin/sub-domain/delete/:id',
                    'constraints'   => array(
                        'id'        => '[1-9][0-9]*',
                    ),
                    'defaults'      => array(
                        'controller'    => 'Grid\Core\Controller\SubDomain',
                        'action'        => 'delete',
                    ),
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Grid\Core\Controller\Index'     => 'Grid\Core\Controller\IndexController',
            'Grid\Core\Controller\Favicon'   => 'Grid\Core\Controller\FaviconController',
            'Grid\Core\Controller\Rpc'       => 'Grid\Core\Controller\RpcController',
            'Grid\Core\Controller\Admin'     => 'Grid\Core\Controller\AdminController',
            'Grid\Core\Controller\Upload'    => 'Grid\Core\Controller\UploadController',
            'Grid\Core\Controller\Settings'  => 'Grid\Core\Controller\SettingsController',
            'Grid\Core\Controller\Uri'       => 'Grid\Core\Controller\UriController',
            'Grid\Core\Controller\SubDomain' => 'Grid\Core\Controller\SubDomainController',
            'Grid\Core\Controller\Module'    => 'Grid\Core\Controller\ModuleController',
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'cacheManager'                              => 'Zork\Cache\CacheServiceFactory',
            'factoryBuilder'                            => 'Zork\Factory\BuilderServiceFactory',
            'form'                                      => 'Zork\Form\FormServiceFactory',
            'locale'                                    => 'Zork\I18n\Locale\LocaleServiceFactory',
            'logger'                                    => 'Zork\Log\LoggerServiceFactory',
            'timezone'                                  => 'Zork\I18n\Timezone\TimezoneServiceFactory',
            'translator'                                => 'Zork\I18n\Translator\TranslatorServiceFactory',
            'navigation'                                => 'Zend\Navigation\Service\DefaultNavigationFactory',
            'adminLocale'                               => 'Zork\Mvc\AdminLocaleServiceFactory',
            'Zend\Http\Client'                          => 'Zork\Http\Client\ServiceFactory',
            'Zend\Http\Client\Adapter\AdapterInterface' => 'Zork\Http\Client\Adapter\ServiceFactory',
            'Grid\Core\Model\Settings\Definitions'           => 'Grid\Core\Model\Settings\DefinitionServiceFactory',
            'Zork\Mvc\View\Http\ForbiddenStrategy'      => 'Zork\Mvc\View\Http\ForbiddenStrategyServiceFactory',
        ),
        'aliases' => array(
            'Zork\Cache\CacheManager'           => 'cacheManager',
            'Zork\Factory\Builder'              => 'factoryBuilder',
            'Zork\I18n\Locale\Locale'           => 'locale',
            'Zork\I18n\Timezone\Timezone'       => 'timezone',
            'Zend\I18n\Translator\Translator'   => 'translator',
            'Zork\I18n\Translator\Translator'   => 'translator',
            'Zork\Mvc\AdminLocale'              => 'adminLocale',
            'Zend\Http\Client\Adapter'          => 'Zend\Http\Client\Adapter\AdapterInterface',
            'Zork\Log\LoggerManager'            => 'logger',
        ),
    ),
    'cache' => array(
        'storage' => array(
            'adapter' => APPLICATION_ENV == 'development' ?
                array(
                    'name'      => 'filesystem',
                    'options'   => array(
                        'ttl'       => 7200,
                        'cache_dir' => 'data/cache',
                    ),
                ) :
                array(
                    'name'      => 'apc',
                    'options'   => array(
                        'ttl'   => 7200,
                    ),
                ),
            'plugins' => array(
                'exception_handler' => array(
                    'throw_exceptions' => false,
                ),
            ),
        ),
    ),
    'translator' => array(
        'translation_file_patterns' => array(
            'admin' => array(
                'type'          => 'phpArray',
                'base_dir'      => __DIR__ . '/../languages/admin',
                'pattern'       => '%s.php',
                'text_domain'   => 'admin',
            ),
            'default' => array(
                'type'          => 'phpArray',
                'base_dir'      => __DIR__ . '/../languages/default',
                'pattern'       => '%s.php',
                'text_domain'   => 'default',
            ),
            'css' => array(
                'type'          => 'phpArray',
                'base_dir'      => __DIR__ . '/../languages/css',
                'pattern'       => '%s.php',
                'text_domain'   => 'css',
            ),
            'locale' => array(
                'type'          => 'phpArray',
                'base_dir'      => __DIR__ . '/../languages/locale',
                'pattern'       => '%s.php',
                'text_domain'   => 'locale',
            ),
            'mime' => array(
                'type'          => 'phpArray',
                'base_dir'      => __DIR__ . '/../languages/mime',
                'pattern'       => '%s.php',
                'text_domain'   => 'mime',
            ),
            'pathselect' => array(
                'type'          => 'phpArray',
                'base_dir'      => __DIR__ . '/../languages/pathselect',
                'pattern'       => '%s.php',
                'text_domain'   => 'pathselect',
            ),
            'settings' => array(
                'type'          => 'phpArray',
                'base_dir'      => __DIR__ . '/../languages/settings',
                'pattern'       => '%s.php',
                'text_domain'   => 'settings',
            ),
            'subDomain' => array(
                'type'          => 'phpArray',
                'base_dir'      => __DIR__ . '/../languages/subDomain',
                'pattern'       => '%s.php',
                'text_domain'   => 'subDomain',
            ),
            'system' => array(
                'type'          => 'phpArray',
                'base_dir'      => __DIR__ . '/../languages/system',
                'pattern'       => '%s.php',
                'text_domain'   => 'system',
            ),
            'uri' => array(
                'type'          => 'phpArray',
                'base_dir'      => __DIR__ . '/../languages/uri',
                'pattern'       => '%s.php',
                'text_domain'   => 'uri',
            ),
            'validate' => array(
                'type'          => 'phpArray',
                'base_dir'      => __DIR__ . '/../languages/validate',
                'pattern'       => '%s.php',
                'text_domain'   => 'validate',
            ),
        ),
    ),
    'factory' => array(
        'Grid\Core\Model\Settings\StructureFactory' => array(
            'dependency' => 'Grid\Core\Model\Settings\StructureAbstract',
            'adapter'    => array(
                'default-fallback'  => 'Grid\Core\Model\Settings\Structure\DefaultFallback',
                'site-definition'   => 'Grid\Core\Model\Settings\Structure\SiteDefinition',
                'locale'            => 'Grid\Core\Model\Settings\Structure\Locale',
            ),
        ),
    ),
    'form' => array(
        'Grid\Core\AdminLocale' => array(
            'attributes'  => array(
                'action'         => '?',
                'data-js-type'   => 'js.admin.locale',
                'method'         => 'GET',
            ),
            'elements'  => array(
                'adminLocale' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\Locale',
                        'name'      => 'adminLocale',
                        'options'   => array(
                            'required'  => true,
                        ),
                    ),
                ),
            ),
        ),
        'Grid\Core\Module' => array(
            'elements'  => array(
                'modules' => array(
                    'spec' => array(
                        'type'  => 'Zork\Form\Element\MultiCheckbox',
                        'name'  => 'modules',
                    ),
                ),
                'submit' => array(
                    'spec' => array(
                        'type'  => 'Zork\Form\Element\Submit',
                        'name'  => 'submit',
                        'attributes'  => array(
                            'value' => 'default.form.module.submit',
                        ),
                    ),
                ),
            ),
        ),
        'Grid\Core\Settings\SiteDefinition' => array(
            'type'      => 'Grid\Core\Form\Settings',
            'elements'  => array(
                'headTitle' => array(
                    'spec'  => array(
                        'type'  => 'Zork\Form\Element\Text',
                        'name'  => 'headTitle',
                        'options'   => array(
                            'required'  => true,
                            'label'     => 'settings.form.siteDefinition.headTitle',
                        ),
                    ),
                ),
                'titleSeparator' => array(
                    'spec'      => array(
                        'type'  => 'Zork\Form\Element\Select',
                        'name'  => 'titleSeparator',
                        'options'   => array(
                            'required'      => true,
                            'label'         => 'settings.form.siteDefinition.titleSeparator',
                            'options'       => include 'config/separators.php',
                            'translatable'  => false,
                        ),
                    ),
                ),
                'timeZone'  => array(
                    'spec'  => array(
                        'type'  => 'Zork\Form\Element\TimeZone',
                        'name'  => 'timeZone',
                        'options'   => array(
                            'required'  => true,
                            'label'     => 'settings.form.siteDefinition.timeZone',
                        ),
                        'attributes'    => array(
                            'data-js-type'              => 'js.form.element.datalist',
                            'data-js-datalist-toggle'   => 'true',
                        ),
                    ),
                ),
                'keywords'  => array(
                    'spec'  => array(
                        'type'  => 'Zork\Form\Element\Textarea',
                        'name'  => 'keywords',
                        'options'   => array(
                            'label' => 'settings.form.siteDefinition.keywords',
                        ),
                    ),
                ),
                'description'   => array(
                    'spec'      => array(
                        'type'  => 'Zork\Form\Element\Textarea',
                        'name'  => 'description',
                        'options'   => array(
                            'label' => 'settings.form.siteDefinition.description',
                        ),
                    ),
                ),
                'favicon'   => array(
                    'spec'  => array(
                        'type'  => 'Zork\Form\Element\FileUpload',
                        'name'  => 'favicon',
                        'options'   => array(
                            'label' => 'settings.form.siteDefinition.favicon',
                            'types' => 'image/*',
                        ),
                    ),
                ),
                'logo'      => array(
                    'spec'  => array(
                        'type'  => 'Zork\Form\Element\FileUpload',
                        'name'  => 'logo',
                        'options'   => array(
                            'label'         => 'settings.form.siteDefinition.logo',
                            'description'   => 'settings.form.siteDefinition.logo.description',
                            'types'         => 'image/*',
                        ),
                    ),
                ),
            ),
        ),
        'Grid\Core\Settings\Locale' => array(
            'type'      => 'Grid\Core\Form\Settings',
            'elements'  => array(
                'default' => array(
                    'spec'  => array(
                        'type'  => 'Zork\Form\Element\Locale',
                        'name'  => 'default',
                        'options'   => array(
                            'required'      => true,
                            'label'         => 'locale.default',
                            'only_enabled'  => false,
                            'text_domain'   => 'locale',
                        ),
                    ),
                ),
                'enabled' => array(
                    'spec'  => array(
                        'type'  => 'Zork\Form\Element\Locales',
                        'name'  => 'enabled',
                        'options'   => array(
                            'label'         => 'locale.enabled',
                            'only_enabled'  => false,
                            'text_domain'   => 'locale',
                        ),
                    ),
                ),
            ),
        ),
        'Grid\Core\Uri' => array(
            'elements'  => array(
                'subdomainId' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\SelectModel',
                        'name'      => 'subdomainId',
                        'options'   => array(
                            'label'     => 'uri.form.subdomain',
                            'required'  => true,
                            'model'     => 'Grid\Core\Model\SubDomain\Model',
                            'method'    => 'findOptions',
                        ),
                    ),
                ),
                'contentId' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\SelectModel',
                        'name'      => 'contentId',
                        'options'   => array(
                            'label'     => 'uri.form.content',
                            'required'  => true,
                            'model'     => 'Grid\Paragraph\Model\Paragraph\Model',
                            'method'    => 'findOptions',
                            'arguments' => array(
                                'content',
                            ),
                        ),
                        'attributes'    => array(
                            'data-js-type'  => 'js.paragraph.contentSelect',
                        ),
                    ),
                ),
                'locale' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\Locale',
                        'name'      => 'locale',
                        'options'   => array(
                            'label'     => 'uri.form.locale',
                            'required'  => true,
                        ),
                    ),
                ),
                'uri' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\Text',
                        'name'      => 'uri',
                        'options'   => array(
                            'label'     => 'uri.form.uri',
                            'required'  => true,
                            'pattern'   => '((?!(app|images|scripts|styles|thumbnails|tmp|uploads)\/)(?!favicon\.ico|sitemap\.xml|robots\.txt).+(?!index\.php))',
                            'maxlength' => 64,
                        ),
                    ),
                ),
                'save' => array(
                    'spec' => array(
                        'type'  => 'Zork\Form\Element\Submit',
                        'name'  => 'save',
                        'attributes'  => array(
                            'value' => 'uri.form.submit',
                        ),
                    ),
                ),
            ),
        ),
        'Grid\Core\SubDomain' => array(
            'elements'  => array(
                'id' => array(
                    'spec'  => array(
                        'type'  => 'Zork\Form\Element\Hidden',
                        'name'  => 'id',
                    ),
                ),
                'subdomain' => array(
                    'spec'  => array(
                        'type'      => 'Zork\Form\Element\Text',
                        'name'      => 'subdomain',
                        'options'   => array(
                            'label'             => 'subDomain.form.subdomain',
                            'required'          => true,
                            'pattern'           => '(xn--)?[A-Za-z\d]+(-[A-Za-z\d]+)*',
                            'maxlength'         => 16,
                            'rpc_validators'    => array(
                                'Grid\Core\Model\SubDomain\Rpc::isSubdomainAvailable',
                            ),
                        ),
                    ),
                ),
                'locale' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\Locale',
                        'name'      => 'locale',
                        'options'   => array(
                            'label'     => 'subDomain.form.locale',
                            'required'  => true,
                        ),
                    ),
                ),
                'defaultLayoutId' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\SelectModel',
                        'name'      => 'defaultLayoutId',
                        'options'   => array(
                            'label'     => 'subDomain.form.defaultLayout',
                            'required'  => true,
                            'model'     => 'Grid\Paragraph\Model\Paragraph\Model',
                            'method'    => 'findOptions',
                            'arguments' => array(
                                'layout',
                            ),
                        ),
                    ),
                ),
                'defaultContentId' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\SelectModel',
                        'name'      => 'defaultContentId',
                        'options'   => array(
                            'label'     => 'subDomain.form.defaultContent',
                            'required'  => true,
                            'model'     => 'Grid\Paragraph\Model\Paragraph\Model',
                            'method'    => 'findOptions',
                            'arguments' => array(
                                'content',
                            ),
                        ),
                        'attributes'    => array(
                            'data-js-type'  => 'js.paragraph.contentSelect',
                        ),
                    ),
                ),
                'save' => array(
                    'spec' => array(
                        'type'  => 'Zork\Form\Element\Submit',
                        'name'  => 'save',
                        'attributes'    => array(
                            'value'     => 'subDomain.form.submit',
                        ),
                    ),
                ),
            ),
        ),
        'Grid\Core\ListExport'   => array(
            'fieldsets'     => array(
                'csv'       => array(
                    'spec'  => array(
                        'name'      => 'csv',
                        'options'   => array(
                            'label'     => 'default.rowSet.export.type.csv',
                            'required'  => false,
                        ),
                        'elements'  => array(
                            'sendHeaders'   => array(
                                'spec'      => array(
                                    'type'      => 'Zork\Form\Element\Checkbox',
                                    'name'      => 'sendHeaders',
                                    'options'   => array(
                                        'label'     => 'default.rowSet.export.sendHeaders',
                                        'required'  => false,
                                    ),
                                    'attributes'    => array(
                                        'checked'   => true,
                                    ),
                                ),
                            ),
                            'separator' => array(
                                'spec'  => array(
                                    'type'      => 'Zork\Form\Element\Select',
                                    'name'      => 'separator',
                                    'options'   => array(
                                        'label'     => 'default.rowSet.export.type.csv.separator',
                                        'required'  => false,
                                        'options'   => array(
                                            'comma'     => 'default.rowSet.export.type.csv.separator.comma',
                                            'semicolon' => 'default.rowSet.export.type.csv.separator.semicolon',
                                            'tab'       => 'default.rowSet.export.type.csv.separator.tab',
                                        ),
                                    ),
                                ),
                            ),
                            'eol'       => array(
                                'spec'  => array(
                                    'type'      => 'Zork\Form\Element\Select',
                                    'name'      => 'eol',
                                    'options'   => array(
                                        'label'     => 'default.rowSet.export.type.csv.eol',
                                        'required'  => false,
                                        'options'   => array(
                                            'windows'   => 'default.rowSet.export.type.csv.eol.windows',
                                            'linux'     => 'default.rowSet.export.type.csv.eol.linux',
                                            'macos'     => 'default.rowSet.export.type.csv.eol.macos',
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                    'flags' => array(
                        'priority'  => 2,
                    ),
                ),
                'xlsx'      => array(
                    'spec'  => array(
                        'name'      => 'xlsx',
                        'options'   => array(
                            'label'     => 'default.rowSet.export.type.xlsx',
                            'required'  => false,
                        ),
                        'elements'  => array(
                            'sendHeaders'   => array(
                                'spec'      => array(
                                    'type'      => 'Zork\Form\Element\Checkbox',
                                    'name'      => 'sendHeaders',
                                    'options'   => array(
                                        'label'     => 'default.rowSet.export.sendHeaders',
                                        'required'  => false,
                                    ),
                                    'attributes'    => array(
                                        'checked'   => true,
                                    ),
                                ),
                            ),
                            'creator' => array(
                                'spec'  => array(
                                    'type'      => 'Zork\Form\Element\Text',
                                    'name'      => 'creator',
                                    'options'   => array(
                                        'label'     => 'default.rowSet.export.type.xlsx.creator',
                                        'required'  => false,
                                    ),
                                ),
                            ),
                        ),
                    ),
                    'flags' => array(
                        'priority'  => 2,
                    ),
                ),
            ),
            'elements'      => array(
                'type'      => array(
                    'spec'  => array(
                        'type'      => 'Zork\Form\Element\Select',
                        'name'      => 'type',
                        'options'   => array(
                            'label'     => 'default.rowSet.export.type',
                            'required'  => true,
                            'options'   => array(
                                'csv'   => 'default.rowSet.export.type.csv',
                                'xlsx'  => 'default.rowSet.export.type.xlsx',
                            ),
                        ),
                        'attributes'    => array(
                            'data-js-type' => 'js.form.fieldsetChooser',
                        ),
                    ),
                    'flags' => array(
                        'priority'  => 3,
                    ),
                ),
                'export' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\Submit',
                        'name'      => 'export',
                        'attributes'    => array(
                            'value'     => 'default.rowSet.export.submit',
                        ),
                    ),
                    'flags' => array(
                        'priority'  => 1,
                    ),
                ),
            ),
        ),
    ),
    'modules' => array(
        'Grid\Core' => array(
            'dashboardIcons' => array(
                'settings' => array(
                    'order'         => 1,
                    'label'         => 'admin.dashboard.settings',
                    'textDomain'    => 'admin',
                    'route'         => 'Grid\Core\Settings\Index',
                    'params'        => array(
                        'section'   => 'site-definition',
                    ),
                    'resource'      => 'settings.site-definition',
                    'privilege'     => 'edit',
                ),
                'subdomain' => array(
                    'order'         => 5,
                    'label'         => 'admin.navTop.subDomain',
                    'textDomain'    => 'admin',
                    'route'         => 'Grid\Core\SubDomain\List',
                    'resource'      => 'subDomain',
                    'privilege'     => 'view',
                ),
            ),
            'navigation' => array(
                'backToPage' => array(
                    'order'         => 1,
                    'label'         => 'admin.navTop.backToPage',
                    'textDomain'    => 'admin',
                    'uri'           => '/',
                ),
                'dashboard' => array(
                    'order'         => 2,
                    'label'         => 'admin.navTop.dashboard',
                    'textDomain'    => 'admin',
                    'route'         => 'Grid\Core\Admin\Dashboard',
                ),
                'settings' => array(
                    'order'         => 3,
                    'label'         => 'admin.navTop.settings',
                    'textDomain'    => 'admin',
                    'uri'           => '#',
                    'parentOnly'    => true,
                    'pages'         => array(
                        'siteDefinition'    => array(
                            'label'         => 'admin.navTop.settings.siteDefinition',
                            'textDomain'    => 'admin',
                            'order'         => 1,
                            'route'         => 'Grid\Core\Settings\Index',
                            'resource'      => 'settings.site-definition',
                            'privilege'     => 'edit',
                            'params'        => array(
                                'section'   => 'site-definition',
                            ),
                        ),
                        'locale'    => array(
                            'label'         => 'admin.navTop.settings.locale',
                            'textDomain'    => 'admin',
                            'order'         => 2,
                            'route'         => 'Grid\Core\Settings\Index',
                            'resource'      => 'settings.locale',
                            'privilege'     => 'edit',
                            'params'        => array(
                                'section'   => 'locale',
                            ),
                        ),
                        'sub-domain' => array(
                            'order'         => 5,
                            'label'         => 'admin.navTop.subDomain',
                            'textDomain'    => 'admin',
                            'route'         => 'Grid\Core\SubDomain\List',
                            'parentOnly'    => true,
                            'pages'         => array(
                                'list'      => array(
                                    'order'         => 1,
                                    'label'         => 'admin.navTop.subDomainList',
                                    'textDomain'    => 'admin',
                                    'route'         => 'Grid\Core\SubDomain\List',
                                    'resource'      => 'subDomain',
                                    'privilege'     => 'view',
                                ),
                                'create'    => array(
                                    'order'         => 2,
                                    'label'         => 'admin.navTop.subDomainCreate',
                                    'textDomain'    => 'admin',
                                    'route'         => 'Grid\Core\SubDomain\Create',
                                    'resource'      => 'subDomain',
                                    'privilege'     => 'create',
                                ),
                            ),
                        ),
                    ),
                ),
                'content' => array(
                    'order'         => 4,
                    'label'         => 'admin.navTop.content',
                    'textDomain'    => 'admin',
                    'uri'           => '#',
                    'parentOnly'    => true,
                    'pages'         => array(
                        'uri'       => array(
                            'order'         => 3,
                            'label'         => 'admin.navTop.uri',
                            'textDomain'    => 'admin',
                            'uri'           => '#',
                            'parentOnly'    => true,
                            'pages'         => array(
                                'list'      => array(
                                    'order'         => 1,
                                    'label'         => 'admin.navTop.uriList',
                                    'textDomain'    => 'admin',
                                    'route'         => 'Grid\Core\Uri\List',
                                    'resource'      => 'uri',
                                    'privilege'     => 'view',
                                ),
                                'create'    => array(
                                    'order'         => 2,
                                    'label'         => 'admin.navTop.uriCreate',
                                    'textDomain'    => 'admin',
                                    'route'         => 'Grid\Core\Uri\Create',
                                    'resource'      => 'uri',
                                    'privilege'     => 'create',
                                ),
                            ),
                        ),
                    ),
                ),
                'system' => array(
                    'label'         => 'admin.navTop.system',
                    'textDomain'    => 'admin',
                    'uri'           => '#',
                    'order'         => 999,
                    'parentOnly'    => true,
                    'pages'         => array(
                        'modules'       => array(
                            'label'         => 'admin.navTop.system.modules',
                            'textDomain'    => 'admin',
                            'route'         => 'Grid\Core\Admin\System\Module',
                            'order'         => 1,
                            'resource'      => 'sysadmin.modules',
                            'privilege'     => 'edit',
                        ),
                    ),
                ),
            ),
            // Configuration of choosable modules
            'modules' => array(
                'Grid\FacebookLogin'          => 'default.form.module.modules.facebookLogin',
                'Grid\GoogleAnalytics'        => 'default.form.module.modules.googleAnalytics',
                'Grid\GoogleSiteVerification' => 'default.form.module.modules.googleSiteVerification',
                'Grid\GoogleMap'              => 'default.form.module.modules.googleMap',
                'Grid\Vote'                   => 'default.form.module.modules.vote',
                'Grid\OpenId'                 => 'default.form.module.modules.openId',
                'Grid\DomainManager'          => 'default.form.module.modules.domainManager',
                'Grid\MultisiteCentral'       => 'default.form.module.modules.multisiteCentral',
            ),
            'settings' => array(
                'site-definition' => array(
                    'textDomain'  => 'settings',
                    'elements'    => array(
                        'headTitle' => array(
                            'key'   => 'view_manager.head_defaults.headTitle.content',
                            'type'  => 'ini',
                        ),
                        'titleSeparator' => array(
                            'key'   => 'view_manager.head_defaults.headTitle.separator',
                            'type'  => 'ini',
                        ),
                        'keywords'  => array(
                            'key'   => 'view_manager.head_defaults.headMeta.keywords.content',
                            'type'  => 'ini',
                        ),
                        'description' => array(
                            'key'   => 'view_manager.head_defaults.headMeta.description.content',
                            'type'  => 'ini',
                        ),
                        'favicon'   => array(
                            'key'   => 'view_manager.head_defaults.headLink.favicon.href',
                            'type'  => 'ini',
                        ),
                        'faviconType' => array(
                            'key'   => 'view_manager.head_defaults.headLink.favicon.type',
                            'type'  => 'ini',
                        ),
                        'logo'      => array(
                            'key'   => 'view_manager.head_defaults.headLink.logo.href',
                            'type'  => 'ini',
                        ),
                        'logoType'  => array(
                            'key'   => 'view_manager.head_defaults.headLink.logo.type',
                            'type'  => 'ini',
                        ),
                        'timeZone'  => array(
                            'key'   => 'timezone.id',
                            'type'  => 'ini',
                        ),
                    ),
                ),
                'locale' => array(
                    'textDomain'  => 'locale',
                    'elements'    => array(
                        'default' => array(
                            'key'   => 'locale.default',
                            'type'  => 'ini',
                        ),
                        'enabled' => array(
                            'key'   => 'locale.available',
                            'type'  => 'ini',
                        ),
                    ),
                ),
            ),
        ),
    ),
    'timezone'  => array(
        'id'    => 'UTC',
    ),
    'locale'    => include 'config/languages.php',
    'http'      => array(
        'adapter'   => 'Zend\Http\Client\Adapter\Curl',
        'options'   => array(
            'curloptions' => array(
                ( defined( 'CURLOPT_SSL_VERIFYPEER' ) ? CURLOPT_SSL_VERIFYPEER : 64 ) => false,
             // ( defined( 'CURLOPT_SSL_VERIFYHOST' ) ? CURLOPT_SSL_VERIFYHOST : 81 ) => 0,
            ),
        ),
    ),
    'log'       => array(
        'application'   => array(
            'writers'   => array(
                'null'      => array(
                    'name'  => 'null',
                ),
            ),
        ),
    ),
    'controller_plugins' => array(
        'invokables'    => array(
            'layout'    => 'Zork\Mvc\Controller\Plugin\Layout',
            'locale'    => 'Zork\Mvc\Controller\Plugin\Locale',
            'messenger' => 'Zork\Mvc\Controller\Plugin\Messenger',
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason'  => true,
        'display_exceptions'        => APPLICATION_ENV == 'production' ? false : true,
        'doctype'                   => 'XHTML5',
        'forbidden_template'        => 'error/403',
        'not_found_template'        => 'error/404',
        'exception_template'        => 'error/index',
        'layout'                    => 'layout/layout',
        'strategies'                => array(
            'ViewJsonStrategy',
        ),
        'mvc_strategies'            => array(
            'Zork\Mvc\View\Http\ForbiddenStrategy',
        ),
        'template_map'              => array(
            'grid/core/admin/dashboard'     => __DIR__ . '/../view/grid/core/admin/dashboard.phtml',
            'grid/core/admin/not-allowed'   => __DIR__ . '/../view/grid/core/admin/not-allowed.phtml',
            'grid/core/module/index'        => __DIR__ . '/../view/grid/core/module/index.phtml',
            'grid/core/settings/index'      => __DIR__ . '/../view/grid/core/settings/index.phtml',
            'grid/core/sub-domain/edit'     => __DIR__ . '/../view/grid/core/sub-domain/edit.phtml',
            'grid/core/sub-domain/list'     => __DIR__ . '/../view/grid/core/sub-domain/list.phtml',
            'grid/core/upload/index'        => __DIR__ . '/../view/grid/core/upload/index.phtml',
            'grid/core/uri/edit'            => __DIR__ . '/../view/grid/core/uri/edit.phtml',
            'grid/core/uri/list'            => __DIR__ . '/../view/grid/core/uri/list.phtml',
            'grid/core/wizard/index'        => __DIR__ . '/../view/grid/core/wizard/index.phtml',
            'error/403'                     => __DIR__ . '/../view/error/403.phtml',
            'error/404'                     => __DIR__ . '/../view/error/404.phtml',
            'error/index'                   => __DIR__ . '/../view/error/index.phtml',
            'layout/layout'                 => __DIR__ . '/../view/layout/layout.phtml',
            'layout/middle/admin'           => __DIR__ . '/../view/layout/middle/admin.phtml',
            'layout/middle/center'          => __DIR__ . '/../view/layout/middle/center.phtml',
            'paginator/default'             => __DIR__ . '/../view/paginator/default.phtml',
            'paginator/uriPattern'          => __DIR__ . '/../view/paginator/uriPattern.phtml',
            'rowSet/layout'                 => __DIR__ . '/../view/rowSet/layout.phtml',
            'rowSet/layout/ajax'            => __DIR__ . '/../view/rowSet/layout/ajax.phtml',
            'rowSet/layout/basic'           => __DIR__ . '/../view/rowSet/layout/basic.phtml',
            'rowSet/layout/filtering'       => __DIR__ . '/../view/rowSet/layout/filtering.phtml',
        ),
        'template_path_stack'       => array(
            __DIR__ . '/../view',
        ),
        'head_defaults'         => array(
            'headTitle'         => array(
                'content'           => 'Zork',
                'separator'         => '-',
                'autoEscape'        => false,
                'translatorEnabled' => false,
            ),
            'headMeta'          => array(
                'viewport'      => array(
                    'name'      => 'viewport',
                    'content'   => 'width=device-width, initial-scale=1.0, maximum-scale=1.0',
                ),
                'keywords'      => array(
                    'name'      => 'keywords',
                    'content'   => '',
                ),
                'description'   => array(
                    'name'      => 'description',
                    'content'   => '',
                ),
            ),
            'headLink'          => array(
                'jqueryUi'      => array(
                    'rel'       => 'stylesheet',
                    'type'      => 'text/css',
                    'href'      => '/styles/ui/custom' . ( APPLICATION_ENV != 'development' ? '.min' : '' ) . '.css',
                                // 'http://code.jquery.com/ui/1.9.0/themes/base/jquery-ui.css',
                ),
                'defaults'      => array(
                    'rel'       => 'stylesheet',
                    'type'      => 'text/css',
                    'href'      => '/styles/defaults.css',
                ),
                'favicon'       => array(
                    'rel'       => 'shortcut icon',
                    'type'      => 'image/x-icon',
                    'href'      => '/uploads/_central/settings/favicon.ico',
                ),
                'logo'          => array(
                    'rel'       => array( 'image_src', 'apple-touch-icon' ),
                    'type'      => 'image/png',
                    'href'      => '',
                ),
            ),
            'headScript'        => array(
                'jquery'        => array(
                    /// TODO: use jquery-ui version 1.9 & 2.0, when available from google
                    'src'       => 'http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery' . ( APPLICATION_ENV != 'development' ? '.min' : '' ) . '.js',
                    'type'      => 'text/javascript',
                ),
                'jqueryUi'      => array(
                    /// TODO: use jquery-ui version 1.10, when available from google
                    'src'       => 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.1/jquery-ui' . ( APPLICATION_ENV != 'development' ? '.min' : '' ) . '.js',
                    'type'      => 'text/javascript',
                ),
                'coreJs'        => array(
                    'src'       => '/scripts/zork/core.js',
                    'type'      => 'text/javascript',
                ),
            ),
        ),
    ),
);
