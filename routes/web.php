<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect('/dashboard'));

Route::view('/dashboard', 'dashboard');

Route::view('/clients', 'dashboard');   // placeholder
Route::view('/projects', 'dashboard');  // placeholder
Route::view('/users', 'dashboard');     // placeholder