@extends('layouts.app')
@section('title', 'Membres')

@section('content')
@if(session('nouveau_membre'))
@php $nm = session('nouveau_membre'); @endphp
<div class="alert alert-success border-success shadow-sm mb-4">
    <h5 class="fw-bold">✅ Membre créé avec succès !</h5>
    <p class="mb-1">Partagez ce lien d'activation à <strong>{{ $nm['nom'] }}</strong> :</p>
    <div class="bg-white rounded p-3 border mt-2">
        <div class="mb-2">
            <span class="text-muted small d-block">Email / Login</span>
            <strong>{{ $nm['email'] }}</strong>
        </div>
        <div>
            <span class="text-muted small d-block">Lien de définition du mot de passe</span>
            <div class="d-flex gap-2 align-items-start mt-1 flex-wrap">
                <code class="text-break small flex-grow-1" id="reset-link-new">{{ $nm['reset_url'] }}</code>
                <button class="btn btn-outline-secondary btn-sm flex-shrink-0"
                        onclick="navigator.clipboard.writeText(document.getElementById('reset-link-new').textContent).then(() => this.textContent = '✅ Copié')">
                    📋 Copier
                </button>
            </div>
        </div>
    </div>
    <p class="text-muted small mt-2 mb-0">
        ⚠️ Ce lien est valable <strong>7 jours</strong>. Vous pouvez en regénérer un depuis la fiche du membre.
    </p>
</div>
@endif
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
                            <span class="badge bg-success-subtle text-success">Actif</span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary">Inactif</span>
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
                              data-confirm="Supprimer {{ $membre->nom_complet }} ? Cette action est irréversible."
                              data-confirm-titre="Supprimer le membre"
                              data-confirm-type="danger"
                              data-confirm-btn="Oui, supprimer">
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
