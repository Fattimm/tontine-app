<?php

namespace App\Http\Controllers;

use App\Models\Tour;
use App\Models\Tontine;
use App\Models\Membre;

class TourController extends Controller
{
    public function index()
    {
        $tours = Tour::with(['tontine', 'membre'])
                     ->orderBy('numero_tour')
                     ->paginate(15);

        return view('tours.index', compact('tours'));
    }

    public function create()
    {
        $tontines = Tontine::where('statut', 'active')->get();
        $membres  = Membre::actifs()->orderBy('nom')->get();

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

        Tour::create($data);

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
        $tontines = Tontine::where('statut', 'active')->get();
        $membres  = Membre::actifs()->orderBy('nom')->get();

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

        $tour->update($data);

        return redirect()
            ->route('tours.index')
            ->with('success', 'Tour mis à jour.');
    }

    public function destroy(Tour $tour)
    {
        $tour->delete();

        return redirect()
            ->route('tours.index')
            ->with('success', 'Tour supprimé.');
    }
}
