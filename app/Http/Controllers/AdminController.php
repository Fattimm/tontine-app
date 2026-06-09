<?php

namespace App\Http\Controllers;

use App\Models\Membre;
use App\Models\User;
use App\Services\UserService;
use App\Http\Requests\StoreUserRequest;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct(private UserService $userService) {}

    public function users()
    {
        $users   = $this->userService->getListe();
        $membres = Membre::actifs()->orderBy('nom')->get();
        return view('admin.users', compact('users', 'membres'));
    }

    public function createUser(StoreUserRequest $request)
    {
        $this->userService->creer($request->validated());
        return back()->with('success', 'Utilisateur créé avec succès.');
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate(['role' => 'required|in:admin,organisateur'], [
            'role.required' => 'Le rôle est obligatoire.',
            'role.in'       => 'Le rôle sélectionné n\'est pas valide.',
        ]);

        $resultat = $this->userService->changerRole($user, auth()->user(), $request->role);

        return back()->with($resultat['succes'] ? 'success' : 'error', $resultat['message']);
    }

    public function deleteUser(User $user)
    {
        $resultat = $this->userService->supprimer($user, auth()->user());
        return back()->with($resultat['succes'] ? 'success' : 'error', $resultat['message']);
    }

    public function usersSupprimees()
    {
        $users = $this->userService->getSupprimees();

        return view('admin.users_supprimes', compact('users'));
    }

    public function restaurerUser(int $id)
    {
        $user = $this->userService->restaurer($id);
        return back()->with('success', $user->name . ' a été restauré.');
    }
}
