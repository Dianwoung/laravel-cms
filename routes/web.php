<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

Route::get('/home', 'HomeController@index');
//用户权限管理系统路由组
Route::group(['prefix' => 'admin','namespace' => 'Admin','middleware' => ['auth']],function ($router)
{
	$router->get('/dash','DashboardController@index')->name('system.index');
	$router->get('/i18n', 'DashboardController@dataTableI18n');
	// 权限
	require(__DIR__ . '/admin/permission.php');
	// 角色
	require(__DIR__ . '/admin/role.php');
	// 用户
	require(__DIR__ . '/admin/user.php');
	// 菜单
	require(__DIR__ . '/admin/menu.php');


});

//拓展路由组

Route::group(['middleware' => 'auth'], function() {
    Route::resource('card', 'CardController');
    Route::get('card/{id}', 'CardController@show');
    Route::post('upload', 'UploadfileController@uploadOSS');


//注册extra组件
    $dir = __DIR__.'/extra/';
    $file = scandir($dir);
    foreach ($file as $filename) {
        if ($filename != '.' && $filename != '..') {
            require_once($dir . $filename);
        }
    }



  // require(__DIR__ . '/extra/qasystem.php');


});



// 后台系统日志
Route::group(['prefix' => 'admin/log','middleware' => ['auth','check.permission:log']],function ($router)
{
	$router->get('/','\Arcanedev\LogViewer\Http\Controllers\LogViewerController@index')->name('log.dash');
	$router->get('list','\Arcanedev\LogViewer\Http\Controllers\LogViewerController@listLogs')->name('log.index');
	$router->post('delete','\Arcanedev\LogViewer\Http\Controllers\LogViewerController@delete')->name('log.destroy');
	$router->get('/{date}','\Arcanedev\LogViewer\Http\Controllers\LogViewerController@show')->name('log.show');
	$router->get('/{date}/download','\Arcanedev\LogViewer\Http\Controllers\LogViewerController@download')->name('log.download');
	$router->get('/{date}/{level}','\Arcanedev\LogViewer\Http\Controllers\LogViewerController@showByLevel')->name('log.filter');

});


//
//spl_autoload_register(function($file){
//    $file = $class . '.php';
//    if (is_file($file)) {
//        require_once($file);
//    }
//});