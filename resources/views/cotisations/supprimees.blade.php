@extends('layouts.app')
@section('title', 'Cotisations supprimées')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold"><i class="bi bi-trash text-danger"></i> Cotisations supprimées</h4>
        <small class="text-muted">{{ $cotisations->total() }} cotisation(s) dans la corbeille</small>
    </div>
    <a href="{{ route('cotisations.index') }}" class="btn btn-outline-secondary btn-sm">← Retour aux cotisations</a>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Référence</th>
                    <th>Membre</th>
                    <th>Tontine</th>
                    <th>Montant</th>
                    <th>Date paiement</th>
                    <th>Supprimée le</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cotisations as $c)
                <tr class="table-warning">
                    <td><code class="small text-muted">{{ $c->reference }}</code></td>
                    <td class="text-muted">{{ $c->membre?->nom_complet ?? '—' }}</td>
                    <td class="text-muted">{{ $c->tontine?->nom ?? '—' }}</td>
                    <td class="text-muted fw-semibold">{{ number_format($c->montant, 0, ',', ' ') }} FCFA</td>
                    <td class="text-muted small">{{ $c->date_paiement->format('d/m/Y') }}</td>
                    <td class="text-muted small">{{ $c->deleted_at->format('d/m/Y à H:i') }}</td>
                    <td class="text-end">
                        <form action="{{ route('cotisations.restaurer', $c->id) }}" method="POST"
                              class="d-inline"
                              data-confirm="Restaurer cette cotisation ?"
                              data-confirm-titre="Restaurer la cotisation"
                              data-confirm-type="success"
                              data-confirm-btn="Oui, restaurer">
                            @csrf @method('PATCH')
                            <button class="btn btn-success btn-action"><i class="bi bi-arrow-counterclockwise"></i> Restaurer</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        Aucune cotisation dans la corbeille.
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
