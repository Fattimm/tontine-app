@extends('layouts.app')
@section('title', 'Tirage — ' . $tontine->nom)

@section('content')
<div class="d-flex align-items-center mb-4 gap-2">
    <a href="{{ route('tontines.show', $tontine) }}" class="btn btn-sm btn-outline-secondary">← Retour</a>
    <h4 class="mb-0 fw-bold">🎲 Tirage — {{ $tontine->nom }}</h4>
</div>

<div class="row justify-content-center">
<div class="col-md-8">

    {{-- Statut cotisations ce mois --}}
    <div class="card mb-4">
        <div class="card-header bg-white fw-semibold">
            Cotisations de {{ now()->translatedFormat('F Y') }}
        </div>
        <div class="card-body">
            @php
                $mois      = now()->month;
                $annee     = now()->year;
                $tsMembres = $tontine->membres;
            @endphp
            <div class="row g-2">
                @foreach($tsMembres as $m)
                @php
                    $aCotise = \App\Models\Cotisation::where('membre_id', $m->id)
                        ->where('tontine_id', $tontine->id)
                        ->where('mois', $mois)
                        ->where('annee', $annee)
                        ->where('est_reserve', false)
                        ->where('statut', 'paye')
                        ->exists();
                @endphp
                <div class="col-md-6">
                    <div class="d-flex align-items-center gap-2 p-2 rounded border
                        {{ $aCotise ? 'border-success bg-success-subtle' : 'border-warning bg-warning-subtle' }}">
                        <span>{{ $aCotise ? '✅' : '⏳' }}</span>
                        <span class="fw-semibold">{{ $m->nom_complet }}</span>
                        <span class="badge ms-auto {{ $aCotise ? 'bg-success' : 'bg-warning text-dark' }}">
                            {{ $aCotise ? 'Cotisé' : 'En attente' }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>

            @if(!$tousOntCotise)
                <div class="alert alert-warning mt-3 mb-0">
                    ⚠️ Le tirage sera disponible quand tous les membres auront cotisé ce mois.
                </div>
            @endif
        </div>
    </div>

    {{-- Zone tirage --}}
    <div class="card shadow-sm">
        <div class="card-body p-4 text-center">

            @if(session('gagnant_nom'))
                {{-- ✅ Résultat affiché avec animation CSS --}}
                <div class="py-3" id="resultat">
                    <div class="display-1 mb-2 anim-bounce">🏆</div>
                    <h2 class="fw-bold text-success anim-apparaitre">
                        {{ session('gagnant_nom') }}
                    </h2>
                    <p class="text-muted fs-5">est le bénéficiaire du mois !</p>
                    <div class="alert alert-success mt-3 d-inline-block px-4">
                        Il recevra les fonds le
                        <strong>{{ now()->addMonth()->format('d/m/Y') }}</strong>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('tours.index') }}" class="btn btn-success px-4">
                            Voir dans les tours →
                        </a>
                        <a href="{{ route('tontines.show', $tontine) }}"
                           class="btn btn-outline-secondary ms-2">
                            Retour à la tontine
                        </a>
                    </div>
                </div>

            @elseif($eligibles->isEmpty())
                {{-- Tontine terminée --}}
                <div class="py-4">
                    <div class="display-1 mb-3">🎉</div>
                    <h4 class="text-success fw-bold">Tontine terminée !</h4>
                    <p class="text-muted">Tous les membres ont bénéficié de la tontine.</p>
                    <a href="{{ route('tontines.show', $tontine) }}"
                       class="btn btn-outline-secondary mt-2">Retour</a>
                </div>

            @else
                {{-- ✅ Interface de tirage --}}
                <h5 class="fw-bold mb-4">Membres éligibles au tirage</h5>

                {{-- Liste des participants --}}
                <div class="d-flex flex-wrap justify-content-center gap-2 mb-4"
                     id="participants">
                    @foreach($eligibles as $e)
                        <span class="badge fs-6 px-3 py-2 participant
                              bg-primary-subtle text-primary border border-primary"
                              data-nom="{{ $e->nom_complet }}">
                            {{ $e->nom_complet }}
                        </span>
                    @endforeach
                </div>

                {{-- Nom animé --}}
                <div id="zone-animation" class="mb-4" style="min-height:50px">
                    <div class="fs-2 fw-bold text-success" id="nom-anime"></div>
                </div>

                {{-- Bouton + formulaire caché --}}
                @if($tousOntCotise)
                    <button type="button" id="btn-tirage"
                            onclick="lancerAnimation()"
                            class="btn btn-success btn-lg px-5 fw-bold">
                        🎲 Lancer le tirage
                    </button>
                    <form action="{{ route('tontines.tirage.executer', $tontine) }}"
                          method="POST" id="form-tirage">
                        @csrf
                    </form>
                @else
                    <button class="btn btn-secondary btn-lg px-5" disabled>
                        🔒 En attente des cotisations
                    </button>
                @endif
            @endif

        </div>
    </div>

    {{-- Historique des tours --}}
    @if($tontine->tours->count() > 0)
    <div class="card mt-4">
        <div class="card-header bg-white fw-semibold">
            Historique des bénéficiaires
            <span class="badge bg-secondary ms-2">{{ $tontine->tours->count() }} tour(s)</span>
        </div>
        <div class="card-body p-0">
            <table class="table table-sm mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Bénéficiaire</th>
                        <th>Date prévue</th>
                        <th>Montant</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tontine->tours()->with('membre')->orderBy('numero_tour')->get() as $tour)
                    <tr>
                        <td class="text-muted">{{ $tour->numero_tour }}</td>
                        <td class="fw-semibold">{{ $tour->membre->nom_complet }}</td>
                        <td>{{ $tour->date_prevue->format('d/m/Y') }}</td>
                        <td>
                            {{ number_format(
                                $tontine->montant_cotisation * $tontine->membres()->count(),
                                0, ',', ' '
                            ) }} FCFA
                        </td>
                        <td>
                            @if($tour->statut === 'complete')
                                <span class="badge bg-success-subtle text-success">✅ Complété</span>
                            @elseif($tour->statut === 'en_attente')
                                <span class="badge bg-info-subtle text-info">⏳ En attente</span>
                            @else
                                <span class="badge bg-warning-subtle text-warning">Reporté</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>
