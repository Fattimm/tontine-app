<?php

namespace App\Services;

use App\Models\Tontine;
use App\Models\Tour;
use Carbon\Carbon;

class TontineService
{
    /**
     * ✅ Génère automatiquement les tours par tirage aléatoire
     * appelé quand tous les membres ont cotisé pour le mois
     */
    public function genererTirage(Tontine $tontine): ?Tour
    {
        // Membres qui n'ont PAS encore eu leur tour
        $membresEligibles = $tontine->membres()
            ->whereDoesntHave('tours', function ($q) use ($tontine) {
                $q->where('tontine_id', $tontine->id)
                  ->whereIn('statut', ['complete', 'en_attente']);
            })
            ->get();

        if ($membresEligibles->isEmpty()) {
            return null; // tous ont eu leur tour → tontine terminée
        }

        // ✅ Tirage aléatoire parmi les éligibles
        $gagnant     = $membresEligibles->random();
        $dernierTour = $tontine->tours()->max('numero_tour') ?? 0;

        // Calculer la date du prochain tour selon la fréquence
        $dateProchaineEcheance = $this->calculerProchaineDate($tontine);

        $tour = Tour::create([
            'tontine_id'  => $tontine->id,
            'membre_id'   => $gagnant->id,
            'numero_tour' => $dernierTour + 1,
            'date_prevue' => $dateProchaineEcheance,
            'statut'      => 'en_attente',
        ]);

        return $tour->load('membre');
    }

    /**
     * ✅ Vérifie si tous les membres ont cotisé ce mois
     * et déclenche le tirage si c'est le cas
     */
    public function verifierEtTirer(Tontine $tontine): ?Tour
    {
        $moisActuel = now()->month;
        $anneeActuelle = now()->year;

        $nbMembres = $tontine->membres()->count();

        // Compter combien ont cotisé ce mois (cotisations normales, pas réserves)
        $nbAyantCotise = \App\Models\Cotisation::where('tontine_id', $tontine->id)
            ->where('mois', $moisActuel)
            ->where('annee', $anneeActuelle)
            ->where('est_reserve', false)
            ->where('statut', 'paye')
            ->distinct('membre_id')
            ->count('membre_id');

        // ✅ Tout le monde a cotisé → on tire
        if ($nbAyantCotise >= $nbMembres) {
            return $this->genererTirage($tontine);
        }

        return null;
    }

    private function calculerProchaineDate(Tontine $tontine): Carbon
    {
        $base = now();
        return match($tontine->frequence) {
            'hebdomadaire' => $base->addWeek(),
            'trimestriel'  => $base->addMonths(3),
            default        => $base->addMonth(), // mensuel
        };
    }

    public function getProchainBeneficiaire(Tontine $tontine): ?Tour
    {
        return $tontine->tours()
                       ->where('statut', 'en_attente')
                       ->orderBy('numero_tour')
                       ->with('membre')
                       ->first();
    }

    public function getStats(Tontine $tontine): array
    {
        $tours = $tontine->tours;
        return [
            'nb_membres'         => $tontine->membres()->count(),
            'nb_tours_total'     => $tours->count(),
            'nb_tours_completes' => $tours->where('statut', 'complete')->count(),
            'nb_tours_attente'   => $tours->where('statut', 'en_attente')->count(),
            'total_collecte'     => (float) $tontine->cotisations()
                                                     ->where('statut', 'paye')
                                                     ->sum('montant'),
            'prochain_beneficiaire' => $this->getProchainBeneficiaire($tontine),
        ];
    }
}
