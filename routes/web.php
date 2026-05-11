<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MembreController;
use App\Http\Controllers\TontineController;
use App\Http\Controllers\CotisationController;
use App\Http\Controllers\TourController;

/*
|--------------------------------------------------------------------------
| Redirection racine
|--------------------------------------------------------------------------
*/
Route::get('/', fn() => redirect()->route('membres.index'))->name('home');

/*
|--------------------------------------------------------------------------
| Routes resource — CRUD complet automatique
|--------------------------------------------------------------------------
*/
Route::resource('membres',     MembreController::class);
Route::resource('tontines',    TontineController::class);
Route::resource('cotisations', CotisationController::class)->except(['edit', 'update']);
Route::resource('tours',       TourController::class);

/*
|--------------------------------------------------------------------------
| Routes métier supplémentaires
|--------------------------------------------------------------------------
*/

// ✅ Cotisations d'un membre spécifique
Route::get('membres/{membre}/cotisations', [CotisationController::class, 'parMembre'])
     ->name('membres.cotisations');

// ✅ Cotisations d'une tontine avec filtres
Route::get('tontines/{tontine}/cotisations', [CotisationController::class, 'parTontine'])
     ->name('cotisations.par-tontine');

// ✅ Prochain bénéficiaire d'une tontine
Route::get('tontines/{tontine}/prochain-beneficiaire', [TontineController::class, 'prochainBeneficiaire'])
     ->name('tontines.prochain-beneficiaire');

// ✅ Ajouter un membre à une tontine
Route::post('tontines/{tontine}/membres', [TontineController::class, 'ajouterMembre'])
     ->name('tontines.ajouter-membre');
