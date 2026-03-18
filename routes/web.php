<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

    Route::get('/services', fn () => redirect()->route('dashboard'))
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

    // Clients CRUD
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
    });

    // Projects CRUD
    Route::prefix('projects')->name('projects.')->group(function () {
        Route::get('/', [ProjectController::class, 'index'])
            ->middleware('permission:projects.view')
            ->name('index');

        Route::get('/create', [ProjectController::class, 'create'])
            ->middleware('permission:projects.create')
            ->name('create');

        Route::post('/', [ProjectController::class, 'store'])
            ->middleware('permission:projects.create')
            ->name('store');

        Route::get('/{project}', [ProjectController::class, 'show'])
            ->middleware('permission:projects.view')
            ->name('show');

        Route::get('/{project}/edit', [ProjectController::class, 'edit'])
            ->middleware('permission:projects.edit')
            ->name('edit');

        Route::put('/{project}', [ProjectController::class, 'update'])
            ->middleware('permission:projects.edit')
            ->name('update');

        Route::patch('/{project}/pause', [ProjectController::class, 'pause'])
            ->middleware('permission:projects.status.change')
            ->name('pause');

        Route::patch('/{project}/activate', [ProjectController::class, 'activate'])
            ->middleware('permission:projects.status.change')
            ->name('activate');

        Route::patch('/{project}/finish', [ProjectController::class, 'finish'])
            ->middleware('permission:projects.status.change')
            ->name('finish');
    });

    Route::resource('services', ServiceController::class)->parameters([
    'services' => 'service'
]);
Route::patch('services/{service}/restore', [ServiceController::class, 'restore'])
     ->name('services.restore');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';