<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use App\Models\User;
use App\Http\Requests\LoginRequest;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
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
        return redirect()->route('login')->with('success', 'Vous avez été déconnecté.');
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
                'nbMembres'        => \App\Models\Membre::count(),
                'nbTontines'       => \App\Models\Tontine::where('statut', 'active')->count(),
                'nbCotisations'    => \App\Models\Cotisation::whereMonth('date_paiement', now()->month)->count(),
                'nbEnAttente'      => \App\Models\Cotisation::where('statut', 'en_attente')->count(),
            ]);
        }

        // Membre → son espace perso
        $membre = $user->membre;
        return view('dashboard.membre', compact('membre'));
    }

    public function showResetForm(Request $request)
    {
        return view('auth.reset-password', [
            'token' => $request->token,
            'email' => $request->email,
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'                 => 'required',
            'email'                 => 'required|email',
            'password'              => 'required|min:6|confirmed',
        ], [
            'password.required'  => 'Le mot de passe est obligatoire.',
            'password.min'       => 'Minimum 6 caractères.',
            'password.confirmed' => 'La confirmation ne correspond pas.',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->update(['password' => Hash::make($password)]);
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')
                ->with('success', 'Mot de passe défini avec succès. Vous pouvez vous connecter.');
        }

        return back()->withErrors(['email' => 'Ce lien est invalide ou a expiré.']);
    }

    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email'], [
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email'    => 'L\'adresse email n\'est pas valide.',
        ]);

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('success', 'Un lien de réinitialisation a été envoyé à votre adresse email.');
        }

        return back()->withErrors(['email' => 'Aucun compte trouvé avec cette adresse email.']);
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
