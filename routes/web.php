<?php

// use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Auth::routes();
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::post('/dropzone', [App\Http\Controllers\HomeController::class, 'index'])->name('file-upload');
Route::resource('product-variant', App\Http\Controllers\HomeController::class);
Route::resource('product', App\Http\Controllers\ProductController::class);
Route::resource('blog', App\Http\Controllers\BlogController::class);
Route::resource('blog-category', App\Http\Controllers\BlogCategoryController::class);
