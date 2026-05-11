@extends('layouts.app')
@section('title', 'Cotisation ' . $cotisation->reference)

@section('content')
<div class="d-flex align-items-center mb-4 gap-2">
    <a href="{{ route('cotisations.index') }}" class="btn btn-sm btn-outline-secondary">← Retour</a>
    <h4 class="mb-0 fw-bold">Détail cotisation</h4>
</div>

<div class="row justify-content-center">
<div class="col-md-6">
    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <span class="fw-semibold">Référence : <code>{{ $cotisation->reference }}</code></span>
            @if($cotisation->statut === 'paye')
                <span class="badge bg-success-subtle text-success">Payé</span>
            @elseif($cotisation->statut === 'retard')
                <span class="badge bg-danger-subtle text-danger">Retard</span>
            @else
                <span class="badge bg-warning-subtle text-warning">En attente</span>
            @endif
        </div>
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-5 text-muted">Membre</dt>
                <dd class="col-7">
                    <a href="{{ route('membres.show', $cotisation->membre) }}">
                        {{ $cotisation->membre->nom_complet }}
                    </a>
                </dd>

                <dt class="col-5 text-muted">Tontine</dt>
                <dd class="col-7">
                    <a href="{{ route('tontines.show', $cotisation->tontine) }}">
                        {{ $cotisation->tontine->nom }}
                    </a>
                </dd>

                <dt class="col-5 text-muted">Tour</dt>
                <dd class="col-7">
                    {{ $cotisation->tour ? 'Tour #' . $cotisation->tour->numero_tour : '—' }}
                </dd>

                <dt class="col-5 text-muted">Montant</dt>
                <dd class="col-7 fw-bold text-success fs-5">
                    {{ number_format($cotisation->montant, 0, ',', ' ') }} FCFA
                </dd>

                <dt class="col-5 text-muted">Date paiement</dt>
                <dd class="col-7">{{ $cotisation->date_paiement->format('d/m/Y') }}</dd>

                <dt class="col-5 text-muted">Mode paiement</dt>
                <dd class="col-7">
                    <span class="badge bg-light text-dark border">
                        {{ str_replace('_', ' ', ucfirst($cotisation->mode_paiement)) }}
                    </span>
                </dd>

                @if($cotisation->notes)
                <dt class="col-5 text-muted">Notes</dt>
                <dd class="col-7">{{ $cotisation->notes }}</dd>
                @endif

                <dt class="col-5 text-muted">Enregistrée le</dt>
                <dd class="col-7 text-muted small">{{ $cotisation->created_at->format('d/m/Y à H:i') }}</dd>
            </dl>
        </div>
        <div class="card-footer bg-white d-flex gap-2">
            <a href="{{ route('membres.cotisations', $cotisation->membre) }}"
               class="btn btn-outline-primary btn-sm">
                Voir toutes les cotisations du membre
            </a>
            <form action="{{ route('cotisations.destroy', $cotisation) }}" method="POST"
                  class="d-inline" onsubmit="return confirm('Supprimer cette cotisation ?')">
                @csrf @method('DELETE')
                <button class="btn btn-outline-danger btn-sm">Supprimer</button>
            </form>
        </div>
    </div>
</div>
</div>
@endsection