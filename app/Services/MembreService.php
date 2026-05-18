<?php

namespace App\Services;

use App\Models\Membre;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Pagination\LengthAwarePaginator;

class MembreService
{
    public function getListe(array $filtres = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = Membre::query()->withCount('tontines');

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
     * ✅ Crée le membre + son compte user + l'attache à la tontine
     */
    public function creer(array $data): array
    {
        $tontineId = $data['tontine_id'] ?? null;
        unset($data['tontine_id']);

        // ✅ Générer mot de passe temporaire
        $motDePasseTemp = Str::random(8);

        // Créer le membre
        $membre = Membre::create([
            'nom'       => $data['nom'],
            'prenom'    => $data['prenom'],
            'telephone' => $data['telephone'],
            'email'     => $data['email'] ?? null,
            'adresse'   => $data['adresse'] ?? null,
            'statut'    => 'actif',
        ]);

        // ✅ Créer automatiquement le compte user lié
        $user = User::create([
            'name'      => $membre->nom_complet,
            'email'     => $data['email'] ?? ($data['telephone'] . '@tontine.sn'),
            'password'  => Hash::make($motDePasseTemp),
            'role'      => 'membre',
            'membre_id' => $membre->id,
        ]);

        // ✅ Attacher à la tontine
        if ($tontineId) {
            $tontine = \App\Models\Tontine::findOrFail($tontineId);

            if (!$tontine->peutAjouterMembre()) {
                throw new \Exception(
                    'La tontine "' . $tontine->nom . '" a atteint sa limite de '
                    . $tontine->nombre_membres_max . ' membres.'
                );
            }

            $tontine->membres()->attach($membre->id, [
                'date_adhesion' => now(),
                'statut'        => 'actif',
            ]);
        }

        return [
            'membre'          => $membre,
            'user'            => $user,
            'mot_de_passe'    => $motDePasseTemp, // ✅ à afficher une seule fois
        ];
    }

    public function modifier(Membre $membre, array $data): Membre
    {
        $membre->update($data);

        // ✅ Mettre à jour aussi le compte user lié
        if ($membre->user) {
            $membre->user->update([
                'name'  => $membre->nom_complet,
                'email' => $data['email'] ?? $membre->user->email,
            ]);
        }

        return $membre->fresh();
    }

    public function supprimer(Membre $membre): bool
    {
        // ✅ Désactiver aussi le compte user
        if ($membre->user) {
            $membre->user->update(['role' => 'membre']);
            $membre->user->delete();
        }

        return $membre->delete();
    }

    public function getResume(Membre $membre): array
    {
        $membre->load([
            'tontines' => function ($q) {
                $q->whereNull('tontines.deleted_at')
                  ->withPivot('date_adhesion', 'statut');
            },
            'tours.tontine',
        ]);

        $cotisationsActives = $membre->cotisations()
            ->whereHas('tontine', fn($q) => $q->whereNull('deleted_at'));

        return [
            'nb_tontines'      => $membre->tontines->count(),
            'nb_cotisations'   => (clone $cotisationsActives)->count(),
            'tours_completes'  => $membre->tours->where('statut', 'complete')->count(),
            'tours_en_attente' => $membre->tours->where('statut', 'en_attente')->count(),
        ];
    }
}