<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ], [
            'email.required'    => 'L\'email est obligatoire.',
            'password.required' => 'Le mot de passe est obligatoire.',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'Email ou mot de passe incorrect.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    // ✅ Dashboard selon le rôle
    public function dashboard()
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return view('dashboard.admin', [
                'nbUsers'          => User::count(),
                'nbOrganisateurs'  => User::where('role', 'organisateur')->count(),
                'nbMembresUsers'   => User::where('role', 'membre')->count(),
            ]);
        }

        if ($user->isOrganisateur()) {
            return view('dashboard.organisateur', [
                'nbMembres'     => \App\Models\Membre::count(),
                'nbTontines'    => \App\Models\Tontine::where('statut', 'active')->count(),
                'nbCotisations' => \App\Models\Cotisation::whereMonth('date_paiement', now()->month)->count(),
            ]);
        }

        // Membre → son espace perso
        $membre = $user->membre;
        return view('dashboard.membre', compact('membre'));
    }

    public function profilEdit()
    {
        return view('profil.edit');
    }

    public function profilUpdate(Request $request)
    {
        $request->validate([
            'ancien_mot_de_passe' => 'required',
            'mot_de_passe'        => 'required|min:6|confirmed',
        ], [
            'ancien_mot_de_passe.required' => 'L\'ancien mot de passe est obligatoire.',
            'mot_de_passe.required'        => 'Le nouveau mot de passe est obligatoire.',
            'mot_de_passe.min'             => 'Minimum 6 caractères.',
            'mot_de_passe.confirmed'       => 'La confirmation ne correspond pas.',
        ]);

        if (!Hash::check($request->ancien_mot_de_passe, auth()->user()->password)) {
            return back()->with('error', 'Ancien mot de passe incorrect.');
        }

        auth()->user()->update([
            'password' => Hash::make($request->mot_de_passe),
        ]);

        return back()->with('success', 'Mot de passe mis à jour.');
    }
}
