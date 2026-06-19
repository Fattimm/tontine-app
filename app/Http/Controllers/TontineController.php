<?php

namespace App\Http\Controllers;

use App\Models\Tontine;
use App\Models\Membre;
use App\Services\TontineService;
use App\Services\CotisationService;
use App\Http\Requests\StoreTontineRequest;
use App\Http\Requests\UpdateTontineRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class TontineController extends Controller
{
    public function __construct(
        private TontineService    $tontineService,
        private CotisationService $cotisationService,
    ) {}

    public function index()
    {
        $this->authorize('viewAny', Tontine::class);
        $this->tontineService->mettreAJourStatutsExpires(auth()->user());

        $filtres  = request()->only(['search', 'statut', 'frequence']);
        $tontines = $this->tontineService->getListe(auth()->user(), $filtres);

        return view('tontines.index', compact('tontines'));
    }

    public function create()
    {
        return view('tontines.create');
    }

    public function store(StoreTontineRequest $request)
    {
        $this->authorize('create', Tontine::class);

        $this->tontineService->creer($request->validated(), auth()->id());

        return redirect()->route('tontines.index')->with('success', 'Tontine créée avec succès.');
    }

    public function show(Tontine $tontine)
    {
        $this->tontineService->mettreAJourStatut($tontine);
        $tontine->refresh();

        $stats   = $this->tontineService->getStats($tontine);
        $membres = $tontine->membres()->paginate(10);

        return view('tontines.show', compact('tontine', 'stats', 'membres'));
    }

    public function edit(Tontine $tontine)
    {
        $this->authorize('update', $tontine);
        $tirageDemarre = $tontine->tours()->exists();
        return view('tontines.edit', compact('tontine', 'tirageDemarre'));
    }

    public function update(UpdateTontineRequest $request, Tontine $tontine)
    {
        $this->authorize('update', $tontine);
        $estSuspendue = $tontine->statut === 'suspendue';
        $tontineMAJ   = $this->tontineService->modifier($tontine, $request->validated());

        if ($estSuspendue && $tontineMAJ->statut === 'active') {
            $message = 'Tontine réactivée avec succès — la date de fin a été prolongée.';
        } elseif ($tontineMAJ->tours()->exists()) {
            $message = 'Tontine mise à jour (seuls le nom et la date de fin sont modifiables après le premier tirage).';
        } else {
            $message = 'Tontine mise à jour.';
        }

        return redirect()->route('tontines.index')->with('success', $message);
    }

    public function destroy(Tontine $tontine)
    {
        $this->authorize('delete', $tontine);
        $this->tontineService->supprimer($tontine);

        return redirect()->route('tontines.index')->with('success', 'Tontine supprimée.');
    }

    public function supprimees()
    {
        $tontines = $this->tontineService->getSupprimees(auth()->user());
        return view('tontines.supprimees', compact('tontines'));
    }

    public function restaurer(int $id)
    {
        $tontine = $this->tontineService->restaurer($id, auth()->user());
        return back()->with('success', 'Tontine "' . $tontine->nom . '" restaurée avec ses cotisations et tours.');
    }

    public function ajouterMembre(Request $request, Tontine $tontine)
    {
        $request->validate(['membre_id' => 'required|exists:membres,id']);

        $resultat = $this->tontineService->ajouterMembre($tontine, $request->membre_id);

        return back()->with($resultat['succes'] ? 'success' : 'error', $resultat['message']);
    }

    public function retirerMembre(Tontine $tontine, Membre $membre)
    {
        $resultat = $this->tontineService->retirerMembre($tontine, $membre);

        return back()->with($resultat['succes'] ? 'success' : 'error', $resultat['message']);
    }

    public function tirage(Tontine $tontine)
    {
        $this->tontineService->completerToursExpires($tontine);
        $tontine->refresh();

        $peutFaireTirage   = $this->tontineService->peutFaireTirage($tontine);
        $eligibles         = $this->tontineService->getMembresEligibles($tontine);
        $tourEnAttente     = $tontine->tours()->where('statut', 'en_attente')->with('membre')->first();
        $membresAvecStatut = $this->tontineService->getMembresAvecStatutCotisation($tontine);

        return view('tontines.tirage', compact(
            'tontine', 'peutFaireTirage', 'eligibles', 'tourEnAttente', 'membresAvecStatut'
        ));
    }

    public function executerTirage(Tontine $tontine)
    {
        if (!$this->tontineService->peutFaireTirage($tontine)) {
            return back()->with('error', 'Les conditions pour le tirage ne sont pas encore remplies (tous les membres doivent avoir cotisé depuis le dernier tirage).');
        }

        $tour = $this->tontineService->genererTirage($tontine);

        if (!$tour) {
            return back()->with('error', 'Tous les membres ont déjà bénéficié. Tontine terminée.');
        }

        return redirect()
            ->route('tontines.tirage', $tontine)
            ->with('gagnant_id', $tour->membre_id)
            ->with('gagnant_nom', $tour->membre->nom_complet)
            ->with('success', 'Tirage effectué !');
    }

    public function exportPdf(Tontine $tontine)
    {
        $this->authorize('view', $tontine);

        $tontine->load(['membres', 'tours.membre', 'organisateur']);
        $cotisations = $this->cotisationService->getListeComplete(['tontine_id' => $tontine->id]);
        $stats       = $this->tontineService->getStats($tontine);

        $pdf = Pdf::loadView('pdf.tontine', compact('tontine', 'cotisations', 'stats'))
                  ->setPaper('a4', 'portrait');

        return $pdf->download('tontine-' . \Str::slug($tontine->nom) . '-' . now()->format('Y-m-d') . '.pdf');
    }
}
