<?php

use Inertia\Inertia;
use App\Models\Product;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/guest', function () {
    return Inertia::render('Guest');
})->name('guest');


Route::get('/home', [HomeController::class, 'index'])->middleware(['auth', 'verified'])->name('home');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::get('/cart/add/{id}', [CartController::class, 'addToCart'])->name('cart.add');
Route::get('/cart/remove/{id}', [CartController::class, 'removeFromCart'])->name('cart.remove');


Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified', 'admin'])->name('dashboard');


Route::prefix('/category')->controller(CategoryController::class)
    ->middleware(['auth', 'verified', 'admin'])
    ->group(function () {
        Route::get('',  'index')->name('category.index');
        Route::post('store', 'store')->name('category.store');
        Route::put('update/{id}', 'update')->name('category.update');
        Route::delete('delete/{id}', 'delete')->name('category.delete');
    });


Route::prefix('/brand')->controller(BrandController::class)
    ->middleware(['auth', 'verified', 'admin'])
    ->group(function () {
        Route::get('',  'index')->name('brand.index');
        Route::post('store', 'store')->name('brand.store');
        Route::put('update/{id}', 'update')->name('brand.update');
        Route::delete('delete/{id}', 'delete')->name('brand.delete');
    });



Route::middleware(['auth', 'verified', 'admin'])->group(function () {


    Route::put('/user/deactivate/{id}', [AdminController::class, 'deactivate'])->name('user.deactivate');
    Route::delete('/user/delete/{id}', [AdminController::class, 'delete'])->name('user.delete');

    Route::get('/user', [ProfileController::class, 'index'])->name('user.index');

    Route::get('/product', [ProductController::class, 'index'])->name('product.index');
    Route::get('/product/{id}', [ProductController::class, 'getProduct']);
    Route::post('/product/store', [ProductController::class, 'store'])->name('product.store');
    Route::post('/product/update/{id}', [ProductController::class, 'update'])->name('product.update');
    Route::delete('/product/delete/{id}', [ProductController::class, 'delete'])->name('product.delete');

    // Route::get('/brand', [BrandController::class, 'index'])->name('brand.index');
    // Route::post('/brand/store', [BrandController::class, 'store'])->name('brand.store');
    // Route::put('/brand/update.{id}', [BrandController::class, 'update'])->name('brand.update');
    // Route::delete('/brand/delete/{id}', [BrandController::class, 'delete'])->name('brand.delete');

    // Route::get('/category', [CategoryController::class, 'index'])->name('category.index');
    // Route::post('/category/store', [CategoryController::class, 'store'])->name('category.store');
    // Route::put('/category/update/{id}', [CategoryController::class, 'update'])->name('category.update');
    // Route::delete('/category/delete/{id}', [CategoryController::class, 'delete'])->name('category.delete');



    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
