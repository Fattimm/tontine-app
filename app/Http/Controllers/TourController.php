<?php

namespace App\Http\Controllers;

use App\Models\Tour;
use App\Services\TontineService;
use App\Services\TourService;
use App\Http\Requests\StoreTourRequest;
use App\Http\Requests\UpdateTourRequest;

class TourController extends Controller
{
    public function __construct(
        private TontineService $tontineService,
        private TourService    $tourService,
    ) {}

    public function index()
    {
        $this->authorize('viewAny', Tour::class);

        $filtres  = request()->only(['search', 'statut', 'frequence']);
        $tontines = $this->tourService->getListe(auth()->user(), $filtres);

        return view('tours.index', compact('tontines'));
    }

    public function create()
    {
        $this->authorize('create', Tour::class);

        ['tontines' => $tontines, 'membres' => $membres] = $this->tourService->getFormData();

        return view('tours.create', compact('tontines', 'membres'));
    }

    public function store(StoreTourRequest $request)
    {
        $this->authorize('create', Tour::class);

        $this->tourService->creer($request->validated());

        return redirect()
            ->route('tours.index')
            ->with('success', 'Tour créé avec succès.');
    }

    public function show(Tour $tour)
    {
        $this->authorize('view', $tour);

        $tour->load(['tontine', 'membre', 'cotisations.membre']);

        return view('tours.show', compact('tour'));
    }

    public function edit(Tour $tour)
    {
        $this->authorize('update', $tour);

        ['tontines' => $tontines, 'membres' => $membres] = $this->tourService->getFormData();

        return view('tours.edit', compact('tour', 'tontines', 'membres'));
    }

    public function update(UpdateTourRequest $request, Tour $tour)
    {
        $this->authorize('update', $tour);

        $this->tourService->modifier($tour, $request->validated());

        return redirect()
            ->route('tours.index')
            ->with('success', 'Tour mis à jour.');
    }

    public function destroy(Tour $tour)
    {
        $this->authorize('delete', $tour);

        $this->tourService->supprimer($tour);

        return redirect()
            ->route('tours.index')
            ->with('success', 'Tour supprimé.');
    }

    public function confirmer(Tour $tour)
    {
        $this->authorize('confirmer', $tour);
        ['tontine' => $tontine, 'montant' => $montant] = $this->tourService->confirmer($tour);

        $this->tontineService->verifierEtTerminer($tontine);
        $tontine->refresh();

        $message = $tour->membre->nom_complet . ' a bien reçu ' .
            number_format($montant, 0, ',', ' ') . ' FCFA.';

        if ($tontine->statut === 'terminee') {
            $message .= ' Tous les membres ont bénéficié — tontine terminée.';
            return redirect()->route('tontines.show', $tontine)->with('success', $message);
        }

        $message .= ' Le prochain tirage sera disponible après les nouvelles cotisations.';
        return redirect()->route('tontines.tirage', $tontine)->with('success', $message);
    }
}
