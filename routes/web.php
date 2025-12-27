<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

// Named login route for Filament
Route::get('/login', function () {
    return redirect('/admin/login');
})->name('login');
