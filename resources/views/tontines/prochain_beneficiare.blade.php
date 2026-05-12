@extends('layouts.app')
@section('title', 'Prochain bénéficiaire')

@section('content')
<div class="d-flex align-items-center mb-4 gap-2">
    <a href="{{ route('tontines.show', $tontine) }}" class="btn btn-sm btn-outline-secondary">← Retour</a>
    <h4 class="mb-0 fw-bold">Prochain bénéficiaire — {{ $tontine->nom }}</h4>
</div>

<div class="row justify-content-center">
<div class="col-md-6">
    <div class="card border-success text-center">
        <div class="card-body py-5">
            @if($prochain)
                <div class="display-1 mb-3">🏆</div>
                <h2 class="fw-bold text-success">{{ $prochain->membre->nom_complet }}</h2>
                <p class="text-muted mb-1">{{ $prochain->membre->telephone }}</p>
                <hr>
                <div class="row text-start mt-3">
                    <div class="col-6">
                        <small class="text-muted">Tour numéro</small>
                        <div class="fw-bold fs-5">#{{ $prochain->numero_tour }}</div>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">Date prévue</small>
                        <div class="fw-bold fs-5">{{ $prochain->date_prevue->format('d/m/Y') }}</div>
                    </div>
                    <div class="col-6 mt-3">
                        <small class="text-muted">Montant attendu</small>
                        <div class="fw-bold fs-5 text-success">
                            {{ number_format($tontine->montant_cotisation, 0, ',', ' ') }} FCFA
                        </div>
                    </div>
                    <div class="col-6 mt-3">
                        <small class="text-muted">Tours restants</small>
                        <div class="fw-bold fs-5">{{ $stats['nb_tours_attente'] }}</div>
                    </div>
                </div>
            @else
                <div class="display-1 mb-3">✅</div>
                <h3 class="text-muted">Tous les tours sont terminés</h3>
                <p class="text-muted">Cette tontine a complété tous ses cycles.</p>
            @endif
        </div>
    </div>
</div>
</div>
@endsection
