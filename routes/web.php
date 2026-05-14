<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController,
    MembreController,
    TontineController,
    CotisationController,
    TourController
};

// ✅ Auth
Route::get('/login',  [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::post('/logout',[AuthController::class, 'logout'])->name('logout');

// ✅ Dashboard (tous les connectés)
Route::get('/dashboard', [AuthController::class, 'dashboard'])
     ->name('dashboard')
     ->middleware('auth');

Route::get('/', fn() => redirect()->route('dashboard'));

// ✅ Routes admin + organisateur
Route::middleware(['auth', 'organisateur'])->group(function () {
    Route::resource('membres',     MembreController::class);
    Route::resource('tontines',    TontineController::class);
    Route::resource('cotisations', CotisationController::class)->except(['edit', 'update']);
    Route::resource('tours',       TourController::class);

    Route::get('membres/{membre}/cotisations',
        [CotisationController::class, 'parMembre'])->name('membres.cotisations');
    Route::get('membres/{membre}/tontines/{tontine}',
        [MembreController::class, 'detailTontine'])->name('membres.tontine-detail');
    Route::get('tontines/{tontine}/cotisations',
        [CotisationController::class, 'parTontine'])->name('cotisations.par-tontine');
    Route::get('tontines/{tontine}/prochain-beneficiaire',
        [TontineController::class, 'prochainBeneficiaire'])->name('tontines.prochain-beneficiaire');
    Route::post('tontines/{tontine}/membres',
        [TontineController::class, 'ajouterMembre'])->name('tontines.ajouter-membre');
    Route::delete('tontines/{tontine}/membres/{membre}',
        [TontineController::class, 'retirerMembre'])->name('tontines.retirer-membre');
    Route::get('tontines/{tontine}/tirage',
        [TontineController::class, 'tirage'])->name('tontines.tirage');
    Route::post('tontines/{tontine}/tirage',
        [TontineController::class, 'executerTirage'])->name('tontines.tirage.executer');
});

// ✅ Routes admin uniquement
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::post('/users', [AdminController::class, 'createUser'])->name('users.store');
    Route::patch('/users/{user}/role', [AdminController::class, 'updateRole'])->name('users.role');
    Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('users.delete');
});
