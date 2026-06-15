<?php

namespace App\Services;

use App\Models\Membre;
use App\Models\Tontine;
use App\Models\Tour;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class TourService
{
    public function getListe($user, array $filtres = []): LengthAwarePaginator
    {
        return Tontine::withCount([
                'tours',
                'tours as tours_completes' => fn($q) => $q->where('statut', 'complete'),
                'membres',
            ])
            ->when($user->isOrganisateur(), fn($q) => $q->where('organisateur_id', $user->id))
            ->when($filtres['search']    ?? null, fn($q, $v) => $q->where('nom', 'like', "%$v%"))
            ->when($filtres['statut']    ?? null, fn($q, $v) => $q->where('statut', $v))
            ->when($filtres['frequence'] ?? null, fn($q, $v) => $q->where('frequence', $v))
            ->latest()
            ->paginate(10);
    }

    public function getFormData(): array
    {
        return [
            'tontines' => Tontine::where('statut', 'active')->get(),
            'membres'  => Membre::actifs()->orderBy('nom')->get(),
        ];
    }

    public function creer(array $data): Tour
    {
        return Tour::create($data);
    }

    public function modifier(Tour $tour, array $data): Tour
    {
        $tour->update($data);
        return $tour->fresh();
    }

    public function supprimer(Tour $tour): void
    {
        $tour->delete();
    }

    public function confirmer(Tour $tour): array
    {
        $tontine = $tour->tontine()->withTrashed()->first();
        $montant = $tontine->montant_gain ?? ($tontine->montant_cotisation * $tontine->membres()->count());

        $tour->update([
            'statut'         => 'complete',
            'date_effective' => today(),
            'montant_recu'   => $montant,
        ]);

        return compact('tontine', 'montant');
    }
}
