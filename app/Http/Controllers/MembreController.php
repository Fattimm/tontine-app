<?php

namespace App\Http\Controllers;

use App\Models\Membre;
use App\Services\MembreService;
use App\Services\TontineService;
use App\Http\Requests\StoreMembreRequest;
use App\Http\Requests\UpdateMembreRequest;

class MembreController extends Controller
{
    public function __construct(
        private MembreService  $membreService,
        private TontineService $tontineService,
    ) {}

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
        $tontines = $this->tontineService->getTontinesActives();

        return view('membres.create', compact('tontines'));
    }

    public function store(StoreMembreRequest $request)
    {
        try {
            $resultat = $this->membreService->creer($request->validated());

            session()->flash('nouveau_membre', [
                'nom'       => $resultat['membre']->nom_complet,
                'email'     => $resultat['user']->email,
                'reset_url' => $resultat['reset_url'],
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
        $this->authorize('delete', $membre);
        try {
            $this->membreService->supprimer($membre);
            return redirect()->route('membres.index')->with('success', 'Membre supprimé.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function supprimes()
    {
        $membres = $this->membreService->getSupprimees();

        return view('membres.supprimes', compact('membres'));
    }

    public function restaurer(int $id)
    {
        $membre = $this->membreService->restaurer($id);

        return back()->with('success', $membre->nom_complet . ' a été restauré avec succès.');
    }

    public function regenererLien(Membre $membre)
    {
        if (!$membre->user) {
            return back()->with('error', 'Ce membre n\'a pas de compte utilisateur.');
        }

        $resetUrl = $this->membreService->regenererLien($membre);

        return back()->with('reset_url_regenere', $resetUrl);
    }

    /**
 * ✅ Détail d'un membre dans une tontine spécifique
 */
    public function detailTontine(Membre $membre, \App\Models\Tontine $tontine)
    {
        if ($tontine->trashed()) {
            return redirect()->route('membres.show', $membre)
                ->with('error', 'Cette tontine a été supprimée.');
        }

        $detail = $this->membreService->getDetailTontine($membre, $tontine);

        return view('membres.detail_tontine', array_merge(
            compact('membre', 'tontine'),
            $detail
        ));
    }
}
