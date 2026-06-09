<?php

namespace App\Http\Controllers;

use App\Models\Cotisation;
use App\Models\Membre;
use App\Models\Tontine;
use App\Services\CotisationService;
use App\Services\TontineService;
use App\Http\Requests\StoreCotisationRequest;
use Barryvdh\DomPDF\Facade\Pdf;

class CotisationController extends Controller
{
    public function __construct(
        private CotisationService $cotisationService,
        private TontineService    $tontineService,
    ) {}

    public function index()
    {
        $filtres     = request()->only(['statut', 'mode_paiement', 'tontine_id']);
        $cotisations = $this->cotisationService->getListe($filtres);

        return view('cotisations.index', compact('cotisations'));
    }

    public function create()
    {
        $user = auth()->user();

        if ($user->isMembre() && $user->membre) {
            $monMembre = $user->membre;
            $membres   = collect([$monMembre]);
            $tontines  = $monMembre->tontines()
                ->where('tontines.statut', 'active')
                ->where(function ($q) {
                    $q->whereNull('tontines.date_fin')
                      ->orWhere('tontines.date_fin', '>=', today());
                })
                ->orderBy('nom')->get();
        } else {
            $monMembre = null;
            $membres   = Membre::actifs()->orderBy('nom')->get();
            $tontines  = $this->tontineService->getTontinesActives();
        }

        return view('cotisations.create', compact('membres', 'tontines', 'monMembre'));
    }

    public function store(StoreCotisationRequest $request)
    {
        $validated = $request->validated();

        // Sécurité : un membre ne peut cotiser que pour lui-même
        $user = auth()->user();
        if ($user->isMembre() && $user->membre) {
            $validated['membre_id'] = $user->membre->id;
        }

        $resultat = $this->cotisationService->enregistrer($validated);

        if (!$resultat['succes']) {
            return back()->withInput()->with('error', $resultat['message']);
        }

        $type     = $resultat['reserve'] ? 'warning' : 'success';
        $redirect = auth()->user()->isMembre()
            ? redirect()->route('dashboard')
            : redirect()->route('cotisations.index');

        return $redirect->with($type, $resultat['message']);
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
        $this->cotisationService->supprimer($cotisation);

        return back()->with('success', 'Cotisation supprimée.');
    }

    public function supprimees()
    {
        $cotisations = $this->cotisationService->getSupprimees(auth()->user());

        return view('cotisations.supprimees', compact('cotisations'));
    }

    public function restaurer(int $id)
    {
        $this->cotisationService->restaurer($id, auth()->user());

        return back()->with('success', 'Cotisation restaurée avec succès.');
    }

    public function exportPdf()
    {
        $filtres      = request()->only(['statut', 'mode_paiement', 'tontine_id']);
        $cotisations  = $this->cotisationService->getListeComplete($filtres);
        $tontineFiltree = !empty($filtres['tontine_id'])
            ? $this->tontineService->getTontinesActives()->firstWhere('id', $filtres['tontine_id'])
            : null;

        $pdf = Pdf::loadView('pdf.cotisations', compact('cotisations', 'tontineFiltree'))
                  ->setPaper('a4', 'landscape');

        return $pdf->download('cotisations-' . now()->format('Y-m-d') . '.pdf');
    }
}
