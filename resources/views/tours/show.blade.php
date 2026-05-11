@extends('layouts.app')
@section('title', 'Tour #' . $tour->numero_tour)

@section('content')
<div class="d-flex align-items-center mb-4 gap-2">
    <a href="{{ route('tours.index') }}" class="btn btn-sm btn-outline-secondary">← Retour</a>
    <h4 class="mb-0 fw-bold">Tour #{{ $tour->numero_tour }} — {{ $tour->tontine->nom }}</h4>
</div>

<div class="row justify-content-center">
<div class="col-md-6">
    <div class="card">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-5 text-muted">Bénéficiaire</dt>
                <dd class="col-7">
                    <a href="{{ route('membres.show', $tour->membre) }}">
                        {{ $tour->membre->nom_complet }}
                    </a>
                </dd>

                <dt class="col-5 text-muted">Tontine</dt>
                <dd class="col-7">{{ $tour->tontine->nom }}</dd>

                <dt class="col-5 text-muted">Date prévue</dt>
                <dd class="col-7">{{ $tour->date_prevue->format('d/m/Y') }}</dd>

                <dt class="col-5 text-muted">Date effective</dt>
                <dd class="col-7">{{ $tour->date_effective?->format('d/m/Y') ?? '—' }}</dd>

                <dt class="col-5 text-muted">Montant reçu</dt>
                <dd class="col-7 fw-bold">
                    {{ $tour->montant_recu ? number_format($tour->montant_recu, 0, ',', ' ') . ' FCFA' : '—' }}
                </dd>

                <dt class="col-5 text-muted">Statut</dt>
                <dd class="col-7">
                    @if($tour->statut === 'complete')
                        <span class="badge bg-success-subtle text-success">Complété</span>
                    @elseif($tour->statut === 'reporte')
                        <span class="badge bg-warning-subtle text-warning">Reporté</span>
                    @else
                        <span class="badge bg-info-subtle text-info">En attente</span>
                    @endif
                </dd>
            </dl>
        </div>
        <div class="card-footer bg-white d-flex gap-2">
            <a href="{{ route('tours.edit', $tour) }}" class="btn btn-warning btn-sm">Modifier</a>
            <form action="{{ route('tours.destroy', $tour) }}" method="POST"
                  class="d-inline" onsubmit="return confirm('Supprimer ce tour ?')">
                @csrf @method('DELETE')
                <button class="btn btn-outline-danger btn-sm">Supprimer</button>
            </form>
        </div>
    </div>
</div>
</div>
@endsection