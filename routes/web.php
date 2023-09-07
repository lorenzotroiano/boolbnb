<?php

// Percorso dei controller
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\Apartment\ApartmentController;
use Illuminate\Support\Facades\Route;


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';


// Rotta guest intro
Route::get('/', [GuestController::class, 'index'])
    ->name('home');

// Rotta Guest Show
Route::get('/show/{id}', [GuestController::class, 'show'])
    ->name('show');


// Rotte auth
Route::middleware('auth')->group(function () {


    // Rotta Auth CREATE
    Route::get('/create', [ApartmentController::class, 'create'])
        ->name('create');

    // Rotta per salvare un nuovo progetto nel database
    Route::post('/store', [ApartmentController::class, 'store'])
        ->name('store');

    // Rotte per la update
    Route::get('/edit/{id}', [ApartmentController::class, 'edit'])
        ->name('edit');

    Route::put('/update/{id}', [ApartmentController::class, 'update'])
        ->name('update');
});
