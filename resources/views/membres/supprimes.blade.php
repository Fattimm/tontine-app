@extends('layouts.app')
@section('title', 'Membres supprimés')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold"><i class="bi bi-trash text-danger"></i> Membres supprimés</h4>
        <small class="text-muted">{{ $membres->total() }} membre(s) dans la corbeille</small>
    </div>
    <a href="{{ route('membres.index') }}" class="btn btn-outline-secondary btn-sm">← Retour aux membres</a>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Téléphone</th>
                    <th>Email</th>
                    <th>Supprimé le</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($membres as $m)
                <tr class="table-warning">
                    <td class="fw-semibold text-decoration-line-through text-muted">
                        {{ $m->nom_complet }}
                    </td>
                    <td class="text-muted">{{ $m->telephone }}</td>
                    <td class="text-muted small">{{ $m->email ?? '—' }}</td>
                    <td class="text-muted small">{{ $m->deleted_at->format('d/m/Y à H:i') }}</td>
                    <td class="text-end">
                        <form action="{{ route('membres.restaurer', $m->id) }}" method="POST"
                              class="d-inline"
                              data-confirm="Restaurer {{ $m->nom_complet }} ? Son compte sera réactivé."
                              data-confirm-titre="Restaurer le membre"
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
                        Aucun membre dans la corbeille.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3 d-flex justify-content-center">
    {{ $membres->links() }}
</div>
@endsection
