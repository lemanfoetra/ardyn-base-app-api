<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BaseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Login;
use App\Http\Controllers\MasterApiController;
use App\Http\Controllers\MasterMenuController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('login', [LoginController::class, 'login']);
Route::post('register', [RegisterController::class, 'index']);

Route::middleware(['auth:sanctum', 'check.token.expiration'])->group(function () {
    Route::get('refresh_token', [LoginController::class, 'refreshToken'])->middleware("api.role:refresh_token");
});


Route::middleware(['auth:sanctum', 'check.token.expiration'])->group(function () {

    Route::prefix('base')->group(function () {
        Route::get('/menus', [BaseController::class, 'menus'])->middleware("api.role:base_menus");
        Route::get('/menu_access', [BaseController::class, 'menuAccess'])->middleware("api.role:base_menu_access");
    });

    Route::prefix('user_management')->group(function () {
        Route::get('index', [UserManagementController::class, 'index'])->middleware("api.role:user_management_index");
        Route::get('/', [UserManagementController::class, 'users'])->middleware("api.role:user_management_get_list");
        Route::get('/{user}', [UserManagementController::class, 'show'])
            ->middleware("api.role:user_management_get_saved_user")
            ->where(['user' => '[0-9]+']);

        Route::get('roles', [UserManagementController::class, 'roles'])->middleware("api.role:user_management_roles");
        Route::post('/', [UserManagementController::class, 'store'])->middleware("api.role:user_management_post");
        Route::put('/{user}', [UserManagementController::class, 'update'])->middleware("api.role:user_management_put");
        Route::delete('/{user}', [UserManagementController::class, 'delete'])->middleware("api.role:user_management_delete");
    });

    Route::prefix('role')->group(function () {
        Route::get('index', [RoleController::class, 'index'])->middleware("api.role:role_index");
        Route::get('/', [RoleController::class, 'roles'])->middleware("api.role:role_get_list");
        Route::get('/{roleId}', [RoleController::class, 'show'])
            ->middleware("api.role:role_get_saved_role")
            ->where(['roleId' => '[0-9]+']);
        Route::post('/', [RoleController::class, 'store'])->middleware("api.role:role_post");
        Route::put('/{roleId}', [RoleController::class, 'update'])->middleware("api.role:role_put");
        Route::delete('/{roleId}', [RoleController::class, 'delete'])->middleware("api.role:role_delete");

        Route::get('/menus', [RoleController::class, 'menus'])->middleware("api.role:role_all_menus");
        Route::get('/{roleId}/menus', [RoleController::class, 'roleMenus'])->middleware("api.role:role_menus")->where(['roleId' => '[0-9]+']);
        Route::post('/{roleId}/menus', [RoleController::class, 'roleMenuSubmit'])->middleware("api.role:role_menus_post")->where(['roleId' => '[0-9]+']);
        Route::delete('/{roleId}/menus/{menuId}', [RoleController::class, 'roleMenuDestroy'])
            ->middleware("api.role:role_menus_delete")
            ->where(['roleId' => '[0-9]+', 'menuId' => '[0-9]+']);
        Route::post('/{roleId}/menus/{menuId}/access', [RoleController::class, 'roleMenuAccessSubmit'])
            ->middleware("api.role:role_menus_access_post")
            ->where(['roleId' => '[0-9]+', 'menuId' => '[0-9]+']);

        Route::get('/{roleId}/menus/{menuId}/apis', [RoleController::class, 'roleMenuApis'])
            ->middleware("api.role:role_menu_apis")
            ->where(['roleId' => '[0-9]+'], ['menuId' => '[0-9]+']);
        Route::post('/{roleId}/menus/{menuId}/apis', [RoleController::class, 'roleMenuApisSubmit'])
            ->middleware("api.role:role_menu_apis_post")
            ->where(['roleId' => '[0-9]+'], ['menuId' => '[0-9]+']);
    });


    Route::prefix('master_api')->group(function () {
        Route::get('index', [MasterApiController::class, 'index'])->middleware("api.role:master_api_index");
        Route::get('/', [MasterApiController::class, 'apis'])->middleware("api.role:master_api_get_list");
        Route::get('/{api}', [MasterApiController::class, 'show'])
            ->middleware("api.role:master_api_get")
            ->where(['api' => '[0-9]+']);

        Route::get('/menus', [MasterApiController::class, 'menus'])->middleware("api.role:master_api_menus");
        Route::post('/', [MasterApiController::class, 'store'])->middleware("api.role:master_api_post");
        Route::put('/{api}', [MasterApiController::class, 'update'])->middleware("api.role:master_api_put")->where(['api' => '[0-9]+']);;
        Route::delete('/{api}', [MasterApiController::class, 'delete'])->middleware("api.role:master_api_delete")->where(['api' => '[0-9]+']);;
    });

    Route::prefix('master_menu')->group(function () {
        Route::get('index', [MasterMenuController::class, 'index'])->middleware("api.role:master_menu_index");
        Route::get('/', [MasterMenuController::class, 'menu'])->middleware("api.role:master_menu_get");
        Route::get('/{menuId}', [MasterMenuController::class, 'show'])
            ->middleware("api.role:master_menu_show")
            ->where(['menuId' => '[0-9]+']);
        Route::post('/', [MasterMenuController::class, 'store'])
            ->middleware("api.role:master_menu_post");
        Route::put('/{menu}', [MasterMenuController::class, 'update'])
            ->middleware("api.role:master_menu_put")
            ->where(['menu' => '[0-9]+']);;
        Route::delete('/{menu}', [MasterMenuController::class, 'delete'])
            ->middleware("api.role:master_menu_delete")
            ->where(['menu' => '[0-9]+']);
        Route::get('/menus', [MasterMenuController::class, 'menus'])
            ->middleware("api.role:master_menu_menus");
    });

    Route::prefix('home')->group(function () {
        Route::get('index', [HomeController::class, 'index'])->middleware("api.role:home_index");
    });
});
