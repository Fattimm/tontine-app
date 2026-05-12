<?php

namespace App\Services;

use App\Models\Cotisation;
use App\Models\Membre;
use App\Models\Tontine;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CotisationService
{
    /**
     * Enregistrer une cotisation avec toutes les règles métier
     */
    public function enregistrer(array $data): array
    {
        $membre   = Membre::findOrFail($data['membre_id']);
        $tontine  = Tontine::findOrFail($data['tontine_id']);
        $datePaie = Carbon::parse($data['date_paiement']);
        $mois     = $datePaie->month;
        $annee    = $datePaie->year;

        // ✅ Règle 1 : montant forcé = montant_cotisation de la tontine
        $montant = $tontine->montant_cotisation;

        // ✅ Règle 2 : tontine doit être active
        if ($tontine->statut !== 'active') {
            return ['succes' => false, 'message' => 'Cette tontine n\'est plus active.'];
        }

        // ✅ Règle 3 : membre doit appartenir à la tontine
        if (!$tontine->membres()->where('membre_id', $membre->id)->exists()) {
            return ['succes' => false, 'message' => 'Ce membre n\'appartient pas à cette tontine.'];
        }

        // ✅ Règle 4 : vérifier si cotisation normale déjà faite ce mois
        $dejaCotiseCeMois = Cotisation::where('membre_id', $membre->id)
            ->where('tontine_id', $tontine->id)
            ->where('mois', $mois)
            ->where('annee', $annee)
            ->where('est_reserve', false)
            ->where('statut', 'paye')
            ->exists();

        // ✅ Règle 5 : vérifier si réserve déjà faite pour le mois prochain
        $moisProchain = $datePaie->copy()->addMonth();
        $dejaReserveMoisProchain = Cotisation::where('membre_id', $membre->id)
            ->where('tontine_id', $tontine->id)
            ->where('mois', $moisProchain->month)
            ->where('annee', $moisProchain->year)
            ->where('est_reserve', true)
            ->where('statut', 'paye')
            ->exists();

        // ✅ Règle 6 : ne pas dépasser la durée de la tontine
        if ($tontine->date_fin && $datePaie->gt($tontine->date_fin)) {
            return ['succes' => false, 'message' => 'La tontine est terminée, impossible de cotiser.'];
        }

        return DB::transaction(function () use (
            $data, $membre, $tontine, $montant,
            $mois, $annee, $moisProchain,
            $dejaCotiseCeMois, $dejaReserveMoisProchain
        ) {
            if (!$dejaCotiseCeMois) {
                // Cotisation normale pour ce mois
                $cotisation = Cotisation::create([
                    'membre_id'     => $membre->id,
                    'tontine_id'    => $tontine->id,
                    'tour_id'       => $data['tour_id'] ?? null,
                    'montant'       => $montant,
                    'date_paiement' => $data['date_paiement'],
                    'mode_paiement' => $data['mode_paiement'],
                    'statut'        => 'paye',
                    'est_reserve'   => false,
                    'mois'          => $mois,
                    'annee'         => $annee,
                    'notes'         => $data['notes'] ?? null,
                ]);

                return [
                    'succes'    => true,
                    'reserve'   => false,
                    'message'   => 'Cotisation enregistrée pour ' . $tontine->frequence . '.',
                    'cotisation'=> $cotisation,
                ];

            } elseif (!$dejaReserveMoisProchain) {
                // ✅ Déjà cotisé ce mois → mise en réserve pour le mois prochain
                if ($tontine->date_fin && $moisProchain->gt($tontine->date_fin)) {
                    return [
                        'succes'  => false,
                        'message' => 'Vous avez déjà cotisé ce mois. La réserve dépasserait la durée de la tontine.',
                    ];
                }

                $cotisation = Cotisation::create([
                    'membre_id'     => $membre->id,
                    'tontine_id'    => $tontine->id,
                    'tour_id'       => $data['tour_id'] ?? null,
                    'montant'       => $montant,
                    'date_paiement' => $data['date_paiement'],
                    'mode_paiement' => $data['mode_paiement'],
                    'statut'        => 'paye',
                    'est_reserve'   => true,
                    'mois'          => $moisProchain->month,
                    'annee'         => $moisProchain->year,
                    'notes'         => 'Réserve pour ' . $moisProchain->translatedFormat('F Y'),
                ]);

                return [
                    'succes'    => true,
                    'reserve'   => true,
                    'message'   => 'Cotisation mise en réserve pour ' . $moisProchain->translatedFormat('F Y') . '.',
                    'cotisation'=> $cotisation,
                ];

            } else {
                return [
                    'succes'  => false,
                    'message' => 'Vous avez déjà cotisé ce mois ET mis une réserve pour le mois prochain.',
                ];
            }
        });
    }

    public function getCotisationsParMembre(Membre $membre, int $perPage = 10): LengthAwarePaginator
    {
        return $membre->cotisations()
                      ->with(['tontine', 'tour'])
                      ->latest('date_paiement')
                      ->paginate($perPage);
    }

    public function getTotalCotise(Membre $membre): float
    {
        return (float) $membre->cotisations()->where('statut', 'paye')->sum('montant');
    }

    public function getStatsMembre(Membre $membre): array
    {
        $cotisations = $membre->cotisations();
        return [
            'total_paye'       => (float) (clone $cotisations)->where('statut', 'paye')->sum('montant'),
            'total_en_attente' => (float) (clone $cotisations)->where('statut', 'en_attente')->sum('montant'),
            'total_retard'     => (float) (clone $cotisations)->where('statut', 'retard')->sum('montant'),
            'nombre_paiements' => (clone $cotisations)->count(),
            'derniere_cotisation' => (clone $cotisations)->latest('date_paiement')->first(),
        ];
    }

    public function getCotisationsParTontine(Tontine $tontine, array $filtres = []): LengthAwarePaginator
    {
        $query = $tontine->cotisations()->with(['membre', 'tour']);

        if (!empty($filtres['statut']))        $query->where('statut', $filtres['statut']);
        if (!empty($filtres['mode_paiement'])) $query->where('mode_paiement', $filtres['mode_paiement']);
        if (!empty($filtres['date_debut']))    $query->whereDate('date_paiement', '>=', $filtres['date_debut']);
        if (!empty($filtres['date_fin']))      $query->whereDate('date_paiement', '<=', $filtres['date_fin']);

        return $query->latest('date_paiement')->paginate(15);
    }
}
