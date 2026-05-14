<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Membre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function users()
    {
        $users   = User::with('membre')->latest()->paginate(15);
        $membres = Membre::actifs()->orderBy('nom')->get();
        return view('admin.users', compact('users', 'membres'));
    }

    public function createUser(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:100',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|min:6|confirmed',
            'role'      => 'required|in:admin,organisateur,membre',
            'membre_id' => 'nullable|exists:membres,id',
        ]);

        User::create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'password'  => Hash::make($data['password']),
            'role'      => $data['role'],
            'membre_id' => $data['role'] === 'membre' ? $data['membre_id'] : null,
        ]);

        return back()->with('success', 'Utilisateur créé avec succès.');
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:admin,organisateur,membre',
        ]);

        // ✅ Empêcher de se rétrograder soi-même
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas modifier votre propre rôle.');
        }

        $user->update([
            'role'      => $request->role,
            'membre_id' => $request->role === 'membre' ? $request->membre_id : null,
        ]);

        return back()->with('success', 'Rôle mis à jour.');
    }

    public function deleteUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $user->delete();
        return back()->with('success', 'Utilisateur supprimé.');
    }
}
