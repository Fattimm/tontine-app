<?php

namespace App\Services;

use App\Models\Cotisation;
use App\Models\Membre;
use App\Models\Tontine;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class CotisationService
{
    /**
     * Enregistrer une nouvelle cotisation
     * ✅ Wrappé dans une transaction pour la sécurité
     */
    public function enregistrer(array $data): Cotisation
    {
        return DB::transaction(function () use ($data) {
            return Cotisation::create($data);
        });
    }

    /**
     * Cotisations paginées d'un membre avec ses relations
     */
    public function getCotisationsParMembre(Membre $membre, int $perPage = 10): LengthAwarePaginator
    {
        return $membre->cotisations()
                      ->with(['tontine', 'tour'])
                      ->latest('date_paiement')
                      ->paginate($perPage);
    }

    /**
     * Total des cotisations payées d'un membre
     */
    public function getTotalCotise(Membre $membre): float
    {
        return (float) $membre->cotisations()
                               ->where('statut', 'paye')
                               ->sum('montant');
    }

    /**
     * ✅ Statistiques complètes d'un membre
     */
    public function getStatsMembre(Membre $membre): array
    {
        $cotisations = $membre->cotisations();

        return [
            'total_paye'      => (float) (clone $cotisations)->where('statut', 'paye')->sum('montant'),
            'total_en_attente'=> (float) (clone $cotisations)->where('statut', 'en_attente')->sum('montant'),
            'total_retard'    => (float) (clone $cotisations)->where('statut', 'retard')->sum('montant'),
            'nombre_paiements'=> (clone $cotisations)->count(),
            'derniere_cotisation' => (clone $cotisations)->latest('date_paiement')->first(),
        ];
    }

    /**
     * ✅ Cotisations d'une tontine avec filtres
     */
    public function getCotisationsParTontine(Tontine $tontine, array $filtres = []): LengthAwarePaginator
    {
        $query = $tontine->cotisations()
                         ->with(['membre', 'tour']);

        if (!empty($filtres['statut'])) {
            $query->where('statut', $filtres['statut']);
        }

        if (!empty($filtres['mode_paiement'])) {
            $query->where('mode_paiement', $filtres['mode_paiement']);
        }

        if (!empty($filtres['date_debut'])) {
            $query->whereDate('date_paiement', '>=', $filtres['date_debut']);
        }

        if (!empty($filtres['date_fin'])) {
            $query->whereDate('date_paiement', '<=', $filtres['date_fin']);
        }

        return $query->latest('date_paiement')->paginate(15);
    }

    /**
     * ✅ Vérifie si un membre a déjà cotisé pour un tour donné
     */
    public function aDejaCoitsePourTour(Membre $membre, int $tourId): bool
    {
        return $membre->cotisations()
                      ->where('tour_id', $tourId)
                      ->where('statut', 'paye')
                      ->exists();
    }
}
