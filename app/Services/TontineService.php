<?php

namespace App\Services;

use App\Models\Cotisation;
use App\Models\Membre;
use App\Models\Tontine;
use App\Models\Tour;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

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

    public  function calculerProchaineDate(Tontine $tontine): Carbon
    {
        $base = now();
        return match($tontine->frequence) {
            'quotidien'    => $base->addDay(),
            'hebdomadaire' => $base->addWeek(),
            'trimestriel'  => $base->addMonths(3),
            default        => $base->addMonth(),
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

    public function tousOntCotiseCeMois(Tontine $tontine): bool
    {
        $mois  = now()->month;
        $annee = now()->year;

        $nbMembres = $tontine->membres()->count();

        if ($nbMembres === 0) return false;

        $nbAyantCotise = \App\Models\Cotisation::where('tontine_id', $tontine->id)
            ->where('mois', $mois)
            ->where('annee', $annee)
            ->where('est_reserve', false)
            ->where('statut', 'paye')
            ->distinct('membre_id')
            ->count('membre_id');

        return $nbAyantCotise >= $nbMembres;
    }

    /**
     * Marque automatiquement comme "complete" les tours dont la date est dépassée.
     * Déclenché au chargement de la page tirage ou show.
     */
    // Supprimé : on ne complète plus automatiquement les tours expirés.
    // L'organisateur doit confirmer explicitement le versement via le bouton "Confirmer".
    // Un tour en_attente dont la date est dépassée reste bloquant — aucun nouveau tirage possible.
    public function completerToursExpires(Tontine $tontine): void
    {
        // no-op intentionnel
    }

    /**
     * Un tirage est possible si :
     * - Aucun tour n'est en attente (le dernier bénéficiaire a été payé)
     * - Tous les membres ont cotisé depuis le dernier tirage
     */
    public function peutFaireTirage(Tontine $tontine): bool
    {
        if ($tontine->tours()->where('statut', 'en_attente')->exists()) {
            return false;
        }

        $nbMembres = $tontine->membres()->count();
        if ($nbMembres === 0) return false;

        // Date de référence : dernier tirage ou création de la tontine
        $depuis = $tontine->tours()->latest('created_at')->value('created_at')
                  ?? $tontine->created_at;

        $nbAyantCotise = \App\Models\Cotisation::where('tontine_id', $tontine->id)
            ->where('est_reserve', false)
            ->where('statut', 'paye')
            ->where('created_at', '>', $depuis)
            ->distinct('membre_id')
            ->count('membre_id');

        return $nbAyantCotise >= $nbMembres;
    }

    public function getMembresEligibles(Tontine $tontine)
    {
        return $tontine->membres()
            ->whereDoesntHave('tours', function ($q) use ($tontine) {
                $q->where('tontine_id', $tontine->id)
                ->whereIn('statut', ['complete', 'en_attente']);
            })
            ->get();
    }

    /**
     * Met à jour le statut d'une tontine dont la date de fin est dépassée.
     * - Tous les membres ont eu leur tour → terminee
     * - Il reste des membres sans tour        → suspendue (relançable via modif date)
     */
    /**
     * Vérifie si tous les membres ont eu leur tour et termine la tontine immédiatement.
     * Appelé après chaque confirmation de versement.
     */
    public function verifierEtTerminer(Tontine $tontine): void
    {
        if ($tontine->statut === 'terminee') return;

        $nbMembres = $tontine->membres()->count();
        if ($nbMembres === 0) return;

        $nbAvecTourComplete = $tontine->tours()
            ->where('statut', 'complete')
            ->distinct('membre_id')
            ->count('membre_id');

        if ($nbAvecTourComplete >= $nbMembres) {
            $tontine->update(['statut' => 'terminee']);
        }
    }

    /**
     * Met à jour le statut des tontines dont la date de fin est dépassée.
     * - Tous ont eu leur tour → terminee
     * - Il en reste sans tour    → suspendue
     */
    public function mettreAJourStatut(Tontine $tontine): void
    {
        if ($tontine->statut === 'terminee') return;
        if (!$tontine->date_fin || !$tontine->date_fin->copy()->endOfDay()->isPast()) return;

        $nbMembres = $tontine->membres()->count();
        if ($nbMembres === 0) return;

        $nbAvecTour = $tontine->tours()
            ->whereIn('statut', ['en_attente', 'complete'])
            ->distinct('membre_id')
            ->count('membre_id');

        $nouveauStatut = ($nbAvecTour >= $nbMembres) ? 'terminee' : 'suspendue';

        if ($tontine->statut !== $nouveauStatut) {
            $tontine->update(['statut' => $nouveauStatut]);
        }
    }

    /**
     * Met à jour le statut de toutes les tontines expirées de l'utilisateur.
     * Appelé à chaque chargement de l'index ou du détail.
     */
    public function mettreAJourStatutsExpires($user): void
    {
        // date_fin < today() = date_fin était hier ou avant → la tontine a expiré depuis minuit
        $query = Tontine::where('statut', '!=', 'terminee')
            ->whereNotNull('date_fin')
            ->where('date_fin', '<', today());

        if ($user->isOrganisateur()) {
            $query->where('organisateur_id', $user->id);
        }

        $query->get()->each(fn($t) => $this->mettreAJourStatut($t));
    }

    public function getTontinesActives(): Collection
    {
        return Tontine::where('statut', 'active')->orderBy('nom')->get();
    }

    public function getListe(User $user, array $filtres = []): LengthAwarePaginator
    {
        $query = Tontine::withCount('membres')
            ->when($user->isOrganisateur(), fn($q) => $q->where('organisateur_id', $user->id))
            ->when($user->isMembre(), fn($q) => $q->whereHas('membres', fn($q2) =>
                $q2->where('membre_id', $user->membre_id)
            ));

        if (!empty($filtres['search']))    $query->where('nom', 'like', '%'.$filtres['search'].'%');
        if (!empty($filtres['statut']))    $query->where('statut', $filtres['statut']);
        if (!empty($filtres['frequence'])) $query->where('frequence', $filtres['frequence']);

        return $query->latest()->paginate(10);
    }

    public function getSupprimees(User $user): LengthAwarePaginator
    {
        return Tontine::onlyTrashed()
            ->when($user->isOrganisateur(), fn($q) => $q->where('organisateur_id', $user->id))
            ->latest('deleted_at')
            ->paginate(15);
    }

    public function creer(array $data, int $organisateurId): Tontine
    {
        return Tontine::create(array_merge($data, ['organisateur_id' => $organisateurId]));
    }

    public function modifier(Tontine $tontine, array $data): Tontine
    {
        $tontine->update($data);
        return $tontine->fresh();
    }

    public function supprimer(Tontine $tontine): void
    {
        $tontine->delete();
    }

    public function restaurer(int $id, User $user): Tontine
    {
        $tontine = Tontine::onlyTrashed()->findOrFail($id);

        if ($user->isOrganisateur() && $tontine->organisateur_id !== $user->id) {
            abort(403);
        }

        $tontine->restore();
        return $tontine;
    }

    public function getMembresAvecStatutCotisation(Tontine $tontine): Collection
    {
        $depuis = $tontine->tours()->latest('created_at')->value('created_at') ?? $tontine->created_at;

        return $tontine->membres->map(function ($m) use ($tontine, $depuis) {
            $m->a_cotise = Cotisation::where('membre_id', $m->id)
                ->where('tontine_id', $tontine->id)
                ->where('est_reserve', false)
                ->where('statut', 'paye')
                ->where('created_at', '>', $depuis)
                ->exists();
            return $m;
        });
    }

    public function ajouterMembre(Tontine $tontine, int $membreId): array
    {
        if ($tontine->statut === 'terminee') {
            return ['succes' => false, 'message' => 'Impossible de modifier une tontine terminée.'];
        }

        if (!$tontine->peutAjouterMembre()) {
            return ['succes' => false, 'message' => 'Cette tontine a atteint sa limite de ' . $tontine->nombre_membres_max . ' membres.'];
        }

        if ($tontine->membres()->where('membre_id', $membreId)->exists()) {
            return ['succes' => false, 'message' => 'Ce membre est déjà dans cette tontine.'];
        }

        $tontine->membres()->attach($membreId, [
            'date_adhesion' => now(),
            'statut'        => 'actif',
        ]);

        return ['succes' => true, 'message' => 'Membre ajouté à la tontine.'];
    }

    public function retirerMembre(Tontine $tontine, Membre $membre): array
    {
        if ($tontine->statut === 'terminee') {
            return ['succes' => false, 'message' => 'Impossible de retirer un membre d\'une tontine terminée.'];
        }

        if (Cotisation::where('membre_id', $membre->id)->where('tontine_id', $tontine->id)->exists()) {
            return ['succes' => false, 'message' => $membre->nom_complet . ' a déjà cotisé dans cette tontine, impossible de le retirer.'];
        }

        if ($tontine->tours()->where('membre_id', $membre->id)->whereIn('statut', ['complete', 'en_attente'])->exists()) {
            return ['succes' => false, 'message' => $membre->nom_complet . ' a déjà un tour assigné, impossible de le retirer.'];
        }

        if (Cotisation::where('membre_id', $membre->id)->where('tontine_id', $tontine->id)->where('mois', now()->month)->where('annee', now()->year)->exists()) {
            return ['succes' => false, 'message' => $membre->nom_complet . ' a déjà cotisé ce mois, impossible de le retirer.'];
        }

        $tontine->membres()->detach($membre->id);

        return ['succes' => true, 'message' => $membre->nom_complet . ' a été retiré de la tontine.'];
    }
}
