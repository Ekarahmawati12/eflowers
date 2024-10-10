<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Cartcontroller;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WishlistController;
use App\Http\Middleware\AuthAdmin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;



Auth::routes();

Route::get('/', [HomeController::class, 'index'])->name('home.index');
Route::get('/shop',[ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/{products_slug}', [ShopController::class, 'products_details'])->name('shop.products.details');

Route::get('/cart',[CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [Cartcontroller::class,'addto_cart'])->name('cart.add');
Route::put('/cart/increase-quantity/{rowId}',[Cartcontroller::class,'increase_cart_quantity'])->name('cart.qty.increase');
Route::put('/cart/decrease-quantity/{rowId}',[Cartcontroller::class,'decrease_cart_quantity'])->name('cart.qty.decrease');
Route::delete('cart/remove/{rowId}', [Cartcontroller::class, 'remove_item'])->name('cart.Item.remove');
Route::delete('cart/clear', [Cartcontroller::class, 'empty_cart'])->name('cart.empty');

Route::post('/wishlist/add', [WishlistController::class, 'addto_wishlist'])->name('wishlist.add');
Route::get('/wishlist',[WishlistController::class, 'index'])->name('wishlist.index');
Route::delete('/wishlist/item/remove/{rowId}', [WishlistController::class, 'remove_item'])->name('wishlist.item.remove');
Route::delete('wishlist/clear', [WishlistController::class, 'empty_wishlist'])->name('wishlist.items.clear');
Route::post('/wishlist/moveto-cart/{rowId}', [WishlistController::class,'moveto_cart'])->name('wishlist.moveto.cart');
Route::get('/checkout',[CartController::class,'checkout'])->name('cart.checkout'); 
Route::post('/placean-order', [CartController::class,'placean_order'])->name('cart.placean.order');
Route::get('/order-confirmation', [CartController::class, 'order_confirmation'])->name('cart.order.confirmation');

Route::middleware(['auth'])->group(function(){

    Route::get('/account-dashboard', [UserController::class, 'index'])->name('user.index');
    Route::get('/account-orders', [UserController::class, 'orders'])->name('user.orders');
    Route::get('/account-order/{order_id}/details', [UserController::class, 'order_details'])->name('user.order.details');
   
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

    Route::get('/admin/orders',[AdminController::class,'orders'])->name('admin.orders');
    Route::get('/admin/order/{order_id}/details', [AdminController::class,'order_details'])->name('admi.order.details');




});

