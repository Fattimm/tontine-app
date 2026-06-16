<?php

namespace App\Services;

use App\Mail\CotisationEnregistree;
use App\Models\Cotisation;
use App\Models\Membre;
use App\Models\Tontine;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class CotisationService
{
    public function enregistrer(array $data, bool $parMembre = false): array
    {
        $membre     = Membre::findOrFail($data['membre_id']);
        $tontine    = Tontine::findOrFail($data['tontine_id']);
        $datePaie   = Carbon::parse($data['date_paiement']);
        $estReserve = (bool) ($data['est_reserve'] ?? false);

        // Montant forcé depuis la tontine
        $montant = $tontine->montant_cotisation;

        // Tontine active (statut + date_fin double-check)
        $estExpiree = $tontine->date_fin && $tontine->date_fin->copy()->endOfDay()->isPast();
        if ($tontine->statut !== 'active' || $estExpiree) {
            return ['succes' => false, 'message' => 'Cette tontine n\'est plus active.'];
        }

        // Membre appartient à la tontine
        if (!$tontine->membres()->where('membre_id', $membre->id)->exists()) {
            return ['succes' => false, 'message' => 'Ce membre n\'appartient pas à cette tontine.'];
        }

        // Période cible selon la nature et la fréquence
        $datePeriode = $estReserve
            ? $this->getProchaineDate($datePaie, $tontine->frequence)
            : $datePaie;

        $code = $this->getPeriodCode($datePeriode, $tontine->frequence);

        // Doublon : bloque si paye OU en_attente pour la même période
        $dejaExiste = Cotisation::where('membre_id', $membre->id)
            ->where('tontine_id', $tontine->id)
            ->where('est_reserve', $estReserve)
            ->where('mois', $code['mois'])
            ->where('annee', $code['annee'])
            ->whereIn('statut', ['paye', 'en_attente'])
            ->exists();

        if ($dejaExiste) {
            if ($estReserve) {
                return ['succes' => false, 'message' => 'Une réserve existe déjà pour la prochaine période.'];
            }
            return ['succes' => false, 'message' => 'Une cotisation est déjà enregistrée pour cette période (payée ou en attente de validation).'];
        }

        // Réserve ne dépasse pas la date de fin
        if ($estReserve && $tontine->date_fin && $datePeriode->gt($tontine->date_fin)) {
            return ['succes' => false, 'message' => 'La période de réserve dépasse la date de fin de la tontine.'];
        }

        return DB::transaction(function () use (
            $data, $membre, $tontine, $montant,
            $datePaie, $estReserve, $datePeriode, $code, $parMembre
        ) {
            // Si c'est le membre lui-même → en_attente (validation organisateur requise)
            $statut = $parMembre ? 'en_attente' : 'paye';

            $cotisation = Cotisation::create([
                'membre_id'     => $membre->id,
                'tontine_id'    => $tontine->id,
                'tour_id'       => $data['tour_id'] ?? null,
                'montant'       => $montant,
                'date_paiement' => $data['date_paiement'],
                'mode_paiement' => $data['mode_paiement'],
                'statut'        => $statut,
                'est_reserve'   => $estReserve,
                'mois'          => $code['mois'],
                'annee'         => $code['annee'],
                'notes'         => $estReserve
                    ? 'Réserve pour la période du ' . $datePeriode->translatedFormat('d F Y')
                    : ($data['notes'] ?? null),
            ]);

            if ($parMembre) {
                $message = 'Cotisation soumise avec succès. L\'organisateur va valider votre paiement.';
            } elseif ($estReserve) {
                $message = 'Cotisation en réserve enregistrée (période du ' . $datePeriode->translatedFormat('d F Y') . ').';
            } else {
                $message = 'Cotisation normale enregistrée avec succès.';
            }

            // Email seulement si directement validé (organisateur)
            if (!$parMembre) {
                $emailMembre = $membre->user?->email;
                if ($emailMembre && !str_ends_with($emailMembre, '@tontine.sn')) {
                    Mail::to($emailMembre)->send(new CotisationEnregistree($cotisation));
                }
            }

            return [
                'succes'      => true,
                'reserve'     => $estReserve,
                'en_attente'  => $parMembre,
                'message'     => $message,
                'cotisation'  => $cotisation,
            ];
        });
    }

    public function valider(Cotisation $cotisation): void
    {
        $cotisation->update(['statut' => 'paye']);

        $emailMembre = $cotisation->membre->user?->email;
        if ($emailMembre && !str_ends_with($emailMembre, '@tontine.sn')) {
            Mail::to($emailMembre)->send(new CotisationEnregistree($cotisation));
        }
    }

    // Convertit une date en code de période selon la fréquence
    // quotidien → jour de l'année, hebdomadaire → numéro de semaine, mensuel → mois, trimestriel → trimestre
    private function getPeriodCode(Carbon $date, string $frequence): array
    {
        return match($frequence) {
            'quotidien'    => ['mois' => $date->dayOfYear, 'annee' => $date->year],
            'hebdomadaire' => ['mois' => $date->week,      'annee' => $date->year],
            'trimestriel'  => ['mois' => $date->quarter,   'annee' => $date->year],
            default        => ['mois' => $date->month,     'annee' => $date->year],
        };
    }

    private function getProchaineDate(Carbon $date, string $frequence): Carbon
    {
        return match($frequence) {
            'quotidien'    => $date->copy()->addDay(),
            'hebdomadaire' => $date->copy()->addWeek(),
            'trimestriel'  => $date->copy()->addMonths(3),
            default        => $date->copy()->addMonth(),
        };
    }

    public function getTotalCotise(Membre $membre): float
    {
        return (float) $membre->cotisations()->where('statut', 'paye')->sum('montant');
    }

    public function getCotisationsParMembre(Membre $membre, int $perPage = 10): LengthAwarePaginator
    {
        return $membre->cotisations()
                    ->whereHas('tontine', function ($q) {
                        $q->whereNull('deleted_at');
                    })
                    ->with(['tontine', 'tour'])
                    ->latest('date_paiement')
                    ->paginate($perPage);
    }

    public function getStatsMembre(Membre $membre): array
    {
        $cotisations = $membre->cotisations()
                            ->whereHas('tontine', fn($q) => $q->whereNull('deleted_at'));

        return [
            'total_paye'          => (float) (clone $cotisations)->where('statut', 'paye')->sum('montant'),
            'total_en_attente'    => (float) (clone $cotisations)->where('statut', 'en_attente')->sum('montant'),
            'total_retard'        => (float) (clone $cotisations)->where('statut', 'retard')->sum('montant'),
            'nombre_paiements'    => (clone $cotisations)->count(),
            'derniere_cotisation' => (clone $cotisations)->latest('date_paiement')->first(),
        ];
    }

    public function getListeComplete(array $filtres = [])
    {
        $query = Cotisation::with(['membre', 'tontine', 'tour'])
            ->whereHas('tontine', fn($q) => $q->whereNull('deleted_at'));

        if (!empty($filtres['statut']))        $query->where('statut', $filtres['statut']);
        if (!empty($filtres['mode_paiement'])) $query->where('mode_paiement', $filtres['mode_paiement']);
        if (!empty($filtres['tontine_id']))    $query->where('tontine_id', $filtres['tontine_id']);

        return $query->latest('date_paiement')->get();
    }

    public function getListe(array $filtres = []): LengthAwarePaginator
    {
        $query = Cotisation::with(['membre', 'tontine', 'tour'])
            ->whereHas('tontine', fn($q) => $q->whereNull('deleted_at'));

        if (!empty($filtres['statut']))        $query->where('statut', $filtres['statut']);
        if (!empty($filtres['mode_paiement'])) $query->where('mode_paiement', $filtres['mode_paiement']);
        if (!empty($filtres['tontine_id']))    $query->where('tontine_id', $filtres['tontine_id']);

        return $query->latest('date_paiement')->paginate(15);
    }

    public function supprimer(Cotisation $cotisation): void
    {
        $cotisation->delete();
    }

    public function getSupprimees(User $user): LengthAwarePaginator
    {
        return Cotisation::onlyTrashed()
            ->with(['membre' => fn($q) => $q->withTrashed(), 'tontine' => fn($q) => $q->withTrashed()])
            ->when($user->isOrganisateur(), fn($q) =>
                $q->whereHas('tontine', fn($t) => $t->withTrashed()->where('organisateur_id', $user->id))
            )
            ->latest('deleted_at')
            ->paginate(15);
    }

    public function restaurer(int $id, User $user): void
    {
        $cotisation = Cotisation::onlyTrashed()->findOrFail($id);

        if ($user->isOrganisateur()) {
            $tontine = Tontine::withTrashed()->find($cotisation->tontine_id);
            if (!$tontine || $tontine->organisateur_id !== $user->id) {
                abort(403);
            }
        }

        $cotisation->restore();
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
