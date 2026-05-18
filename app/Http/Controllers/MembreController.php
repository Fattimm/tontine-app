<?php

namespace App\Http\Controllers;

use App\Models\Membre;
use App\Services\MembreService;
use App\Http\Requests\StoreMembreRequest;
use App\Http\Requests\UpdateMembreRequest;

class MembreController extends Controller
{
    public function __construct(private MembreService $membreService) {}

    /**
     * Liste des membres avec recherche
     */
    public function index()
    {
        $filtres = request()->only(['search', 'statut']);
        $membres = $this->membreService->getListe($filtres);

        return view('membres.index', compact('membres', 'filtres'));
    }

    public function create()
    {
        // ✅ Seulement les tontines actives avec des places disponibles
        $tontines = \App\Models\Tontine::where('statut', 'active')
                                        ->orderBy('nom')
                                        ->get();

        return view('membres.create', compact('tontines'));
    }

    public function store(StoreMembreRequest $request)
    {
        try {
            $resultat = $this->membreService->creer($request->validated());

            // ✅ Stocker le mot de passe en session pour l'afficher UNE seule fois
            session()->flash('nouveau_membre', [
                'nom'          => $resultat['membre']->nom_complet,
                'email'        => $resultat['user']->email,
                'mot_de_passe' => $resultat['mot_de_passe'],
            ]);

            return redirect()->route('membres.index');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(Membre $membre)
    {
        $resume = $this->membreService->getResume($membre);

        return view('membres.show', compact('membre', 'resume'));
    }

    public function edit(Membre $membre)
    {
        return view('membres.edit', compact('membre'));
    }

    public function update(UpdateMembreRequest $request, Membre $membre)
    {
        $this->membreService->modifier($membre, $request->validated());

        return redirect()
            ->route('membres.index')
            ->with('success', 'Membre mis à jour avec succès.');
    }

    public function destroy(Membre $membre)
    {
        $this->membreService->supprimer($membre);

        return redirect()
            ->route('membres.index')
            ->with('success', 'Membre supprimé avec succès.');
    }

    /**
 * ✅ Détail d'un membre dans une tontine spécifique
 */
   public function detailTontine(Membre $membre, \App\Models\Tontine $tontine)
    {
        // ✅ Bloquer l'accès si la tontine est supprimée
        if ($tontine->trashed()) {
            return redirect()
                ->route('membres.show', $membre)
                ->with('error', 'Cette tontine a été supprimée.');
        }

        // Cotisations du membre dans cette tontine uniquement
        $cotisations = $membre->cotisations()
                            ->where('tontine_id', $tontine->id)
                            ->with('tour')
                            ->latest('date_paiement')
                            ->paginate(10);

        // Tour du membre dans cette tontine
        $tour = \App\Models\Tour::where('membre_id', $membre->id)
                                ->where('tontine_id', $tontine->id)
                                ->first();

        // ✅ Stats uniquement pour cette tontine non supprimée
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

        return view('membres.detail_tontine', compact(
            'membre', 'tontine', 'cotisations',
            'tour', 'totalPaye', 'totalReserve', 'pivot'
        ));
    }
}
