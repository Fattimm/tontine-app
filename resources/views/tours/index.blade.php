@extends('layouts.app')
@section('title', 'Tours')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold">🔄 Tours</h4>
        <small class="text-muted">{{ $tours->total() }} tour(s)</small>
    </div>
    <a href="{{ route('tours.create') }}" class="btn btn-success btn-sm">+ Nouveau tour</a>
</div>

{{-- ✅ FILTRE ICI --}}
<x-filtres
    :route="route('tours.index')"
    :champs="['statut_tour']"
/>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tontine</th>
                    <th>Bénéficiaire</th>
                    <th>Date prévue</th>
                    <th>Montant reçu</th>
                    <th>Statut</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tours as $tour)
                <tr>
                    <td class="text-muted small">{{ $tour->numero_tour }}</td>
                    <td>{{ $tour->tontine->nom }}</td>
                    <td>
                        <a href="{{ route('membres.show', $tour->membre) }}">
                            {{ $tour->membre->nom_complet }}
                        </a>
                    </td>
                    <td>{{ $tour->date_prevue->format('d/m/Y') }}</td>
                    <td>
                        {{ $tour->montant_recu
                            ? number_format($tour->montant_recu, 0, ',', ' ') . ' FCFA'
                            : '—' }}
                    </td>
                    <td>
                        @if($tour->statut === 'complete')
                            <span class="badge bg-success-subtle text-success">Complété</span>
                        @elseif($tour->statut === 'reporte')
                            <span class="badge bg-warning-subtle text-warning">Reporté</span>
                        @else
                            <span class="badge bg-info-subtle text-info">En attente</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('tours.show', $tour) }}"
                           class="btn btn-outline-info btn-action">Voir</a>
                        <a href="{{ route('tours.edit', $tour) }}"
                           class="btn btn-outline-warning btn-action">Modifier</a>
                        <form action="{{ route('tours.destroy', $tour) }}" method="POST"
                              class="d-inline"
                              onsubmit="return confirm('Supprimer ce tour ?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-outline-danger btn-action">Supprimer</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        Aucun tour enregistré.
                        <span class="text-muted small d-block mt-1">
                            Les tours se génèrent automatiquement après cotisation complète.
                        </span>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3 d-flex justify-content-center">
    {{ $tours->withQueryString()->links() }}
</div>
@endsection
