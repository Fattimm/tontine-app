@extends('layouts.app')
@section('title', 'Tours & Tirages')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold"><i class="bi bi-trophy text-warning"></i> Tours & Tirages</h4>
        <small class="text-muted">{{ $tontines->total() }} tontine(s)</small>
    </div>
</div>

<x-filtres
    :route="route('tours.index')"
    :champs="['search', 'statut_tontine', 'frequence']"
    placeholder="Rechercher une tontine..." />

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Tontine</th>
                    <th>Fréquence</th>
                    <th>Tours effectués</th>
                    <th>Prochain bénéficiaire</th>
                    <th>Statut</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tontines as $tontine)
                @php
                    $prochain = $tontine->prochainBeneficiaire();
                @endphp
                <tr>
                    <td class="fw-semibold">{{ $tontine->nom }}</td>
                    <td>{{ ucfirst($tontine->frequence) }}</td>
                    <td>
                        <span class="badge bg-light text-dark border">
                            {{ $tontine->tours_completes }} / {{ $tontine->membres_count }}
                        </span>
                        @if($tontine->membres_count > 0)
                            <div class="progress mt-1 progress-xs">
                                <div class="progress-bar bg-success"
                                     style="width:{{ ($tontine->tours_completes / $tontine->membres_count) * 100 }}%">
                                </div>
                            </div>
                        @endif
                    </td>
                    <td>
                        @if($prochain)
                            <span class="fw-semibold text-primary">{{ $prochain->membre->nom_complet }}</span>
                            <div class="text-muted small">Tour n°{{ $prochain->numero_tour }}</div>
                        @elseif($tontine->tours_count === 0)
                            <span class="text-muted small">Aucun tirage encore</span>
                        @else
                            <span class="text-muted small">—</span>
                        @endif
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
                        <a href="{{ route('tontines.tirage', $tontine) }}"
                           class="btn btn-success btn-action fw-semibold">
                            <i class="bi bi-shuffle text-white"></i> Gérer les tirages
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-5">
                        <div class="fs-1 mb-2"><i class="bi bi-trophy text-warning"></i></div>
                        Aucune tontine trouvée.
                        <a href="{{ route('tontines.create') }}" class="d-block mt-1">Créer une tontine</a>
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
