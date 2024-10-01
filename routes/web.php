<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AuthAdmin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;



Auth::routes();

Route::get('/', [HomeController::class, 'index'])->name('home.index');
Route::get('/shop',[ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/{products_slug}', [ShopController::class, 'products_details'])->name('shop.products.details');

Route::middleware(['auth'])->group(function(){

    Route::get('/account-dashboard', [UserController::class, 'index'])->name('user.index');

});

Route::middleware(['auth', AuthAdmin::class])->group(function(){

    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');

    Route::get('/admin/categories', [AdminController::class, 'categories'])->name('admin.categories');
    Route::get('/admin/category/add', [AdminController::class, 'category_add'])->name('admin.category.add');
    Route::post('/admin/category/store', [AdminController::class, 'category_store'])->name('admin.category.store');

    Route::get('/admin/category/{id}/edit', [AdminController::class, 'category_edit'])->name('admin.category.edit');
    Route::put('/admin/category/update', [AdminController::class, 'category_update'])->name('admin.category.update');
    Route::delete('/admin/category/{id}/delete', [AdminController::class, 'category_delete'])->name('admin.category.delete');

    Route::get('/admin/products', [AdminController::class, 'products'])->name('admin.products');
    Route::get('/admin/products/add', [AdminController::class, 'products_add'])->name('admin.products.add');
    Route::post('/admin/products/store', [AdminController::class, 'products_store'])->name('admin.products.store');
    Route::get('/admin/products/{id}/edit', [AdminController::class, 'products_edit'])->name('admin.products.edit');
    Route::put('/admin/products/update', [AdminController::class, 'products_update'])->name('admin.products.update');
    Route::delete('/admin/products/{id}/delete', [AdminController::class, 'products_delete'])->name('admin.products.delete');






});

