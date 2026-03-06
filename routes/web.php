<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return Auth::check() ? redirect('/dashboard') : redirect('/login');
});

Route::middleware(['auth'])->group(function () {

    // Dashboard (por rol + department)
    Route::get('/dashboard', function () {
        $user = Auth::user();

        $role = $user->getRoleNames()->first();           // admin/senior/junior/intern
        $department = $user->department ?? 'development'; // development/marketing/design

        return view('dashboard', compact('role', 'department'));
    })->name('dashboard');

    // Placeholders para el navbar (por ahora apuntan al dashboard)

    Route::get('/clients', fn () => view('dashboard'))->name('clients.index');
    Route::get('/projects', fn () => view('dashboard'))->name('projects.index');
    Route::get('/users', fn () => view('dashboard'))->name('users.index');
    Route::get('/services', fn () => view('dashboard'))->name('services.index'); 

    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';