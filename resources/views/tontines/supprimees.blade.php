@extends('layouts.app')
@section('title', 'Tontines supprimées')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold"><i class="bi bi-trash text-danger"></i> Tontines supprimées</h4>
        <small class="text-muted">{{ $tontines->total() }} tontine(s) dans la corbeille</small>
    </div>
    <a href="{{ route('tontines.index') }}" class="btn btn-outline-secondary btn-sm">← Retour aux tontines</a>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Fréquence</th>
                    <th>Montant cotisation</th>
                    <th>Supprimée le</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tontines as $t)
                <tr class="table-warning">
                    <td class="fw-semibold text-decoration-line-through text-muted">{{ $t->nom }}</td>
                    <td class="text-muted">{{ ucfirst($t->frequence) }}</td>
                    <td class="text-muted">{{ number_format($t->montant_cotisation, 0, ',', ' ') }} FCFA</td>
                    <td class="text-muted small">{{ $t->deleted_at->format('d/m/Y à H:i') }}</td>
                    <td class="text-end">
                        <form action="{{ route('tontines.restaurer', $t->id) }}" method="POST"
                              class="d-inline"
                              data-confirm="Restaurer la tontine &quot;{{ $t->nom }}&quot; ? Ses cotisations et tours seront aussi restaurés."
                              data-confirm-titre="Restaurer la tontine"
                              data-confirm-type="success"
                              data-confirm-btn="Oui, restaurer">
                            @csrf @method('PATCH')
                            <button class="btn btn-success btn-action"><i class="bi bi-arrow-counterclockwise"></i> Restaurer</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">
                        Aucune tontine dans la corbeille.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3 d-flex justify-content-center">
    {{ $tontines->links() }}
</div>
@endsection
