@extends('layouts.app')
@section('title', 'Utilisateurs supprimés')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold"><i class="bi bi-trash text-danger"></i> Utilisateurs supprimés</h4>
        <small class="text-muted">{{ $users->total() }} utilisateur(s) dans la corbeille</small>
    </div>
    <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary btn-sm">← Retour aux utilisateurs</a>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Membre lié</th>
                    <th>Supprimé le</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $u)
                <tr class="table-warning">
                    <td class="fw-semibold text-decoration-line-through text-muted">{{ $u->name }}</td>
                    <td class="text-muted small">{{ $u->email }}</td>
                    <td>
                        <span class="badge bg-secondary-subtle text-secondary">{{ ucfirst($u->role) }}</span>
                    </td>
                    <td class="text-muted small">{{ $u->membre?->nom_complet ?? '—' }}</td>
                    <td class="text-muted small">{{ $u->deleted_at->format('d/m/Y à H:i') }}</td>
                    <td class="text-end">
                        <form action="{{ route('admin.users.restaurer', $u->id) }}" method="POST"
                              class="d-inline"
                              data-confirm="Restaurer le compte de {{ $u->name }} ?"
                              data-confirm-titre="Restaurer l'utilisateur"
                              data-confirm-type="success"
                              data-confirm-btn="Oui, restaurer">
                            @csrf @method('PATCH')
                            <button class="btn btn-success btn-action"><i class="bi bi-arrow-counterclockwise"></i> Restaurer</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        Aucun utilisateur dans la corbeille.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3 d-flex justify-content-center">
    {{ $users->links() }}
</div>
@endsection