</div>
@endsection

@push('scripts')
<script>
function lancerAnimation() {
    const btn          = document.getElementById('btn-tirage');
    const nomAnim      = document.getElementById('nom-anime');
    const participants = [...document.querySelectorAll('.participant')];
    const noms         = participants.map(p => p.dataset.nom);

    if (noms.length === 0) return;

    // Désactiver bouton
    btn.disabled    = true;
    btn.textContent = '⏳ Tirage en cours...';

    let tours   = 0;
    let vitesse = 80;
    let index   = 0;
    const maxTours = 35;

    function highlight(i) {
        // Retirer highlight de tous
        participants.forEach(p => {
            p.classList.remove('bg-warning', 'text-dark', 'border-warning');
            p.classList.add('bg-primary-subtle', 'text-primary', 'border-primary');
        });
        // Mettre highlight sur le courant
        const courant = participants[i % participants.length];
        courant.classList.remove('bg-primary-subtle', 'text-primary', 'border-primary');
        courant.classList.add('bg-warning', 'text-dark', 'border-warning');
        nomAnim.textContent = noms[i % noms.length];
    }

    function tourner() {
        highlight(index % noms.length);
        index++;
        tours++;

        // Ralentissement progressif
        if (tours > 25)      vitesse += 60;
        else if (tours > 20) vitesse += 30;
        else if (tours > 15) vitesse += 15;
        else if (tours > 10) vitesse += 8;

        if (tours < maxTours) {
            setTimeout(tourner, vitesse);
        } else {
            // ✅ Animation terminée → soumettre le formulaire
            nomAnim.innerHTML = '<span class="text-success">🏆 ' + noms[index % noms.length] + '</span>';
            setTimeout(() => {
                document.getElementById('form-tirage').submit();
            }, 800);
        }
    }

    tourner();
}
</script>

<style>
.anim-bounce {
    animation: bounce 0.6s ease infinite alternate;
}
.anim-apparaitre {
    animation: apparaitre 0.8s ease forwards;
}
@keyframes bounce {
    from { transform: translateY(0); }
    to   { transform: translateY(-12px); }
}
@keyframes apparaitre {
    from { opacity: 0; transform: scale(0.5); }
    to   { opacity: 1; transform: scale(1); }
}
</style>
@endpush
