<?php

namespace App\Services;

use App\Models\Membre;
use Illuminate\Pagination\LengthAwarePaginator;

class MembreService
{
    /**
     * Liste paginée avec recherche
     */
    public function getListe(array $filtres = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = Membre::query()->withCount('tontines');

        // ✅ Recherche sur nom, prénom ou téléphone
        if (!empty($filtres['search'])) {
            $search = $filtres['search'];
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('telephone', 'like', "%{$search}%");
            });
        }

        if (!empty($filtres['statut'])) {
            $query->where('statut', $filtres['statut']);
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Créer un membre
     */
    public function creer(array $data): Membre
    {
        return Membre::create($data);
    }

    /**
     * Mettre à jour un membre
     */
    public function modifier(Membre $membre, array $data): Membre
    {
        $membre->update($data);
        return $membre->fresh(); // ✅ Retourne l'instance rechargée
    }

    /**
     * Supprimer (soft delete)
     */
    public function supprimer(Membre $membre): bool
    {
        return $membre->delete();
    }

    /**
     * ✅ Résumé complet d'un membre
     */
    public function getResume(Membre $membre): array
    {
        // ✅ Charger seulement les tontines non supprimées
        $membre->load([
            'tontines' => function ($q) {
                $q->whereNull('tontines.deleted_at')
                ->withPivot('date_adhesion', 'statut');
            },
            'tours.tontine',
        ]);

        $cotisationsActives = $membre->cotisations()
        ->whereHas('tontine', function ($q) {
            $q->whereNull('deleted_at');
        });
        
        return [
            'nb_tontines'      => $membre->tontines->count(),
            'nb_cotisations'   => $membre->cotisations()->count(),
            'tours_completes'  => $membre->tours->where('statut', 'complete')->count(),
            'tours_en_attente' => $membre->tours->where('statut', 'en_attente')->count(),
        ];
    }
}
