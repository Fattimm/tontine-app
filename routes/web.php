<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AdminController,
    AuthController,
    MembreController,
    TontineController,
    CotisationController,
    TourController
};

// =========================================
// AUTH — public
// =========================================
Route::get('/login',  [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::post('/logout',[AuthController::class, 'logout'])->name('logout');

// Mot de passe oublié
Route::get('/forgot-password',  [AuthController::class, 'showForgotForm'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
// Réinitialisation (lien reçu par email — membres et utilisateurs)
Route::get('/reset-password',   [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password',  [AuthController::class, 'resetPassword'])->name('password.update');

Route::get('/', fn() => redirect()->route('dashboard'));

// =========================================
// TOUS LES CONNECTÉS — auth uniquement
// =========================================
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');

    // ✅ Profil — admin + organisateur + membre
    Route::get('/profil',    [AuthController::class, 'profilEdit'])->name('profil.edit');
    Route::patch('/profil',  [AuthController::class, 'profilUpdate'])->name('profil.update');

    // ✅ Membre voit son détail tontine
    Route::get('membres/{membre}/tontines/{tontine}',
        [MembreController::class, 'detailTontine'])->name('membres.tontine-detail');

    // ✅ Membre peut créer sa cotisation
    Route::get('cotisations/create',  [CotisationController::class, 'create'])->name('cotisations.create');
    Route::post('cotisations',        [CotisationController::class, 'store'])->name('cotisations.store');
});

// =========================================
// ORGANISATEUR + ADMIN — gestion complète
// =========================================
Route::middleware(['auth', 'organisateur'])->group(function () {

    // Membres
    Route::get('membres/supprimes', [MembreController::class, 'supprimes'])->name('membres.supprimes');
    Route::patch('membres/{id}/restaurer', [MembreController::class, 'restaurer'])->name('membres.restaurer');
    Route::resource('membres', MembreController::class);
    Route::get('membres/{membre}/cotisations',
        [CotisationController::class, 'parMembre'])->name('membres.cotisations');
    Route::post('membres/{membre}/regenerer-lien',
        [MembreController::class, 'regenererLien'])->name('membres.regenerer-lien');

    // Tontines
    Route::get('tontines/supprimees', [TontineController::class, 'supprimees'])->name('tontines.supprimees');
    Route::patch('tontines/{id}/restaurer', [TontineController::class, 'restaurer'])->name('tontines.restaurer');
    Route::resource('tontines', TontineController::class);
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

    // Cotisations — index, show, destroy (pas create/store, déjà au dessus)
    Route::get('cotisations/supprimees', [CotisationController::class, 'supprimees'])->name('cotisations.supprimees');
    Route::patch('cotisations/{id}/restaurer', [CotisationController::class, 'restaurer'])->name('cotisations.restaurer');
    Route::resource('cotisations', CotisationController::class)
         ->except(['create', 'store', 'edit', 'update']);

    // Tours
    Route::resource('tours', TourController::class);
    Route::patch('tours/{tour}/confirmer', [TourController::class, 'confirmer'])->name('tours.confirmer');
});

// =========================================
// ADMIN uniquement
// =========================================
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/users',                        [AdminController::class, 'users'])->name('users');
    Route::post('/users',                       [AdminController::class, 'createUser'])->name('users.store');
    Route::patch('/users/{user}/role',          [AdminController::class, 'updateRole'])->name('users.role');
    Route::delete('/users/{user}',              [AdminController::class, 'deleteUser'])->name('users.delete');
    Route::get('/users/supprimes',              [AdminController::class, 'usersSupprimees'])->name('users.supprimes');
    Route::patch('/users/{id}/restaurer',       [AdminController::class, 'restaurerUser'])->name('users.restaurer');
});
