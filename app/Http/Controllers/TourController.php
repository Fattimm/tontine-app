<?php

namespace App\Http\Controllers;

use App\Models\Tour;
use App\Services\TontineService;
use App\Services\TourService;

class TourController extends Controller
{
    public function __construct(
        private TontineService $tontineService,
        private TourService    $tourService,
    ) {}

    public function index()
    {
        $tontines = $this->tourService->getListe(auth()->user());

        return view('tours.index', compact('tontines'));
    }

    public function create()
    {
        ['tontines' => $tontines, 'membres' => $membres] = $this->tourService->getFormData();

        return view('tours.create', compact('tontines', 'membres'));
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $data = $request->validate([
            'tontine_id'  => 'required|exists:tontines,id',
            'membre_id'   => 'required|exists:membres,id',
            'numero_tour' => 'required|integer|min:1',
            'date_prevue' => 'required|date',
            'statut'      => 'in:en_attente,complete,reporte',
        ]);

        $this->tourService->creer($data);

        return redirect()
            ->route('tours.index')
            ->with('success', 'Tour créé avec succès.');
    }

    public function show(Tour $tour)
    {
        $tour->load(['tontine', 'membre', 'cotisations.membre']);

        return view('tours.show', compact('tour'));
    }

    public function edit(Tour $tour)
    {
        ['tontines' => $tontines, 'membres' => $membres] = $this->tourService->getFormData();

        return view('tours.edit', compact('tour', 'tontines', 'membres'));
    }

    public function update(\Illuminate\Http\Request $request, Tour $tour)
    {
        $data = $request->validate([
            'membre_id'       => 'required|exists:membres,id',
            'numero_tour'     => 'required|integer|min:1',
            'date_prevue'     => 'required|date',
            'date_effective'  => 'nullable|date',
            'montant_recu'    => 'nullable|numeric|min:0',
            'statut'          => 'in:en_attente,complete,reporte',
        ]);

        $this->tourService->modifier($tour, $data);

        return redirect()
            ->route('tours.index')
            ->with('success', 'Tour mis à jour.');
    }

    public function destroy(Tour $tour)
    {
        $this->tourService->supprimer($tour);

        return redirect()
            ->route('tours.index')
            ->with('success', 'Tour supprimé.');
    }

    public function confirmer(Tour $tour)
    {
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
