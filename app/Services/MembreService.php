<?php

namespace App\Services;

use App\Models\Membre;
use App\Models\Tontine;
use App\Models\Tour;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
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

        // Créer le membre
        $membre = Membre::create([
            'nom'       => $data['nom'],
            'prenom'    => $data['prenom'],
            'telephone' => $data['telephone'],
            'email'     => $data['email'] ?? null,
            'adresse'   => $data['adresse'] ?? null,
            'statut'    => 'actif',
        ]);

        $emailUser = $data['email'] ?? ($data['telephone'] . '@tontine.sn');

        // Mot de passe aléatoire non partagé — le membre le définira via le lien
        $user = User::create([
            'name'      => $membre->nom_complet,
            'email'     => $emailUser,
            'password'  => Hash::make(Str::random(32)),
            'role'      => 'membre',
            'membre_id' => $membre->id,
        ]);

        // Générer le lien de définition du mot de passe (valable 7 jours)
        $token    = Password::broker()->createToken($user);
        $resetUrl = url('/reset-password?token=' . $token . '&email=' . urlencode($emailUser));

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
            'membre'    => $membre,
            'user'      => $user,
            'reset_url' => $resetUrl,
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
        if ($membre->cotisations()->exists()) {
            throw new \Exception(
                $membre->nom_complet . ' a des cotisations enregistrées et ne peut pas être supprimé.'
            );
        }

        if ($membre->user) {
            $membre->user->delete();
        }

        return $membre->delete();
    }

    public function regenererLien(Membre $membre): string
    {
        $user  = $membre->user;
        $token = Password::broker()->createToken($user);
        return url('/reset-password?token=' . $token . '&email=' . urlencode($user->email));
    }

    public function getSupprimees(int $perPage = 15): LengthAwarePaginator
    {
        return Membre::onlyTrashed()->latest('deleted_at')->paginate($perPage);
    }

    public function restaurer(int $id): Membre
    {
        $membre = Membre::onlyTrashed()->findOrFail($id);
        $membre->restore();
        User::withTrashed()->where('membre_id', $membre->id)->restore();
        return $membre;
    }

    public function getDetailTontine(Membre $membre, Tontine $tontine): array
    {
        $cotisations = $membre->cotisations()
            ->where('tontine_id', $tontine->id)
            ->with('tour')
            ->latest('date_paiement')
            ->paginate(10);

        $tour = Tour::where('membre_id', $membre->id)
            ->where('tontine_id', $tontine->id)
            ->first();

        $totalPaye = $membre->cotisations()
            ->where('tontine_id', $tontine->id)
            ->where('statut', 'paye')
            ->where('est_reserve', false)
            ->sum('montant');

        $totalReserve = $membre->cotisations()
            ->where('tontine_id', $tontine->id)
            ->where('est_reserve', true)
            ->sum('montant');

        $pivot = $tontine->tousLesMembres()
            ->where('membre_id', $membre->id)
            ->first()?->pivot;

        return compact('cotisations', 'tour', 'totalPaye', 'totalReserve', 'pivot');
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