<?php

// Percorso dei controller
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\Apartment\ApartmentController;
use App\Models\Apartment;
use Illuminate\Support\Facades\Route;




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

// Rotta per inviare un messaggio
Route::post('/send-message/{id}', [ApartmentController::class, 'sendMessage'])
    ->name('send.message');



// Rotte auth
Route::middleware('auth')->group(function () {


    Route::get('/dashboard', [ApartmentController::class, 'dashboard'], function () {
        return view('dashboard');
    })->name('dashboard');

    // Rotta Auth CREATE
    Route::get('/create', [ApartmentController::class, 'create'])
        ->name('create');

    // Rotta AUTOCOMPLETE
    Route::get('/search-address', [ApartmentController::class, 'searchAddress']);



    // Rotta per salvare un nuovo progetto nel database
    Route::post('/store', [ApartmentController::class, 'store'])
        ->name('store');

    // Rotte per la update
    Route::get('/edit/{id}', [ApartmentController::class, 'edit'])
        ->name('edit');

    Route::put('/update/{id}', [ApartmentController::class, 'update'])
        ->name('update');

    // Rotte per la delete
    Route::delete('/apartment/{id}/picture', [ApartmentController::class, 'deletePicture'])
        ->name('apartment.picture.delete');

    Route::delete('/delete/{id}', [ApartmentController::class, 'delete'])
        ->name('delete');
});
