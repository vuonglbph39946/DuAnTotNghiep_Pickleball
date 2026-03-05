<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PermissionController;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('contacts', ContactController::class);

// Admin panel (no auth enforced here)
Route::get('admin/contacts', [AdminController::class, 'index'])->name('admin.contacts.index');
Route::post('admin/contacts', [AdminController::class, 'store'])->name('admin.contacts.store');
Route::put('admin/contacts/{contact}', [AdminController::class, 'update'])->name('admin.contacts.update');
Route::delete('admin/contacts/{contact}', [AdminController::class, 'destroy'])->name('admin.contacts.destroy');

// Admin news module
Route::get('admin/news', [NewsController::class, 'index'])->name('admin.news.index');
Route::get('admin/news/create', [NewsController::class, 'create'])->name('admin.news.create');
Route::post('admin/news', [NewsController::class, 'store'])->name('admin.news.store');
Route::get('admin/news/{news}', [NewsController::class, 'show'])->name('admin.news.show');
Route::get('admin/news/{news}/edit', [NewsController::class, 'edit'])->name('admin.news.edit');
Route::put('admin/news/{news}', [NewsController::class, 'update'])->name('admin.news.update');
Route::patch('admin/news/{news}/status', [NewsController::class, 'updateStatus'])->name('admin.news.updateStatus');
Route::delete('admin/news/{news}', [NewsController::class, 'destroy'])->name('admin.news.destroy');

// Admin categories module
Route::get('admin/categories', [CategoryController::class, 'index'])->name('admin.categories.index');
Route::get('admin/categories/create', [CategoryController::class, 'create'])->name('admin.categories.create');
Route::post('admin/categories', [CategoryController::class, 'store'])->name('admin.categories.store');
// Unified form route with mode parameter (create/edit/view)
Route::get('admin/categories/form/{category}', [CategoryController::class, 'form'])->name('admin.categories.form');
Route::get('admin/categories/{category}', [CategoryController::class, 'show'])->name('admin.categories.show');
Route::get('admin/categories/{category}/edit', [CategoryController::class, 'edit'])->name('admin.categories.edit');
Route::put('admin/categories/{category}', [CategoryController::class, 'update'])->name('admin.categories.update');
Route::delete('admin/categories/{category}', [CategoryController::class, 'destroy'])->name('admin.categories.destroy');

// Admin users module
Route::get('admin/users', [UserController::class, 'index'])->name('admin.users.index');
Route::get('admin/users/create', [UserController::class, 'form'])->name('admin.users.create');
Route::post('admin/users', [UserController::class, 'store'])->name('admin.users.store');
Route::get('admin/users/form/{user}', [UserController::class, 'form'])->name('admin.users.form');
Route::get('admin/users/{user}', [UserController::class, 'form'])->name('admin.users.show');
Route::get('admin/users/{user}/edit', [UserController::class, 'form'])->name('admin.users.edit');
Route::get('admin/users/{user}/view', [UserController::class, 'form'])->name('admin.users.view');
Route::put('admin/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
Route::delete('admin/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');

// Admin permissions module
Route::get('admin/permissions', [PermissionController::class, 'index'])->name('admin.permissions.index');
Route::get('admin/permissions/roles', [PermissionController::class, 'roles'])->name('admin.permissions.roles');
Route::get('admin/permissions/permissions', [PermissionController::class, 'permissions'])->name('admin.permissions.permissions');
Route::get('admin/permissions/roles/{role}', [PermissionController::class, 'showRole'])->name('admin.permissions.roles.show');
Route::post('admin/permissions/roles', [PermissionController::class, 'storeRole'])->name('admin.permissions.roles.store');
Route::put('admin/permissions/roles/{role}', [PermissionController::class, 'updateRole'])->name('admin.permissions.roles.update');
Route::patch('admin/permissions/roles/{role}/toggle-permission', [PermissionController::class, 'toggleRolePermission'])->name('admin.permissions.roles.toggle-permission');
Route::delete('admin/permissions/roles/{role}', [PermissionController::class, 'destroyRole'])->name('admin.permissions.roles.destroy');
Route::post('admin/permissions/permissions', [PermissionController::class, 'storePermission'])->name('admin.permissions.permissions.store');
Route::put('admin/permissions/permissions/{permission}', [PermissionController::class, 'updatePermission'])->name('admin.permissions.permissions.update');
Route::delete('admin/permissions/permissions/{permission}', [PermissionController::class, 'destroyPermission'])->name('admin.permissions.permissions.destroy');
Route::put('admin/permissions/users/{user}', [PermissionController::class, 'updateUserPermissions'])->name('admin.permissions.users.update');