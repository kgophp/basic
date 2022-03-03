<?php
use Illuminate\Routing\Router;

Route::group([
    'prefix' => config('admin.route.prefix'),
    'namespace' => 'YK\\Basic\\Controllers',
    'middleware' => config('admin.route.middleware'),
], function (Router $router) {
    $router->resource('role', 'RoleController', ['only' =>
        ['index', 'show', 'store', 'update', 'destroy']
    ]);

    $router->get('role/{id}/permissions', 'RoleController@permissions')->name('role.permissions');

    $router->put('role/{id}/permissions', 'RoleController@assignPermissions')->name('role.assign-permissions');

    $router->get('guard-name-roles/{guardName}', 'RoleController@guardNameRoles')->name('public.role.guard-name-roles');

    $router->resource('permission', 'PermissionController', ['only' =>
        ['index', 'show', 'store', 'update', 'destroy']
    ]);

    $router->get('admin-user/clearcache', 'AdminUserController@clearCache')->name('public.admin-user.clearcache');
    $router->resource('admin-user', 'AdminUserController', ['only' =>
        ['index', 'show', 'store', 'update', 'destroy']
    ]);

    $router->get('admin-user/{id}/roles/{provider}', 'AdminUserController@roles')->name('admin-user.roles');

    $router->put('admin-user/{id}/roles/{provider}', 'AdminUserController@assignRoles')->name('admin-user.assign-roles');

    $router->resource('permission-group', 'PermissionGroupController', ['only' =>
        ['index', 'show', 'store', 'update', 'destroy']
    ]);

    $router->get('guard-name-for-permissions/{guardName}', 'PermissionGroupController@guardNameForPermissions')
        ->name('public.permission-group.guard-name-for-permission');

    $router->resource('menu', 'MenuController', ['only' =>
        ['index', 'show', 'store', 'update', 'destroy']
    ]);

    $router->resource('dictionary', 'AdminDictionaryController', ['only' =>
        ['index', 'store', 'update', 'destroy']
    ]);
    $router->get('dictionary/getTypeList', 'AdminDictionaryController@getTypeList')->name('public.dict.getTypeList');

    $router->resource('param', 'AdminParamController', ['only' =>
        ['index', 'store', 'update', 'destroy']
    ]);
    $router->post('param/batch/destroy', 'AdminParamController@batchDestroy')->name('param.batchdestroy');


});

Route::group([
    'prefix' => config('admin.route.prefix'),
    'namespace' => 'YK\\Basic\\Controllers',
    'middleware' => config('admin.route.multi_middleware'),
], function (Router $router) {
    $router->get('permission-user-all', 'PermissionController@allUserPermission')->name('public.permission.all-user-permission');
    $router->get('my-menu', 'MenuController@my')->name('public.menu.my');
    $router->patch('user-change-password', 'ChangePasswordController@changePassword')->name('public.user.change-password');
});

Route::group([
    'prefix'        => '',
    'namespace'     =>  'YK\\Basic\\Controllers',
    'middleware'    => ['web'],
], function (Router $router) {
    $router->post('oauth/login', 'LoginController@login');
    $router->post('oauth/logout','LoginController@logout');
});

