@extends('layouts.app')
@section('title', 'Tontines')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold"><i class="bi bi-briefcase text-success"></i> Tontines</h4>
        <small class="text-muted">{{ $tontines->total() }} tontine(s)</small>
    </div>
    <a href="{{ route('tontines.create') }}" class="btn btn-success btn-sm px-3">+ Nouvelle tontine</a>
</div>

{{-- ✅ FILTRE ICI --}}
<x-filtres
    :route="route('tontines.index')"
    :champs="['search', 'statut_tontine', 'frequence']"
    placeholder="Rechercher une tontine..."
/>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nom</th>
                    <th>Montant</th>
                    <th>Fréquence</th>
                    <th>Membres</th>
                    <th>Statut</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tontines as $tontine)
                <tr>
                    <td class="text-muted small">{{ $tontine->id }}</td>
                    <td class="fw-semibold">{{ $tontine->nom }}</td>
                    <td>{{ number_format($tontine->montant_cotisation, 0, ',', ' ') }} FCFA</td>
                    <td>{{ ucfirst($tontine->frequence) }}</td>
                    <td>
                        <span class="badge bg-light text-dark border">
                            {{ $tontine->membres_count }}
                        </span>
                    </td>
                    <td>
                        @if($tontine->statut === 'active')
                            <span class="badge bg-success-subtle text-success">Active</span>
                        @elseif($tontine->statut === 'terminee')
                            <span class="badge bg-secondary-subtle text-secondary">Terminée</span>
                        @else
                            <span class="badge bg-warning-subtle text-warning">Suspendue</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('tontines.show', $tontine) }}"
                           class="btn btn-outline-info btn-action">Voir</a>
                        @can('update', $tontine)
                        <a href="{{ route('tontines.edit', $tontine) }}"
                           class="btn btn-outline-warning btn-action">Modifier</a>
                        @endcan
                        <form action="{{ route('tontines.destroy', $tontine) }}" method="POST"
                              class="d-inline"
                              data-confirm="Supprimer « {{ $tontine->nom }} » ? Toutes les cotisations et tours associés seront également supprimés."
                              data-confirm-titre="Supprimer la tontine"
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
                        Aucune tontine.
                        <a href="{{ route('tontines.create') }}">Créer la première</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3 d-flex justify-content-center">
    {{ $tontines->withQueryString()->links() }}
</div>
@endsection
