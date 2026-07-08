<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PokjaController;
use App\Http\Controllers\RegulasiController;
use App\Http\Controllers\EpItemController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;

// Auth
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard (public)
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// File Manager
Route::get("/file-manager", [App\Http\Controllers\FileController::class, "index"])->name("file.manager")->middleware("auth");
Route::get("/api/file-manager", [App\Http\Controllers\FileController::class, "getData"])->name("file.manager.data")->middleware("auth");


// Pokja (auth required, filtered by role)
Route::middleware(['auth'])->group(function () {
    Route::get('/pokja', [PokjaController::class, 'index'])->name('pokja.index');
    Route::get('/pokja/{code}', [PokjaController::class, 'show'])->name('pokja.show')->middleware('pokja.access');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');

    // Regulasi CRUD
    Route::post('/pokja/{code}/regulasi', [RegulasiController::class, 'store'])->name('regulasi.store')->middleware('pokja.access');
    Route::put('/regulasi/{id}', [RegulasiController::class, 'update'])->name('regulasi.update');
    Route::delete('/regulasi/{id}', [RegulasiController::class, 'destroy'])->name('regulasi.destroy');
    Route::post('/upload-document', [RegulasiController::class, 'upload'])->name('document.upload');
    Route::delete('/upload-document/{id}', [RegulasiController::class, 'deleteUpload'])->name('document.delete');

    // EP Items & Penilaian
    Route::get('/pokja/{code}/penilaian-ep', [EpItemController::class, 'penilaian'])->name('ep.penilaian')->middleware('pokja.access');
    Route::post('/pokja/{code}/standar', [EpItemController::class, 'storeStandar'])->name('standar.store')->middleware('pokja.access');
    Route::delete('/standar/{id}', [EpItemController::class, 'destroyStandar'])->name('standar.destroy');
    Route::get('/pokja/{code}/data-ep', [EpItemController::class, 'getDataEp'])->name('ep.data')->middleware('pokja.access');
    Route::get('/pokja/{code}/ep', [EpItemController::class, 'index'])->name('ep.index')->middleware('pokja.access');
    Route::post('/pokja/{code}/ep', [EpItemController::class, 'store'])->name('ep.store')->middleware('pokja.access');
    Route::put('/ep/{id}', [EpItemController::class, 'update'])->name('ep.update');
    Route::delete('/ep/{id}', [EpItemController::class, 'destroy'])->name('ep.destroy');
    Route::post('/pokja/{code}/ep/import', [EpItemController::class, 'import'])->name('ep.import')->middleware('pokja.access');

    // Settings (IT & Ketua Tim only)
    Route::middleware(['role:it,ketua_tim'])->group(function () {
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');

        // User management
        Route::get('/settings/users', [UserController::class, 'index'])->name('settings.users');
        Route::post('/settings/users', [UserController::class, 'store'])->name('settings.users.store');
        Route::put('/settings/users/{id}', [UserController::class, 'update'])->name('settings.users.update');
        Route::delete('/settings/users/{id}', [UserController::class, 'destroy'])->name('settings.users.destroy');
    });
});
