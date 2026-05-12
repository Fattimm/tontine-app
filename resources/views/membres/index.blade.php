@extends('layouts.app')
@section('title', 'Membres')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold">👥 Membres</h4>
        <small class="text-muted">{{ $membres->total() }} membre(s) enregistré(s)</small>
    </div>
    <a href="{{ route('membres.create') }}" class="btn btn-success btn-sm px-3">+ Nouveau membre</a>
</div>

{{-- ✅ FILTRE ICI --}}
<x-filtres
    :route="route('membres.index')"
    :champs="['search', 'statut_membre']"
    placeholder="Rechercher par nom, prénom ou téléphone..."
/>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nom complet</th>
                    <th>Téléphone</th>
                    <th>Email</th>
                    <th>Statut</th>
                    <th>Tontines</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($membres as $membre)
                <tr>
                    <td class="text-muted small">{{ $membre->id }}</td>
                    <td class="fw-semibold">{{ $membre->nom_complet }}</td>
                    <td>{{ $membre->telephone }}</td>
                    <td class="text-muted small">{{ $membre->email ?? '—' }}</td>
                    <td>
                        @if($membre->statut === 'actif')
                            <span class="badge badge-statut-actif">Actif</span>
                        @else
                            <span class="badge badge-statut-inactif">Inactif</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-light text-dark border">
                            {{ $membre->tontines_count }}
                        </span>
                    </td>
                    <td class="text-end">
                        <a href="{{ route('membres.show', $membre) }}"
                           class="btn btn-outline-info btn-action">Voir</a>
                        <a href="{{ route('membres.edit', $membre) }}"
                           class="btn btn-outline-warning btn-action">Modifier</a>
                        <a href="{{ route('membres.cotisations', $membre) }}"
                           class="btn btn-outline-secondary btn-action">Cotisations</a>
                        <form action="{{ route('membres.destroy', $membre) }}" method="POST"
                              class="d-inline"
                              onsubmit="return confirm('Supprimer {{ $membre->nom_complet }} ?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-outline-danger btn-action">Supprimer</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        Aucun membre trouvé.
                        <a href="{{ route('membres.create') }}">Ajouter le premier membre</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3 d-flex justify-content-center">
    {{ $membres->withQueryString()->links() }}
</div>
@endsection
