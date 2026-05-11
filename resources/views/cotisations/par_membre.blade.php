@extends('layouts.app')
@section('title', 'Cotisations de ' . $membre->nom_complet)

@section('content')
<div class="d-flex align-items-center mb-4 gap-2">
    <a href="{{ route('membres.show', $membre) }}" class="btn btn-sm btn-outline-secondary">← Retour</a>
    <h4 class="mb-0 fw-bold">Cotisations — {{ $membre->nom_complet }}</h4>
</div>

{{-- Statistiques --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card text-center border-success">
            <div class="card-body py-3">
                <div class="fw-bold text-success fs-5">
                    {{ number_format($stats['total_paye'], 0, ',', ' ') }} F
                </div>
                <div class="text-muted small">Total payé</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center border-warning">
            <div class="card-body py-3">
                <div class="fw-bold text-warning fs-5">
                    {{ number_format($stats['total_en_attente'], 0, ',', ' ') }} F
                </div>
                <div class="text-muted small">En attente</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center border-danger">
            <div class="card-body py-3">
                <div class="fw-bold text-danger fs-5">
                    {{ number_format($stats['total_retard'], 0, ',', ' ') }} F
                </div>
                <div class="text-muted small">En retard</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center">
            <div class="card-body py-3">
                <div class="fw-bold fs-5">{{ $stats['nombre_paiements'] }}</div>
                <div class="text-muted small">Paiements total</div>
            </div>
        </div>
    </div>
</div>

{{-- Tableau des cotisations --}}
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span class="fw-semibold">Historique des cotisations</span>
        <a href="{{ route('cotisations.create') }}?membre_id={{ $membre->id }}"
           class="btn btn-success btn-sm">+ Nouvelle cotisation</a>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Référence</th>
                    <th>Tontine</th>
                    <th>Montant</th>
                    <th>Date</th>
                    <th>Mode</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cotisations as $c)
                <tr>
                    <td><code class="small">{{ $c->reference }}</code></td>
                    <td>{{ $c->tontine->nom }}</td>
                    <td class="fw-semibold">{{ number_format($c->montant, 0, ',', ' ') }} FCFA</td>
                    <td>{{ $c->date_paiement->format('d/m/Y') }}</td>
                    <td>
                        <span class="badge bg-light text-dark border">
                            {{ str_replace('_', ' ', ucfirst($c->mode_paiement)) }}
                        </span>
                    </td>
                    <td>
                        @if($c->statut === 'paye')
                            <span class="badge bg-success-subtle text-success">Payé</span>
                        @elseif($c->statut === 'retard')
                            <span class="badge bg-danger-subtle text-danger">Retard</span>
                        @else
                            <span class="badge bg-warning-subtle text-warning">En attente</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        Aucune cotisation enregistrée.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3 d-flex justify-content-center">
    {{ $cotisations->links() }}
</div>
@endsection
