<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return Auth::check() ? redirect('/dashboard') : redirect('/login');
});

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', function () {
        $user = Auth::user();
        $role = $user->getRoleNames()->first();
        $department = $user->department ?? 'development';

        return view('dashboard', compact('role', 'department'));
    })->name('dashboard');

    // Placeholders (por ahora apuntan al dashboard)
    Route::get('/clients', fn () => view('dashboard'))
        ->middleware('permission:clients.view')
        ->name('clients.index');

    Route::get('/projects', fn () => view('dashboard'))
        ->middleware('permission:projects.view')
        ->name('projects.index');

    Route::get('/services', fn () => view('dashboard'))
        ->middleware('permission:services.view')
        ->name('services.index');

    // Users CRUD
    Route::prefix('users')->name('users.')->middleware(['permission:users.view'])->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');

        Route::get('/create', [UserController::class, 'create'])
            ->middleware('permission:users.create')->name('create');
        Route::post('/', [UserController::class, 'store'])
            ->middleware('permission:users.create')->name('store');

        Route::get('/{user}', [UserController::class, 'show'])->name('show');

        Route::get('/{user}/edit', [UserController::class, 'edit'])
            ->middleware('permission:users.edit')->name('edit');
        Route::patch('/{user}', [UserController::class, 'update'])
            ->middleware('permission:users.edit')->name('update');

        Route::patch('/{user}/deactivate', [UserController::class, 'deactivate'])
            ->middleware('permission:users.deactivate')->name('deactivate');

        // activar usuario
        Route::patch('/{user}/activate', [UserController::class, 'activate'])
            ->middleware('permission:users.deactivate') // o users.edit si prefieres
            ->name('activate');

        // cambiar rol (botón dedicado)
        Route::patch('/{user}/role', [UserController::class, 'assignRole'])
            ->middleware('permission:users.role.assign')
            ->name('role.assign');
    });

    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';