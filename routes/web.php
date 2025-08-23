<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Login;
use App\Livewire\Home;
use App\Livewire\ProductSearch;
use App\Livewire\ProductEdit;

Route::get('/login', Login::class)->name('login');
Route::get('/', Home::class)->name('home')->middleware('auth.token');
Route::get('/search', ProductSearch::class)->name('product.search')->middleware('auth.token');
Route::get('/product/{id}/edit', ProductEdit::class)->name('product.edit')->middleware('auth.token');
