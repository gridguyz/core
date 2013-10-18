<?php

return array(
    'router' => array(
        'routes' => array(
            'Grid\User\Authentication\Login' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/app/:locale/login',
                    'constraints'   => array(
                        'locale'    => '\w+',
                    ),
                    'defaults' => array(
                        'controller' => 'Grid\User\Controller\Authentication',
                        'action'     => 'login',
                    ),
                ),
            ),
            'Grid\User\Authentication\LoginWidth' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/app/:locale/login-with',
                    'constraints'   => array(
                        'locale'    => '\w+',
                    ),
                    'defaults' => array(
                        'controller' => 'Grid\User\Controller\Authentication',
                        'action'     => 'login-with',
                    ),
                ),
            ),
            'Grid\User\Authentication\Logout' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/app/:locale/logout[/[:immediate]]',
                    'constraints'   => array(
                        'locale'    => '\w+',
                        'immediate' => '\w+',
                    ),
                    'defaults' => array(
                        'controller' => 'Grid\User\Controller\Authentication',
                        'action'     => 'logout',
                    ),
                ),
            ),
            'Grid\User\Datasheet\View' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/app/:locale/user/view/:displayName',
                    'constraints'   => array(
                        'locale'    => '\w+',
                    ),
                    'defaults' => array(
                        'controller' => 'Grid\User\Controller\Datasheet',
                        'action'     => 'view',
                    ),
                ),
            ),
            'Grid\User\Datasheet\Edit' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/app/:locale/user/edit/:displayName',
                    'constraints'   => array(
                        'locale'    => '\w+',
                    ),
                    'defaults' => array(
                        'controller' => 'Grid\User\Controller\Datasheet',
                        'action'     => 'edit',
                    ),
                ),
            ),
            'Grid\User\Datasheet\Password' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/app/:locale/user/password/:displayName',
                    'constraints'   => array(
                        'locale'    => '\w+',
                    ),
                    'defaults' => array(
                        'controller' => 'Grid\User\Controller\Datasheet',
                        'action'     => 'password',
                    ),
                ),
            ),
            'Grid\User\Datasheet\Delete' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/app/:locale/user/delete/:displayName',
                    'constraints'   => array(
                        'locale'    => '\w+',
                    ),
                    'defaults' => array(
                        'controller' => 'Grid\User\Controller\Datasheet',
                        'action'     => 'delete',
                    ),
                ),
            ),
            'Grid\User\Manage\Register' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/app/:locale/register',
                    'constraints'   => array(
                        'locale'    => '\w+',
                    ),
                    'defaults' => array(
                        'controller' => 'Grid\User\Controller\Manage',
                        'action'     => 'register',
                    ),
                ),
            ),
            'Grid\User\Manage\RegisterSuccess' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/app/:locale/register/success',
                    'constraints'   => array(
                        'locale'    => '\w+',
                    ),
                    'defaults' => array(
                        'controller' => 'Grid\User\Controller\Manage',
                        'action'     => 'register-success',
                    ),
                ),
            ),
            'Grid\User\Manage\Confirm' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/app/:locale/confirm/:hash',
                    'constraints'   => array(
                        'locale'    => '\w+',
                        'hash'      => '\w+',
                    ),
                    'defaults' => array(
                        'controller' => 'Grid\User\Controller\Manage',
                        'action'     => 'confirm',
                    ),
                ),
            ),
            'Grid\User\PasswordChangeRequest\Create' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/app/:locale/password-change-request',
                    'constraints'   => array(
                        'locale'    => '\w+',
                    ),
                    'defaults' => array(
                        'controller' => 'Grid\User\Controller\PasswordChangeRequest',
                        'action'     => 'create',
                    ),
                ),
            ),
            'Grid\User\PasswordChangeRequest\Resolve' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/app/:locale/password-change-request/:hash',
                    'constraints'   => array(
                        'locale'    => '\w+',
                        'hash'      => '\w+',
                    ),
                    'defaults' => array(
                        'controller' => 'Grid\User\Controller\PasswordChangeRequest',
                        'action'     => 'resolve',
                    ),
                ),
            ),
            'Grid\User\Admin\Index' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/app/:locale/admin/user',
                    'constraints'   => array(
                        'locale'    => '\w+',
                    ),
                    'defaults' => array(
                        'controller' => 'Grid\User\Controller\Admin',
                        'action'     => 'index',
                    ),
                ),
            ),
            'Grid\User\Admin\Create' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/app/:locale/admin/user/create',
                    'constraints'   => array(
                        'locale'    => '\w+',
                    ),
                    'defaults' => array(
                        'controller' => 'Grid\User\Controller\Admin',
                        'action'     => 'create',
                    ),
                ),
            ),
            'Grid\User\Admin\Edit' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'         => '/app/:locale/admin/user/edit/:id',
                    'constraints'   => array(
                        'locale'    => '\w+',
                        'id'        => '[1-9][0-9]*',
                    ),
                    'defaults'      => array(
                        'controller'    => 'Grid\User\Controller\Admin',
                        'action'        => 'edit',
                    ),
                ),
            ),
            'Grid\User\Admin\Grant' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'         => '/app/:locale/admin/user/grant/:id',
                    'constraints'   => array(
                        'locale'    => '\w+',
                        'id'        => '[1-9][0-9]*',
                    ),
                    'defaults'      => array(
                        'controller'    => 'Grid\User\Controller\Admin',
                        'action'        => 'grant',
                    ),
                ),
            ),
            'Grid\User\Admin\List' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/app/:locale/admin/user/list',
                    'constraints'   => array(
                        'locale'    => '\w+',
                    ),
                    'defaults' => array(
                        'controller' => 'Grid\User\Controller\Admin',
                        'action'     => 'list',
                    ),
                ),
            ),
            'Grid\User\Admin\Export' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/app/:locale/admin/user/export',
                    'constraints'   => array(
                        'locale'    => '\w+',
                    ),
                    'defaults' => array(
                        'controller' => 'Grid\User\Controller\Admin',
                        'action'     => 'export',
                    ),
                ),
            ),
            'Grid\User\Admin\Password' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'         => '/app/:locale/admin/user/password/:id',
                    'constraints'   => array(
                        'locale'    => '\w+',
                        'id'        => '[1-9][0-9]*',
                    ),
                    'defaults'      => array(
                        'controller'    => 'Grid\User\Controller\Admin',
                        'action'        => 'password',
                    ),
                ),
            ),
            'Grid\User\Admin\Activate' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'         => '/app/:locale/admin/user/activate/:id',
                    'constraints'   => array(
                        'locale'    => '\w+',
                        'id'        => '[1-9][0-9]*',
                    ),
                    'defaults'      => array(
                        'controller'    => 'Grid\User\Controller\Admin',
                        'action'        => 'activate',
                    ),
                ),
            ),
            'Grid\User\Admin\Delete' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'         => '/app/:locale/admin/user/delete/:id',
                    'constraints'   => array(
                        'locale'    => '\w+',
                        'id'        => '[1-9][0-9]*',
                    ),
                    'defaults'      => array(
                        'controller'    => 'Grid\User\Controller\Admin',
                        'action'        => 'delete',
                    ),
                ),
            ),
            'Grid\User\Admin\Ban' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'         => '/app/:locale/admin/user/ban/:id',
                    'constraints'   => array(
                        'locale'    => '\w+',
                        'id'        => '[1-9][0-9]*',
                    ),
                    'defaults'      => array(
                        'controller'    => 'Grid\User\Controller\Admin',
                        'action'        => 'ban',
                    ),
                ),
            ),
            'Grid\User\GroupAdmin\Create' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/app/:locale/admin/user-group/create',
                    'constraints'   => array(
                        'locale'    => '\w+',
                    ),
                    'defaults' => array(
                        'controller' => 'Grid\User\Controller\GroupAdmin',
                        'action'     => 'create',
                    ),
                ),
            ),
            'Grid\User\GroupAdmin\Edit' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'         => '/app/:locale/admin/user-group/edit/:id',
                    'constraints'   => array(
                        'locale'    => '\w+',
                        'id'        => '[1-9][0-9]*',
                    ),
                    'defaults'      => array(
                        'controller'    => 'Grid\User\Controller\GroupAdmin',
                        'action'        => 'edit',
                    ),
                ),
            ),
            'Grid\User\GroupAdmin\SetDefault' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'         => '/app/:locale/admin/user-group/set-default/:id',
                    'constraints'   => array(
                        'locale'    => '\w+',
                        'id'        => '[1-9][0-9]*',
                    ),
                    'defaults'      => array(
                        'controller'    => 'Grid\User\Controller\GroupAdmin',
                        'action'        => 'set-default',
                    ),
                ),
            ),
            'Grid\User\GroupAdmin\Grant' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'         => '/app/:locale/admin/user-group/grant/:id',
                    'constraints'   => array(
                        'locale'    => '\w+',
                        'id'        => '[1-9][0-9]*',
                    ),
                    'defaults'      => array(
                        'controller'    => 'Grid\User\Controller\GroupAdmin',
                        'action'        => 'grant',
                    ),
                ),
            ),
            'Grid\User\GroupAdmin\List' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/app/:locale/admin/user-group/list',
                    'constraints'   => array(
                        'locale'    => '\w+',
                    ),
                    'defaults' => array(
                        'controller' => 'Grid\User\Controller\GroupAdmin',
                        'action'     => 'list',
                    ),
                ),
            ),
            'Grid\User\GroupAdmin\Delete' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'         => '/app/:locale/admin/user-group/delete/:id',
                    'constraints'   => array(
                        'locale'    => '\w+',
                        'id'        => '[1-9][0-9]*',
                    ),
                    'defaults'      => array(
                        'controller'    => 'Grid\User\Controller\GroupAdmin',
                        'action'        => 'delete',
                    ),
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Grid\User\Controller\Admin'                 => 'Grid\User\Controller\AdminController',
            'Grid\User\Controller\Manage'                => 'Grid\User\Controller\ManageController',
            'Grid\User\Controller\Datasheet'             => 'Grid\User\Controller\DatasheetController',
            'Grid\User\Controller\GroupAdmin'            => 'Grid\User\Controller\GroupAdminController',
            'Grid\User\Controller\Authentication'        => 'Grid\User\Controller\AuthenticationController',
            'Grid\User\Controller\PasswordChangeRequest' => 'Grid\User\Controller\PasswordChangeRequestController',
        ),
    ),
    'controller_plugins' => array(
        'invokables' => array(
            'DashboardLogoutPlugin' => 'Grid\User\Controller\Plugin\DashboardLogoutPlugin',
        ),
    ),
    'service_manager'   => array(
        'factories'     => array(
            'acl'       => 'Zork\Permissions\Acl\AclServiceFactory',
        ),
        'aliases'       => array(
            'Zend\Permissions\Acl\Acl'  => 'acl',
            'Zork\Permissions\Acl\Acl'  => 'acl',
            'permissions'               => 'Grid\User\Model\Premissions\Model',
        ),
    ),
    'acl' => array(
        'roles' => array(
            '0' => array(), // guest
            '1' => array(), // developer
            '2' => array(), // site-owner
        ),
        'resources' => array(
            'admin'                     => null,
            'settings'                  => null,
            'settings.site-definition'  => 'settings',
            'settings.mail'             => 'settings',
            'sysadmin'                  => null,
            'uploads'                   => null,
            'user'                      => null,
            'user.group'                => 'user',
            'user.group.1'              => 'user.group',
            'user.group.2'              => 'user.group',
            'paragraph'                 => null,
            'paragraph.layout'          => 'paragraph',
            'paragraph.content'         => 'paragraph',
        ),
        'allow' => array(
            'developers to do everything' => array(
                'role'      => '1',
                'resource'  => null,
                'privilege' => null,
            ),
            'site-owners to do everything' => array(
                'role'      => '2',
                'resource'  => null,
                'privilege' => null,
            ),
        ),
        'deny' => array(
            'site-owners to do anything with developers' => array(
                'role'      => '2',
                'resource'  => 'user.group.1',
                'privilege' => null,
            ),
            'site-owners to do anything with site-owners' => array(
                'role'      => '2',
                'resource'  => 'user.group.2',
                'privilege' => null,
            ),
            'site-owners to do anything with sysadmin' => array(
                'role'      => '2',
                'resource'  => 'sysadmin',
                'privilege' => null,
            ),
        ),
    ),
    'factory' => array(
        'Grid\ApplicationLog\Model\Log\StructureFactory' => array(
            'adapter'   => array(
                'user-login'    => 'Grid\User\Model\Log\Structure\UserLogin',
                'user-logout'   => 'Grid\User\Model\Log\Structure\UserLogout',
            ),
        ),
        'Grid\Paragraph\Model\Paragraph\StructureFactory' => array(
            'adapter' => array(
                'login' => 'Grid\User\Model\Paragraph\Structure\Login',
            ),
        ),
        'Grid\User\Model\Authentication\AdapterFactory' => array(
            'dependency' => array(
                'Zork\Model\ModelAwareInterface',
                'Zend\Authentication\Adapter\AdapterInterface',
            ),
            'adapter'    => array(
                'default'   => 'Grid\User\Model\Authentication\DefaultAdapter',
                'confirm'   => 'Grid\User\Model\Authentication\ConfirmAdapter',
            ),
        ),
    ),
    'form' => array(
        'Grid\User\Login' => array(
            'type'      => 'Grid\User\Form\ReturnUri',
            'elements'  => array(
                'email' => array(
                    'spec' => array(
                        'type'  => 'Zork\Form\Element\Email',
                        'name'  => 'email',
                        'options'   => array(
                            'label'     => 'user.form.login.email',
                            'required'  => true,
                        ),
                    ),
                ),
                'password' => array(
                    'spec' => array(
                        'type'  => 'Zork\Form\Element\Password',
                        'name'  => 'password',
                        'options'   => array(
                            'label'     => 'user.form.login.password',
                            'required'  => true,
                        ),
                        'attributes'    => array(
                            'autocomplete'  => 'off',
                        ),
                    ),
                ),
             /* 'csrf' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\Csrf',
                        'name'      => 'csrf',
                        'options'   => array(
                            'required'  => true,
                        ),
                    ),
                ), */
                'login' => array(
                    'spec' => array(
                        'type'  => 'Zork\Form\Element\Submit',
                        'name'  => 'login',
                        'attributes'    => array(
                            'value'     => 'user.form.login.submit',
                        ),
                    ),
                ),
            ),
        ),
        'Grid\User\Logout' => array(
            'type'      => 'Grid\User\Form\ReturnUri',
            'elements'  => array(
             /* 'csrf' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\Csrf',
                        'name'      => 'csrf',
                        'options'   => array(
                            'required'  => true,
                        ),
                    ),
                ), */
                'logout' => array(
                    'spec' => array(
                        'type'  => 'Zork\Form\Element\Submit',
                        'name'  => 'login',
                        'attributes'    => array(
                            'value'     => 'user.form.logout.submit',
                        ),
                    ),
                ),
            ),
        ),
        'Grid\User\Register' => array(
            'elements'  => array(
                'email'     => array(
                    'spec'  => array(
                        'type'      => 'Zork\Form\Element\Email',
                        'name'      => 'email',
                        'options'   => array(
                            'label'             => 'user.form.register.email',
                            'required'          => true,
                            'rpc_validators'    => array(
                                'Grid\User\Model\User\Rpc::isEmailAvailable',
                            ),
                        ),
                    ),
                ),
                'password'  => array(
                    'spec'  => array(
                        'type'      => 'Zork\Form\Element\Password',
                        'name'      => 'password',
                        'options'   => array(
                            'label'     => 'user.form.register.password',
                            'required'  => true,
                            'minlength' => 6,
                        ),
                        'attributes'    => array(
                            'autocomplete'  => 'off',
                        ),
                    ),
                ),
                'passwordVerify'    => array(
                    'spec'          => array(
                        'type'      => 'Zork\Form\Element\Password',
                        'name'      => 'passwordVerify',
                        'options'   => array(
                            'label'     => 'user.form.register.passwordVerify',
                            'required'  => true,
                            'identical' => 'password',
                        ),
                        'attributes'    => array(
                            'autocomplete'  => 'off',
                        ),
                    ),
                ),
                'displayName'   => array(
                    'spec'      => array(
                        'type'      => 'Zork\Form\Element\Text',
                        'name'      => 'displayName',
                        'options'   => array(
                            'label'             => 'user.form.register.displayName',
                            'required'          => true,
                            'rpc_validators'    => array(
                                'Grid\User\Model\User\Rpc::isDisplayNameAvailable',
                            ),
                        ),
                    ),
                ),
                'locale'    => array(
                    'spec'  => array(
                        'type'      => 'Zork\Form\Element\Locale',
                        'name'      => 'locale',
                        'options'   => array(
                            'label'     => 'user.form.register.locale',
                            'required'  => true,
                        ),
                    ),
                ),
                'captcha'   => array(
                    'spec'  => array(
                        'type'      => 'Zork\Form\Element\Captcha',
                        'name'      => 'captcha',
                        'options'   => array(
                            'label'     => 'user.form.register.captcha',
                            'required'  => true,
                        ),
                    ),
                   'flags' => array(
                        'priority' => -1000,
                    ),
                ),
                'register' => array(
                    'spec' => array(
                        'type'  => 'Zork\Form\Element\Submit',
                        'name'  => 'save',
                        'attributes'    => array(
                            'value'     => 'user.form.register.submit',
                        ),
                    ),
                    'flags' => array(
                        'priority' => -1010,
                    ),
                ),
            ),
        ),
        'Grid\User\PasswordChangeRequest\Create' => array(
            'elements'  => array(
                'email' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\Email',
                        'name'      => 'email',
                        'options'   => array(
                            'label'         => 'user.form.passwordRequest.email',
                            'description'   => 'user.form.passwordRequest.email.description',
                            'required'      => true,
                        ),
                    ),
                ),
                'request' => array(
                    'spec' => array(
                        'type'  => 'Zork\Form\Element\Submit',
                        'name'  => 'request',
                        'attributes'    => array(
                            'value'     => 'user.form.passwordRequest.submit',
                        ),
                    ),
                ),
            ),
        ),
        'Grid\User\PasswordChangeRequest\Resolve' => array(
            'elements'  => array(
                'password' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\Password',
                        'name'      => 'password',
                        'options'   => array(
                            'label'         => 'user.form.passwordChange.password',
                            'description'   => 'user.form.passwordChange.password.description',
                            'required'      => true,
                            'minlength'     => 6,
                        ),
                        'attributes'    => array(
                            'autocomplete'  => 'off',
                        ),
                    ),
                ),
                'passwordVerify' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\Password',
                        'name'      => 'passwordVerify',
                        'options'   => array(
                            'label'         => 'user.form.passwordChange.check',
                            'description'   => 'user.form.passwordChange.check.description',
                            'required'      => true,
                            'identical'     => 'password',
                        ),
                        'attributes'    => array(
                            'autocomplete'  => 'off',
                        ),
                    ),
                ),
                'change' => array(
                    'spec' => array(
                        'type'  => 'Zork\Form\Element\Submit',
                        'name'  => 'change',
                        'attributes'    => array(
                            'value'     => 'user.form.passwordChange.submit',
                        ),
                    ),
                ),
            ),
        ),
        'Grid\User\Create' => array(
            'elements'  => array(
                'email' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\Email',
                        'name'      => 'email',
                        'options'   => array(
                            'label'             => 'user.form.create.email',
                            'required'          => true,
                            'rpc_validators'    => array(
                                'Grid\User\Model\User\Rpc::isEmailAvailable',
                            ),
                        ),
                    ),
                ),
                'confirmed' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\Checkbox',
                        'name'      => 'confirmed',
                        'options'   => array(
                            'label'     => 'user.form.create.confirmed',
                            'required'  => false,
                        ),
                    ),
                ),
                'password' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\Password',
                        'name'      => 'password',
                        'options'   => array(
                            'label'     => 'user.form.create.password',
                            'required'  => true,
                            'minlength' => 6,
                        ),
                        'attributes'    => array(
                            'autocomplete'  => 'off',
                        ),
                    ),
                ),
                'passwordVerify' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\Password',
                        'name'      => 'passwordVerify',
                        'options'   => array(
                            'label'     => 'user.form.create.passwordVerify',
                            'required'  => true,
                            'identical' => 'password',
                        ),
                        'attributes'    => array(
                            'autocomplete'  => 'off',
                        ),
                    ),
                ),
                'displayName' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\Text',
                        'name'      => 'displayName',
                        'options'   => array(
                            'label'             => 'user.form.create.displayName',
                            'required'          => true,
                            'rpc_validators'    => array(
                                'Grid\User\Model\User\Rpc::isDisplayNameAvailable',
                            ),
                        ),
                    ),
                ),
                'locale' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\Locale',
                        'name'      => 'locale',
                        'options'   => array(
                            'label'     => 'user.form.create.locale',
                            'required'  => true,
                        ),
                    ),
                ),
                'groupId' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\SelectModel',
                        'name'      => 'groupId',
                        'options'   => array(
                            'label'     => 'user.form.edit.group',
                            'required'  => true,
                            'model'     => 'Grid\User\Model\Permissions\Model',
                            'method'    => 'allowedUserGroups',
                            'arguments' => array(
                                'edit',
                            ),
                        ),
                    ),
                ),
                'create' => array(
                    'spec' => array(
                        'type'  => 'Zork\Form\Element\Submit',
                        'name'  => 'create',
                        'attributes'    => array(
                            'value'     => 'user.form.create.submit',
                        ),
                    ),
                    'flags' => array(
                        'priority' => -1000,
                    ),
                ),
            ),
        ),
        'Grid\User\Edit' => array(
            'elements'  => array(
                'id' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\Hidden',
                        'name'      => 'id',
                        'options'   => array(
                            'label'     => '',
                            'required'  => true,
                        ),
                    ),
                ),
             /* 'email' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\Email',
                        'name'      => 'email',
                        'options'   => array(
                            'label'             => 'user.form.register.email',
                            'required'          => true,
                            'rpc_validators'    => array(
                                'Grid\User\Model\User\Rpc::isEmailAvailable',
                            ),
                        ),
                    ),
                ), */
                'displayName' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\Text',
                        'name'      => 'displayName',
                        'options'   => array(
                            'label'             => 'user.form.register.displayName',
                            'required'          => true,
                            'rpc_validators'    => array(
                                'Grid\User\Model\User\Rpc::isDisplayNameAvailable',
                            ),
                        ),
                    ),
                ),
                'locale' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\Locale',
                        'name'      => 'locale',
                        'options'   => array(
                            'label'     => 'user.form.register.locale',
                            'required'  => true,
                        ),
                    ),
                ),
                'avatar' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\FileUpload',
                        'name'      => 'avatar',
                        'options'   => array(
                            'label'     => 'user.form.edit.avatar',
                            'required'  => false,
                        ),
                    ),
                ),
                'groupId' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\SelectModel',
                        'name'      => 'groupId',
                        'options'   => array(
                            'label'     => 'user.form.edit.group',
                            'required'  => true,
                            'model'     => 'Grid\User\Model\Permissions\Model',
                            'method'    => 'allowedUserGroups',
                            'arguments' => array(
                                'edit',
                            ),
                        ),
                    ),
                ),
                'save' => array(
                    'spec' => array(
                        'type'  => 'Zork\Form\Element\Submit',
                        'name'  => 'save',
                        'attributes'    => array(
                            'value'     => 'user.form.edit.submit',
                        ),
                    ),
                    'flags' => array(
                        'priority' => -1000,
                    ),
                ),
            ),
        ),
        'Grid\User\Password' => array(
            'elements'  => array(
                'password' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\Password',
                        'name'      => 'password',
                        'options'   => array(
                            'label'         => 'user.form.passwordChange.password',
                            'description'   => 'user.form.passwordChange.password.description',
                            'required'      => true,
                            'minlength'     => 6,
                        ),
                        'attributes'    => array(
                            'autocomplete'  => 'off',
                        ),
                    ),
                ),
                'passwordVerify' => array(
                    'spec' => array(
                        'type'      => 'Zork\Form\Element\Password',
                        'name'      => 'passwordVerify',
                        'options'   => array(
                            'label'         => 'user.form.passwordChange.check',
                            'description'   => 'user.form.passwordChange.check.description',
                            'required'      => true,
                            'identical'     => 'password',
                        ),
                        'attributes'    => array(
                            'autocomplete'  => 'off',
                        ),
                    ),
                ),
                'change' => array(
                    'spec' => array(
                        'type'  => 'Zork\Form\Element\Submit',
                        'name'  => 'change',
                        'attributes'    => array(
                            'value'     => 'user.form.passwordChange.submit',
                        ),
                    ),
                ),
            ),
        ),
        'Grid\User\Group' => array(
            'elements'  => array(
                'name'  => array(
                    'spec'  => array(
                        'type'      => 'Zork\Form\Element\Text',
                        'name'      => 'name',
                        'options'   => array(
                            'label'     => 'user.form.group.name',
                            'required'  => true,
                        ),
                    ),
                ),
                'save'  => array(
                    'spec'  => array(
                        'type'  => 'Zork\Form\Element\Submit',
                        'name'  => 'save',
                        'attributes'    => array(
                            'value'     => 'user.form.group.submit',
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
                                        'login' => 'paragraph.type.login',
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
                'login' => array(
                    'spec' => array(
                        'name'      => 'login',
                        'options'   => array(
                            'label'     => 'paragraph.type.login',
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
                            'displayRegisterLink' => array(
                                'spec' => array(
                                    'type'      => 'Zork\Form\Element\Checkbox',
                                    'name'      => 'displayRegisterLink',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.login.displayRegisterLink',
                                        'required'  => false,
                                    ),
                                ),
                            ),
                            'displayPasswordRequestLink' => array(
                                'spec' => array(
                                    'type'      => 'Zork\Form\Element\Checkbox',
                                    'name'      => 'displayPasswordRequestLink',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.login.displayPasswordRequestLink',
                                        'required'  => false,
                                    ),
                                ),
                            ),
                            'displayLoginWithLink' => array(
                                'spec' => array(
                                    'type'      => 'Zork\Form\Element\Checkbox',
                                    'name'      => 'displayLoginWithLink',
                                    'options'   => array(
                                        'label'     => 'paragraph.form.login.displayLoginWithLink',
                                        'required'  => false,
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ),
        'Grid\Core\Settings\SiteDefinition' => array(
            'type'      => 'Grid\Core\Form\Settings',
            'elements'  => array(
                'registrationEnabled' => array(
                    'spec'  => array(
                        'type'  => 'Zork\Form\Element\Checkbox',
                        'name'  => 'registrationEnabled',
                        'options'   => array(
                            'required'  => false,
                            'label'     => 'settings.form.siteDefinition.registrationEnabled',
                        ),
                    ),
                ),
            ),
        ),
    ),
    'translator' => array(
        'translation_file_patterns' => array(
            'user' => array(
                'type'          => 'phpArray',
                'base_dir'      => __DIR__ . '/../languages/user',
                'pattern'       => '%s.php',
                'text_domain'   => 'user',
            ),
        ),
    ),
    'modules' => array(
        'Grid\Core' => array(
            'dashboardIcons' => array(
                'users' => array(
                    'order'         => 2,
                    'label'         => 'admin.dashboard.users',
                    'textDomain'    => 'admin',
                    'route'         => 'Grid\User\Admin\List',
                    'resource'      => 'user.group.any',
                    'privilege'     => 'view',
                ),
            ),
            'dashboardBoxes' => array(
                'user' => array(
                    'order'         => 1,
                    'plugin'        => 'DashboardLogoutPlugin',
                ),
            ),
            'settings' => array(
                'site-definition' => array(
                    'elements'    => array(
                        'registrationEnabled' => array(
                            'key'   => 'modules.Grid\User.features.registrationEnabled',
                            'type'  => 'ini',
                        ),
                    ),
                ),
            ),
            'navigation' => array(
                'userAndGroup'      => array(
                    'order'         => 6,
                    'label'         => 'admin.navTop.userAndGroup',
                    'textDomain'    => 'admin',
                    'uri'           => '#',
                    'parentOnly'    => true,
                    'pages'         => array(
                        'user'              => array(
                            'order'         => 1,
                            'label'         => 'admin.navTop.user',
                            'textDomain'    => 'admin',
                            'route'         => 'Grid\User\Admin\List',
                            'parentOnly'    => true,
                            'pages'         => array(
                                'list'              => array(
                                    'order'         => 1,
                                    'label'         => 'admin.navTop.userList',
                                    'textDomain'    => 'admin',
                                    'route'         => 'Grid\User\Admin\List',
                                    'resource'      => 'user.group.some',
                                    'privilege'     => 'view',
                                ),
                                'create'            => array(
                                    'order'         => 2,
                                    'label'         => 'admin.navTop.userCreate',
                                    'textDomain'    => 'admin',
                                    'route'         => 'Grid\User\Admin\Create',
                                    'resource'      => 'user.group.some',
                                    'privilege'     => 'create',
                                ),
                            ),
                        ),
                        'userGroup'         => array(
                            'order'         => 2,
                            'label'         => 'admin.navTop.userGroup',
                            'textDomain'    => 'admin',
                            'route'         => 'Grid\User\GroupAdmin\List',
                            'parentOnly'    => true,
                            'pages'         => array(
                                'list'              => array(
                                    'order'         => 1,
                                    'label'         => 'admin.navTop.userGroupList',
                                    'textDomain'    => 'admin',
                                    'route'         => 'Grid\User\GroupAdmin\List',
                                    'resource'      => 'user.group.some',
                                    'privilege'     => 'view',
                                ),
                                'create'            => array(
                                    'order'         => 2,
                                    'label'         => 'admin.navTop.userGroupCreate',
                                    'textDomain'    => 'admin',
                                    'route'         => 'Grid\User\GroupAdmin\Create',
                                    'resource'      => 'user.group',
                                    'privilege'     => 'create',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ),
        'Grid\User' => array(
            'display' => array(
                'registerLink'          => true,
                'passwordRequestLink'   => true,
                'loginWithLink'         => true,
            ),
            'features' => array(
                'registrationEnabled'   => true,
                'loginWith'             => array(),
            ),
        ),
        'Grid\Paragraph' => array(
            'customizeMapForms' => array(
                'login' => array(
                    'element' => 'general',
                ),
            ),
        ),
        'Grid\MultisiteCentral'  => array(
            'uploadsDirs'   => array(
                'users',
            ),
        ),
    ),
    'view_manager' => array(
        'template_map' => array(
            'grid/core/admin/dashboard.logout'           => __DIR__ . '/../view/grid/core/admin/dashboard.logout.phtml',
            'grid/paragraph/render/login'                => __DIR__ . '/../view/grid/paragraph/render/login.phtml',
            'grid/user/admin/create'                     => __DIR__ . '/../view/grid/user/admin/create.phtml',
            'grid/user/admin/edit'                       => __DIR__ . '/../view/grid/user/admin/edit.phtml',
            'grid/user/admin/list'                       => __DIR__ . '/../view/grid/user/admin/list.phtml',
            'grid/user/admin/password'                   => __DIR__ . '/../view/grid/user/admin/password.phtml',
            'grid/user/authentication/login'             => __DIR__ . '/../view/grid/user/authentication/login.phtml',
            'grid/user/authentication/logout'            => __DIR__ . '/../view/grid/user/authentication/logout.phtml',
            'grid/user/datasheet/edit'                   => __DIR__ . '/../view/grid/user/datasheet/edit.phtml',
            'grid/user/datasheet/password'               => __DIR__ . '/../view/grid/user/datasheet/password.phtml',
            'grid/user/datasheet/view'                   => __DIR__ . '/../view/grid/user/datasheet/view.phtml',
            'grid/user/group-admin/create'               => __DIR__ . '/../view/grid/user/group-admin/create.phtml',
            'grid/user/group-admin/edit'                 => __DIR__ . '/../view/grid/user/group-admin/edit.phtml',
            'grid/user/group-admin/list'                 => __DIR__ . '/../view/grid/user/group-admin/list.phtml',
            'grid/user/manage/register'                  => __DIR__ . '/../view/grid/user/manage/register.phtml',
            'grid/user/password-change-request/create'   => __DIR__ . '/../view/grid/user/password-change-request/create.phtml',
            'grid/user/password-change-request/resolve'  => __DIR__ . '/../view/grid/user/password-change-request/resolve.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);
