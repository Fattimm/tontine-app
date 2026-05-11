<?php

namespace App\Http\Controllers;

use App\Models\Tontine;
use App\Models\Membre;
use App\Services\TontineService;

class TontineController extends Controller
{
    public function __construct(
        private TontineService $tontineService  // ✅ Injection
    ) {}

    public function index()
    {
        $tontines = Tontine::withCount('membres')
                           ->latest()
                           ->paginate(10);

        return view('tontines.index', compact('tontines'));
    }

    public function create()
    {
        return view('tontines.create');
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $data = $request->validate([
            'nom'                => 'required|string|max:150',
            'description'        => 'nullable|string',
            'montant_cotisation' => 'required|numeric|min:100',
            'frequence'          => 'required|in:hebdomadaire,mensuel,trimestriel',
            'date_debut'         => 'required|date',
            'date_fin'           => 'nullable|date|after:date_debut',
            'statut'             => 'in:active,terminee,suspendue',
        ]);

        Tontine::create($data);

        return redirect()
            ->route('tontines.index')
            ->with('success', 'Tontine créée avec succès.');
    }

    public function show(Tontine $tontine)
    {
        $stats    = $this->tontineService->getStats($tontine);
        $membres  = $tontine->membres()->paginate(10);

        return view('tontines.show', compact('tontine', 'stats', 'membres'));
    }

    public function edit(Tontine $tontine)
    {
        return view('tontines.edit', compact('tontine'));
    }

    public function update(\Illuminate\Http\Request $request, Tontine $tontine)
    {
        $data = $request->validate([
            'nom'                => 'required|string|max:150',
            'description'        => 'nullable|string',
            'montant_cotisation' => 'required|numeric|min:100',
            'frequence'          => 'required|in:hebdomadaire,mensuel,trimestriel',
            'date_debut'         => 'required|date',
            'date_fin'           => 'nullable|date|after:date_debut',
            'statut'             => 'in:active,terminee,suspendue',
        ]);

        $tontine->update($data);

        return redirect()
            ->route('tontines.index')
            ->with('success', 'Tontine mise à jour.');
    }

    public function destroy(Tontine $tontine)
    {
        $tontine->delete();

        return redirect()
            ->route('tontines.index')
            ->with('success', 'Tontine supprimée.');
    }

    /**
     * ✅ Prochain bénéficiaire
     */
    public function prochainBeneficiaire(Tontine $tontine)
    {
        $prochain = $this->tontineService->getProchainBeneficiaire($tontine);
        $stats    = $this->tontineService->getStats($tontine);

        return view('tontines.prochain_beneficiaire', compact('tontine', 'prochain', 'stats'));
    }

    /**
     * ✅ Attacher des membres à une tontine
     */
    public function ajouterMembre(\Illuminate\Http\Request $request, Tontine $tontine)
    {
        $request->validate([
            'membre_id' => 'required|exists:membres,id',
        ]);

        // Vérifie que le membre n'est pas déjà dans la tontine
        if ($tontine->membres()->where('membre_id', $request->membre_id)->exists()) {
            return back()->with('error', 'Ce membre est déjà dans cette tontine.');
        }

        $tontine->membres()->attach($request->membre_id, [
            'date_adhesion' => now(),
            'statut'        => 'actif',
        ]);

        return back()->with('success', 'Membre ajouté à la tontine.');
    }
}
