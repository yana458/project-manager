<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientServiceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectServiceController;
use App\Http\Controllers\ProjectTeamController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', function () {
        $user = Auth::user();
        $role = $user->getRoleNames()->first();
        $department = $user->department ?? 'development';

        return view('dashboard', compact('role', 'department'));
    })->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Users
    |--------------------------------------------------------------------------
    */
    Route::prefix('users')->name('users.')->middleware('permission:users.view')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');

        Route::get('/create', [UserController::class, 'create'])
            ->middleware('permission:users.create')
            ->name('create');

        Route::post('/', [UserController::class, 'store'])
            ->middleware('permission:users.create')
            ->name('store');

        Route::get('/{user}', [UserController::class, 'show'])->name('show');

        Route::get('/{user}/edit', [UserController::class, 'edit'])
            ->middleware('permission:users.edit')
            ->name('edit');

        Route::patch('/{user}', [UserController::class, 'update'])
            ->middleware('permission:users.edit')
            ->name('update');

        Route::patch('/{user}/deactivate', [UserController::class, 'deactivate'])
            ->middleware('permission:users.deactivate')
            ->name('deactivate');

        Route::patch('/{user}/activate', [UserController::class, 'activate'])
            ->middleware('permission:users.deactivate')
            ->name('activate');

        Route::patch('/{user}/role', [UserController::class, 'assignRole'])
            ->middleware('permission:users.role.assign')
            ->name('role.assign');

        Route::patch('/{user}/permissions', [UserController::class, 'updatePermissions'])
            ->middleware('permission:users.permissions.assign')
            ->name('permissions.update');
    });

    /*
    |--------------------------------------------------------------------------
    | Clients
    |--------------------------------------------------------------------------
    */
    Route::prefix('clients')->name('clients.')->group(function () {
        Route::get('/', [ClientController::class, 'index'])
            ->middleware('permission:clients.view')
            ->name('index');

        Route::get('/create', [ClientController::class, 'create'])
            ->middleware('permission:clients.create')
            ->name('create');

        Route::post('/', [ClientController::class, 'store'])
            ->middleware('permission:clients.create')
            ->name('store');

        Route::get('/{client}', [ClientController::class, 'show'])
            ->middleware('permission:clients.view')
            ->name('show');

        Route::get('/{client}/edit', [ClientController::class, 'edit'])
            ->middleware('permission:clients.edit')
            ->name('edit');

        Route::put('/{client}', [ClientController::class, 'update'])
            ->middleware('permission:clients.edit')
            ->name('update');

        Route::patch('/{client}/deactivate', [ClientController::class, 'deactivate'])
            ->middleware('permission:clients.deactivate')
            ->name('deactivate');

        Route::patch('/{client}/activate', [ClientController::class, 'activate'])
            ->middleware('permission:clients.deactivate')
            ->name('activate');

        // Contracted services for a client
        Route::post('/{client}/services', [ClientServiceController::class, 'store'])
            ->name('services.store');

        Route::delete('/{client}/services/{service}', [ClientServiceController::class, 'destroy'])
            ->name('services.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Projects
    |--------------------------------------------------------------------------
    */
    Route::prefix('projects')->name('projects.')->group(function () {
        // Global project routes
        Route::get('/', [ProjectController::class, 'index'])
            ->middleware('permission:projects.view')
            ->name('index');

        Route::get('/create', [ProjectController::class, 'create'])
            ->middleware('permission:projects.create')
            ->name('create');

        Route::post('/', [ProjectController::class, 'store'])
            ->middleware('permission:projects.create')
            ->name('store');

        // Instance-based routes: authorization in controller
        Route::get('/{project}', [ProjectController::class, 'show'])->name('show');
        Route::get('/{project}/edit', [ProjectController::class, 'edit'])->name('edit');
        Route::put('/{project}', [ProjectController::class, 'update'])->name('update');

        Route::patch('/{project}/pause', [ProjectController::class, 'pause'])->name('pause');
        Route::patch('/{project}/activate', [ProjectController::class, 'activate'])->name('activate');
        Route::patch('/{project}/finish', [ProjectController::class, 'finish'])->name('finish');

        // Team
        Route::post('/{project}/team', [ProjectTeamController::class, 'store'])->name('team.store');
        Route::patch('/{project}/team/{user}', [ProjectTeamController::class, 'update'])->name('team.update');
        Route::delete('/{project}/team/{user}', [ProjectTeamController::class, 'destroy'])->name('team.destroy');

        // Project services
        Route::post('/{project}/services', [ProjectServiceController::class, 'store'])->name('services.store');
        Route::patch('/{project}/services/{projectService}', [ProjectServiceController::class, 'update'])->name('services.update');
        Route::delete('/{project}/services/{projectService}', [ProjectServiceController::class, 'destroy'])->name('services.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Service catalog
    |--------------------------------------------------------------------------
    */
    Route::prefix('services')->name('services.')->group(function () {
        Route::get('/', [ServiceController::class, 'index'])
            ->middleware('permission:services.view')
            ->name('index');

        Route::get('/{service}', [ServiceController::class, 'show'])
            ->middleware('permission:services.view')
            ->name('show');

        Route::get('/create', [ServiceController::class, 'create'])
            ->middleware('permission:services.create')
            ->name('create');

        Route::post('/', [ServiceController::class, 'store'])
            ->middleware('permission:services.create')
            ->name('store');

        Route::get('/{service}/edit', [ServiceController::class, 'edit'])
            ->middleware('permission:services.edit')
            ->name('edit');

        Route::put('/{service}', [ServiceController::class, 'update'])
            ->middleware('permission:services.edit')
            ->name('update');

        Route::delete('/{service}', [ServiceController::class, 'destroy'])
            ->middleware('permission:services.deactivate')
            ->name('destroy');

        Route::patch('/{service}/restore', [ServiceController::class, 'restore'])
            ->middleware('permission:services.deactivate')
            ->name('restore');
    });

    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';