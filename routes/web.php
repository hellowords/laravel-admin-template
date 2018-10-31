<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function() {
    return view('welcome');
});

Auth::routes(['verify' => true]);

Route::get('/home', 'HomeController@index')->name('home')->middleware('verified');

Route::prefix('admin')->namespace('Admin')->middleware([
    'auth',
    'verified',
])->group(function() {
    Route::get('/', 'HomeController@index')->name('admin');
    Route::view('role', 'admin.role')->name('admin.role');
    Route::get('user', 'UserController@userList')->name('admin.user');
    Route::get('user/profile', 'UserController@index')->name('admin.user.profile');
    Route::post('user', 'UserController@store')->name('admin.user.store');
    Route::delete('user/{user}', 'UserController@destory')->name('admin.user.destory');
    Route::post('user/check_pass', 'UserController@checkPass');
    Route::post('user/update_pass', 'UserController@updatePass');
    Route::put('user', 'UserController@update');
    Route::get('users', 'UserController@getUsers');
    Route::get('roles', 'RoleController@roles');
    Route::get('permissions', 'RoleController@permissions');
    Route::put('sync_permission_by_role/{role}', 'RoleController@syncPermissionsByRole');
    Route::post('role', 'RoleController@addRole');
    Route::post('permission', 'RoleController@addPermission');
    Route::post('sync_permission/{user}', 'RoleController@syncPermissionByUser');
    Route::post('sync_role/{user}', 'RoleController@syncRoleByUser');

});
