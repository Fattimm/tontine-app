<?php

namespace App\Services;

use App\Models\Membre;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function getListe(): LengthAwarePaginator
    {
        return User::with('membre')->latest()->paginate(15);
    }

    public function getSupprimees(): LengthAwarePaginator
    {
        return User::onlyTrashed()
            ->with(['membre' => fn($q) => $q->withTrashed()])
            ->latest('deleted_at')
            ->paginate(15);
    }

    public function creer(array $data): User
    {
        return User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => $data['role'],
        ]);
    }

    public function changerRole(User $user, User $authUser, string $role): array
    {
        if ($user->id === $authUser->id) {
            return ['succes' => false, 'message' => 'Vous ne pouvez pas modifier votre propre rôle.'];
        }

        if ($user->isMembre()) {
            return ['succes' => false, 'message' => 'Les comptes membres sont gérés par les organisateurs.'];
        }

        $user->update(['role' => $role]);

        return ['succes' => true, 'message' => 'Rôle mis à jour.'];
    }

    public function supprimer(User $user, User $authUser): array
    {
        if ($user->id === $authUser->id) {
            return ['succes' => false, 'message' => 'Vous ne pouvez pas supprimer votre propre compte.'];
        }

        $user->delete();

        return ['succes' => true, 'message' => 'Utilisateur supprimé.'];
    }

    public function restaurer(int $id): User
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();

        if ($user->isMembre() && $user->membre_id) {
            Membre::withTrashed()->where('id', $user->membre_id)->restore();
        }

        return $user;
    }
}
