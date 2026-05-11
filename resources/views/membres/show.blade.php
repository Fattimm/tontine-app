@extends('layouts.app')
@section('title', $membre->nom_complet)

@section('content')
<div class="d-flex align-items-center mb-4 gap-2">
    <a href="{{ route('membres.index') }}" class="btn btn-sm btn-outline-secondary">← Retour</a>
    <h4 class="mb-0 fw-bold">{{ $membre->nom_complet }}</h4>
    @if($membre->statut === 'actif')
        <span class="badge badge-statut-actif">Actif</span>
    @else
        <span class="badge badge-statut-inactif">Inactif</span>
    @endif
</div>

<div class="row g-4">

    {{-- Infos personnelles --}}
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header bg-white fw-semibold">Informations</div>
            <div class="card-body">
                <p class="mb-2"><span class="text-muted small">Téléphone</span><br>
                    <strong>{{ $membre->telephone }}</strong></p>
                <p class="mb-2"><span class="text-muted small">Email</span><br>
                    {{ $membre->email ?? '—' }}</p>
                <p class="mb-2"><span class="text-muted small">Adresse</span><br>
                    {{ $membre->adresse ?? '—' }}</p>
                <p class="mb-0"><span class="text-muted small">Membre depuis</span><br>
                    {{ $membre->created_at->format('d/m/Y') }}</p>
            </div>
            <div class="card-footer bg-white">
                <a href="{{ route('membres.edit', $membre) }}" class="btn btn-warning btn-sm w-100">
                    Modifier
                </a>
            </div>
        </div>
    </div>

    {{-- Statistiques --}}
    <div class="col-md-8">
        <div class="row g-3 mb-3">
            <div class="col-6 col-md-3">
                <div class="card text-center">
                    <div class="card-body py-3">
                        <div class="fs-3 fw-bold text-success">{{ $resume['nb_tontines'] }}</div>
                        <div class="text-muted small">Tontines</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card text-center">
                    <div class="card-body py-3">
                        <div class="fs-3 fw-bold text-primary">{{ $resume['nb_cotisations'] }}</div>
                        <div class="text-muted small">Cotisations</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card text-center">
                    <div class="card-body py-3">
                        <div class="fs-3 fw-bold text-warning">{{ $resume['tours_en_attente'] }}</div>
                        <div class="text-muted small">Tours en attente</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card text-center">
                    <div class="card-body py-3">
                        <div class="fs-3 fw-bold text-secondary">{{ $resume['tours_completes'] }}</div>
                        <div class="text-muted small">Tours complétés</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tontines du membre --}}
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Tontines</span>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead>
                        <tr>
                            <th>Nom</th><th>Montant</th><th>Fréquence</th><th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($membre->tontines as $tontine)
                        <tr>
                            <td>
                                <a href="{{ route('tontines.show', $tontine) }}">
                                    {{ $tontine->nom }}
                                </a>
                            </td>
                            <td>{{ number_format($tontine->montant_cotisation, 0, ',', ' ') }} FCFA</td>
                            <td>{{ ucfirst($tontine->frequence) }}</td>
                            <td><span class="badge bg-success-subtle text-success">{{ $tontine->statut }}</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-muted text-center py-3">Aucune tontine.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white">
                <a href="{{ route('membres.cotisations', $membre) }}" class="btn btn-outline-primary btn-sm">
                    Voir toutes les cotisations →
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
