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
        $this->authorize('viewAny', Tontine::class);

        $query = Tontine::withCount('membres')
            // ✅ Organisateur voit SEULEMENT ses tontines
            ->when(auth()->user()->isOrganisateur(), function ($q) {
                $q->where('organisateur_id', auth()->id());
            })
            // ✅ Membre voit ses tontines
            ->when(auth()->user()->isMembre(), function ($q) {
                $q->whereHas('membres', function ($q2) {
                    $q2->where('membre_id', auth()->user()->membre_id);
                });
            });

        if (request('search'))    $query->where('nom', 'like', '%'.request('search').'%');
        if (request('statut'))    $query->where('statut', request('statut'));
        if (request('frequence')) $query->where('frequence', request('frequence'));

        $tontines = $query->latest()->paginate(10);
        return view('tontines.index', compact('tontines'));
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $this->authorize('create', Tontine::class);

        $data = $request->validate([
            'nom'                => 'required|string|max:150',
            'nombre_membres_max' => 'required|integer|min:2|max:100',
            'description'        => 'nullable|string',
            'montant_cotisation' => 'required|numeric|min:100',
            'frequence'          => 'required|in:hebdomadaire,mensuel,trimestriel',
            'date_debut'         => 'required|date',
            'date_fin'           => 'nullable|date|after:date_debut',
            'statut'             => 'in:active,terminee,suspendue',
        ]);

        // ✅ Lier la tontine à l'organisateur connecté
        $data['organisateur_id'] = auth()->id();

        Tontine::create($data);

        return redirect()
            ->route('tontines.index')
            ->with('success', 'Tontine créée avec succès.');
    }

    public function create()
    {
        return view('tontines.create');
    }

    public function show(Tontine $tontine)
    {
        $stats    = $this->tontineService->getStats($tontine);
        $membres = $tontine->tousLesMembres()->paginate(10);

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
            'nombre_membres_max'  => [
                'required', 'integer', 'min:2', 'max:100',
                function ($attribute, $value, $fail) use ($tontine) {
                    if ($value < $tontine->membres()->count()) {
                        $fail('Le maximum ne peut pas être inférieur au nombre de membres actuels ('
                            . $tontine->membres()->count() . ').');
                    }
                }
            ],
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
     * ✅ Attacher des membres à une tontine
     */
    public function ajouterMembre(\Illuminate\Http\Request $request, Tontine $tontine)
    {
        $request->validate([
            'membre_id' => 'required|exists:membres,id',
        ]);

        if (!$tontine->peutAjouterMembre()) {
            return back()->with('error',
                'Cette tontine a atteint sa limite de ' . $tontine->nombre_membres_max . ' membres.'
            );
        }

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

        /**
     * ✅ Retirer un membre d'une tontine
     */
    public function retirerMembre(Tontine $tontine, Membre $membre)
    {
        // ✅ Vérifier que le membre n'a pas encore eu son tour
        $aTour = $tontine->tours()
                        ->where('membre_id', $membre->id)
                        ->whereIn('statut', ['complete', 'en_attente'])
                        ->exists();

        if ($aTour) {
            return back()->with('error',
                $membre->nom_complet . ' a déjà un tour assigné, impossible de le retirer.'
            );
        }

        // ✅ Vérifier qu'il n'a pas de cotisations ce mois
        $aCotiseCeMois = \App\Models\Cotisation::where('membre_id', $membre->id)
            ->where('tontine_id', $tontine->id)
            ->where('mois', now()->month)
            ->where('annee', now()->year)
            ->exists();

        if ($aCotiseCeMois) {
            return back()->with('error',
                $membre->nom_complet . ' a déjà cotisé ce mois, impossible de le retirer.'
            );
        }

        // ✅ Détacher de la tontine
        $tontine->membres()->detach($membre->id);

        return back()->with('success',
            $membre->nom_complet . ' a été retiré de la tontine.'
        );
    }
    public function tirage(Tontine $tontine)
    {
        $tousOntCotise  = $this->tontineService->tousOntCotiseCeMois($tontine);
        $eligibles      = $this->tontineService->getMembresEligibles($tontine);
        $dernierTirage  = $tontine->tours()
                                ->where('statut', 'en_attente')
                                ->latest()
                                ->with('membre')
                                ->first();

        return view('tontines.tirage', compact(
            'tontine',
            'tousOntCotise',
            'eligibles',
            'dernierTirage'
        ));
    }

    public function executerTirage(Tontine $tontine)
    {
        // Vérifications
        if (!$this->tontineService->tousOntCotiseCeMois($tontine)) {
            return back()->with('error', 'Tous les membres n\'ont pas encore cotisé ce mois.');
        }

        $eligibles = $this->tontineService->getMembresEligibles($tontine);

        if ($eligibles->isEmpty()) {
            return back()->with('error', 'Tous les membres ont déjà bénéficié. Tontine terminée.');
        }

        // Tirage aléatoire
        $gagnant     = $eligibles->random();
        $dernierTour = $tontine->tours()->max('numero_tour') ?? 0;

        $tour = \App\Models\Tour::create([
            'tontine_id'  => $tontine->id,
            'membre_id'   => $gagnant->id,
            'numero_tour' => $dernierTour + 1,
            'date_prevue' => $this->tontineService->calculerProchaineDate($tontine),
            'statut'      => 'en_attente',
        ]);

        return redirect()
            ->route('tontines.tirage', $tontine)
            ->with('gagnant_id', $gagnant->id)
            ->with('gagnant_nom', $gagnant->nom_complet)
            ->with('success', 'Tirage effectué !');
    }
}
