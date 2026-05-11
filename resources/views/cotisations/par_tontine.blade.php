@extends('layouts.app')
@section('title', 'Cotisations de ' . $tontine->nom)

@section('content')
<div class="d-flex align-items-center mb-4 gap-2">
    <a href="{{ route('tontines.show', $tontine) }}" class="btn btn-sm btn-outline-secondary">← Retour</a>
    <h4 class="mb-0 fw-bold">Cotisations — {{ $tontine->nom }}</h4>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr><th>Référence</th><th>Membre</th><th>Montant</th><th>Date</th><th>Mode</th><th>Statut</th></tr>
            </thead>
            <tbody>
                @forelse($cotisations as $c)
                <tr>
                    <td><code class="small">{{ $c->reference }}</code></td>
                    <td><a href="{{ route('membres.show', $c->membre) }}">{{ $c->membre->nom_complet }}</a></td>
                    <td class="fw-semibold">{{ number_format($c->montant, 0, ',', ' ') }} FCFA</td>
                    <td>{{ $c->date_paiement->format('d/m/Y') }}</td>
                    <td><span class="badge bg-light text-dark border">{{ str_replace('_', ' ', $c->mode_paiement) }}</span></td>
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
                <tr><td colspan="6" class="text-center text-muted py-4">Aucune cotisation.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3 d-flex justify-content-center">{{ $cotisations->links() }}</div>
@endsection