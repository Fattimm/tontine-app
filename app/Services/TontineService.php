<?php

namespace App\Services;

use App\Models\Tontine;
use App\Models\Tour;

class TontineService
{
    /**
     * ✅ Prochain bénéficiaire d'une tontine
     */
    public function getProchainBeneficiaire(Tontine $tontine): ?Tour
    {
        return $tontine->tours()
                       ->where('statut', 'en_attente')
                       ->orderBy('numero_tour')
                       ->with('membre')
                       ->first();
    }

    /**
     * ✅ Statistiques globales d'une tontine
     */
    public function getStats(Tontine $tontine): array
    {
        $tours = $tontine->tours;

        return [
            'nb_membres'        => $tontine->membres()->count(),
            'nb_tours_total'    => $tours->count(),
            'nb_tours_completes'=> $tours->where('statut', 'complete')->count(),
            'nb_tours_attente'  => $tours->where('statut', 'en_attente')->count(),
            'total_collecte'    => (float) $tontine->cotisations()
                                                    ->where('statut', 'paye')
                                                    ->sum('montant'),
            'prochain_beneficiaire' => $this->getProchainBeneficiaire($tontine),
        ];
    }
}
