@extends('layouts.app')
@section('title', 'Gestion utilisateurs')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><i class="bi bi-key text-warning"></i> Utilisateurs</h4>
    <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalCreate">
        + Nouvel utilisateur
    </button>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr><th>Nom</th><th>Email</th><th>Rôle</th><th>Membre lié</th><th class="text-end">Actions</th></tr>
            </thead>
            <tbody>
                @forelse($users as $u)
                <tr>
                    <td class="fw-semibold">
                        {{ $u->name }}
                        @if($u->id === auth()->id())
                            <span class="badge bg-info-subtle text-info ms-1">Vous</span>
                        @endif
                    </td>
                    <td>{{ $u->email }}</td>
                    <td>
                        @if($u->isMembre())
                            <span class="badge bg-info-subtle text-info">Membre</span>
                            <div class="text-muted small">Géré par l'organisateur</div>
                        @else
                            <form action="{{ route('admin.users.role', $u) }}" method="POST" class="d-flex gap-1">
                                @csrf @method('PATCH')
                                <select name="role" class="form-select form-select-sm select-sm-fixed"
                                        {{ $u->id === auth()->id() ? 'disabled' : '' }}>
                                    <option value="admin"        {{ $u->role === 'admin'        ? 'selected' : '' }}>Admin</option>
                                    <option value="organisateur" {{ $u->role === 'organisateur' ? 'selected' : '' }}>Organisateur</option>
                                </select>
                                @if($u->id !== auth()->id())
                                    <button class="btn btn-outline-primary btn-sm">OK</button>
                                @endif
                            </form>
                        @endif
                    </td>
                    <td class="text-muted small">
                        {{ $u->membre?->nom_complet ?? '—' }}
                    </td>
                    <td class="text-end">
                        @if($u->id !== auth()->id())
                        <form action="{{ route('admin.users.delete', $u) }}" method="POST"
                              class="d-inline"
                              data-confirm="Supprimer le compte de {{ $u->name }} ? Cette action est irréversible."
                              data-confirm-titre="Supprimer l'utilisateur"
                              data-confirm-type="danger"
                              data-confirm-btn="Oui, supprimer">
                            @csrf @method('DELETE')
                            <button class="btn btn-outline-danger btn-sm">Supprimer</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-4">Aucun utilisateur.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3 d-flex justify-content-center">{{ $users->links() }}</div>

{{-- Modal création --}}
<div class="modal fade" id="modalCreate" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Nouvel utilisateur</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nom</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Mot de passe</label>
                        <input type="password" name="password" class="form-control" required minlength="6">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Confirmer mot de passe</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Rôle</label>
                        <select name="role" class="form-select">
                            <option value="organisateur">Organisateur — gère les tontines et membres</option>
                            <option value="admin">Admin — gestion des comptes</option>
                        </select>
                        <div class="form-text text-muted">
                            Les comptes membres sont créés automatiquement par l'organisateur.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">Créer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

