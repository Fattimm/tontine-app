<?php

namespace App\Http\Controllers;

use App\Models\{Cotisation, Membre, Tontine, Tour};
use App\Services\CotisationService;
use App\Http\Requests\StoreCotisationRequest;

class CotisationController extends Controller
{
    public function __construct(
        private CotisationService $cotisationService  // ✅ Injection
    ) {}

    public function index()
    {
        $cotisations = Cotisation::with(['membre', 'tontine', 'tour'])
                                 ->latest('date_paiement')
                                 ->paginate(15);

        return view('cotisations.index', compact('cotisations'));
    }

    public function create()
    {
        $membres  = Membre::actifs()->orderBy('nom')->get();
        $tontines = Tontine::where('statut', 'active')->orderBy('nom')->get();
        $tours    = Tour::where('statut', 'en_attente')
                        ->with('tontine')
                        ->orderBy('numero_tour')
                        ->get();

        return view('cotisations.create', compact('membres', 'tontines', 'tours'));
    }

    public function store(StoreCotisationRequest $request)
    {
        $this->cotisationService->enregistrer($request->validated());

        return redirect()
            ->route('cotisations.index')
            ->with('success', 'Cotisation enregistrée avec succès.');
    }

    public function show(Cotisation $cotisation)
    {
        $cotisation->load(['membre', 'tontine', 'tour']);

        return view('cotisations.show', compact('cotisation'));
    }

    /**
     * ✅ Cotisations d'un membre spécifique
     */
    public function parMembre(Membre $membre)
    {
        $cotisations = $this->cotisationService->getCotisationsParMembre($membre);
        $stats       = $this->cotisationService->getStatsMembre($membre);

        return view('cotisations.par_membre', compact('membre', 'cotisations', 'stats'));
    }

    /**
     * ✅ Cotisations d'une tontine avec filtres
     */
    public function parTontine(Tontine $tontine)
    {
        $filtres     = request()->only(['statut', 'mode_paiement', 'date_debut', 'date_fin']);
        $cotisations = $this->cotisationService->getCotisationsParTontine($tontine, $filtres);

        return view('cotisations.par_tontine', compact('tontine', 'cotisations', 'filtres'));
    }

    public function destroy(Cotisation $cotisation)
    {
        $cotisation->delete();

        return back()->with('success', 'Cotisation supprimée.');
    }
}
