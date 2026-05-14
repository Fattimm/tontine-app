@extends('layouts.app')
@section('title', $membre->nom_complet)

@section('content')
<div class="d-flex align-items-center mb-4 gap-2">
    <a href="{{ route('membres.index') }}" class="btn btn-sm btn-outline-secondary">← Retour</a>
    <h4 class="mb-0 fw-bold">{{ $membre->nom_complet }}</h4>
    @if($membre->statut === 'actif')
        <span class="badge bg-success-subtle text-success">Actif</span>
    @else
        <span class="badge bg-secondary-subtle text-secondary">Inactif</span>
    @endif
</div>

<div class="row g-4">

    {{-- Infos personnelles --}}
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header bg-white fw-semibold">Informations personnelles</div>
            <div class="card-body">
                <p class="mb-3">
                    <span class="text-muted small d-block">Téléphone</span>
                    <strong>{{ $membre->telephone }}</strong>
                </p>
                <p class="mb-3">
                    <span class="text-muted small d-block">Email</span>
                    {{ $membre->email ?? '—' }}
                </p>
                <p class="mb-3">
                    <span class="text-muted small d-block">Adresse</span>
                    {{ $membre->adresse ?? '—' }}
                </p>
                <p class="mb-0">
                    <span class="text-muted small d-block">Membre depuis</span>
                    {{ $membre->created_at->format('d/m/Y') }}
                </p>
            </div>
            <div class="card-footer bg-white">
                <a href="{{ route('membres.edit', $membre) }}"
                   class="btn btn-warning btn-sm w-100">✏️ Modifier</a>
            </div>
        </div>
    </div>

    {{-- Statistiques globales --}}
    <div class="col-md-8">
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-4">
                <div class="card text-center">
                    <div class="card-body py-3">
                        <div class="fs-3 fw-bold text-success">{{ $resume['nb_tontines'] }}</div>
                        <div class="text-muted small">Tontines actives</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4">
                <div class="card text-center">
                    <div class="card-body py-3">
                        <div class="fs-3 fw-bold text-warning">{{ $resume['tours_en_attente'] }}</div>
                        <div class="text-muted small">Tours en attente</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4">
                <div class="card text-center">
                    <div class="card-body py-3">
                        <div class="fs-3 fw-bold text-secondary">{{ $resume['tours_completes'] }}</div>
                        <div class="text-muted small">Tours complétés</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ✅ Liste des tontines — cliquer pour voir le détail --}}
        <div class="card">
            <div class="card-header bg-white fw-semibold">
                Tontines du membre
                <span class="badge bg-secondary ms-2">{{ $membre->tontines->count() }}</span>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Tontine</th>
                            <th>Montant/mois</th>
                            <th>Fréquence</th>
                            <th>Adhésion</th>
                            <th>Statut</th>
                            <th class="text-end">Détail</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($membre->tontines as $tontine)
                        <tr>
                            <td class="fw-semibold">{{ $tontine->nom }}</td>
                            <td>{{ number_format($tontine->montant_cotisation, 0, ',', ' ') }} FCFA</td>
                            <td>{{ ucfirst($tontine->frequence) }}</td>
                            <td class="text-muted small">
                                {{ \Carbon\Carbon::parse($tontine->pivot->date_adhesion)->format('d/m/Y') }}
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
                                {{-- ✅ Clic → détail membre dans cette tontine --}}
                                <a href="{{ route('membres.tontine-detail', [$membre, $tontine]) }}"
                                   class="btn btn-outline-primary btn-sm">
                                    Voir détail →
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                Ce membre n'appartient à aucune tontine active.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection
