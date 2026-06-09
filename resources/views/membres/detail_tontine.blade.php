@extends('layouts.app')
@section('title', $membre->nom_complet . ' — ' . $tontine->nom)

@section('content')

@php $estMembre = auth()->user()->isMembre(); @endphp

{{-- Breadcrumb --}}
<nav class="mb-4">
    <ol class="breadcrumb">
        @if($estMembre)
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard') }}">Mon espace</a>
            </li>
        @else
            <li class="breadcrumb-item">
                <a href="{{ route('membres.index') }}">Membres</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('membres.show', $membre) }}">{{ $membre->nom_complet }}</a>
            </li>
        @endif
        <li class="breadcrumb-item active">{{ $tontine->nom }}</li>
    </ol>
</nav>

<div class="d-flex align-items-center mb-4 gap-2">
    <a href="{{ $estMembre ? route('dashboard') : route('membres.show', $membre) }}"
       class="btn btn-sm btn-outline-secondary">← Retour</a>
    <h4 class="mb-0 fw-bold">{{ $membre->nom_complet }}</h4>
    <span class="text-muted">dans</span>
    <h4 class="mb-0 fw-bold text-success">{{ $tontine->nom }}</h4>
</div>

{{-- Statistiques dans cette tontine --}}
<div class="row g-3 mb-4">

    {{-- Total cotisé --}}
    <div class="col-md-4">
        <div class="card text-center border-success">
            <div class="card-body py-3">
                <div class="fs-4 fw-bold text-success">
                    {{ number_format($totalPaye, 0, ',', ' ') }} FCFA
                </div>
                <div class="text-muted small">Total cotisé</div>
            </div>
        </div>
    </div>

    {{-- Nombre de paiements --}}
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body py-3">
                <div class="fs-4 fw-bold">
                    {{-- ✅ Seulement cotisations normales, pas les réserves --}}
                    {{ $cotisations->total() }}
                </div>
                <div class="text-muted small">Paiements effectués</div>
            </div>
        </div>
    </div>

    {{-- Statut tour --}}
    <div class="col-md-4">
        <div class="card text-center {{ $tour && $tour->statut === 'complete' ? 'border-warning' : '' }}">
            <div class="card-body py-3">
                @if($tour)
                    @if($tour->statut === 'complete')
                        <div class="fs-2">🏆</div>
                        <div class="text-warning fw-bold small">A bénéficié</div>
                    @else
                        <div class="fs-4 fw-bold text-info">Tour #{{ $tour->numero_tour }}</div>
                        <div class="text-muted small">En attente</div>
                    @endif
                @else
                    <div class="fs-4 fw-bold text-muted">—</div>
                    <div class="text-muted small">Pas encore tiré</div>
                @endif
            </div>
        </div>
    </div>

</div>

<div class="row g-4">

    {{-- Infos tontine + tour --}}
    <div class="col-md-4">

        {{-- Info tontine --}}
        <div class="card mb-3">
            <div class="card-header bg-white fw-semibold">Infos tontine</div>
            <div class="card-body">
                <p class="mb-2">
                    <span class="text-muted small d-block">Montant cotisation</span>
                    <strong>{{ number_format($tontine->montant_cotisation, 0, ',', ' ') }} FCFA</strong>
                </p>
                <p class="mb-2">
                    <span class="text-muted small d-block">Fréquence</span>
                    {{ ucfirst($tontine->frequence) }}
                </p>
                <p class="mb-2">
                    <span class="text-muted small d-block">Membres</span>
                    {{ $tontine->membres()->count() }} / {{ $tontine->nombre_membres_max }}
                </p>
                <p class="mb-2">
                    <span class="text-muted small d-block">Date début</span>
                    {{ $tontine->date_debut->format('d/m/Y') }}
                </p>
                <p class="mb-0">
                    <span class="text-muted small d-block">Adhésion</span>
                    {{ $pivot ? \Carbon\Carbon::parse($pivot->date_adhesion)->format('d/m/Y') : '—' }}
                </p>
            </div>
            @if(!$estMembre)
            <div class="card-footer bg-white">
                <a href="{{ route('tontines.show', $tontine) }}"
                   class="btn btn-outline-success btn-sm w-100">
                    Voir la tontine →
                </a>
            </div>
            @endif
        </div>

        {{-- Statut tour --}}
        <div class="card">
            <div class="card-header bg-white fw-semibold">Statut bénéficiaire</div>
            <div class="card-body text-center">
                @if($tour)
                    @if($tour->statut === 'complete')
                        <div class="display-4 mb-2">🏆</div>
                        <h5 class="text-success fw-bold">A bénéficié !</h5>
                        <p class="text-muted small mb-1">
                            Tour #{{ $tour->numero_tour }}
                        </p>
                        <p class="text-muted small mb-1">
                            Date : {{ $tour->date_effective?->format('d/m/Y') ?? '—' }}
                        </p>
                        @if($tour->montant_recu)
                        <p class="fw-bold text-success">
                            {{ number_format($tour->montant_recu, 0, ',', ' ') }} FCFA reçus
                        </p>
                        @endif
                    @elseif($tour->statut === 'en_attente')
                        <div class="display-4 mb-2">⏳</div>
                        <h5 class="text-info fw-bold">Tour en attente</h5>
                        <p class="text-muted small">Tour #{{ $tour->numero_tour }}</p>
                        <p class="text-muted small">
                            Prévu le {{ $tour->date_prevue->format('d/m/Y') }}
                        </p>
                        <div class="alert alert-info small mb-0">
                            Montant attendu :
                            <strong>
                                {{ number_format(
                                    $tontine->montant_gain ?? ($tontine->montant_cotisation * $tontine->membres()->count()),
                                    0, ',', ' '
                                ) }} FCFA
                            </strong>
                        </div>
                    @else
                        <div class="display-4 mb-2">📅</div>
                        <h5 class="text-warning">Tour reporté</h5>
                    @endif
                @else
                    <div class="display-4 mb-2">🎲</div>
                    <p class="text-muted">
                        Pas encore désigné bénéficiaire.
                    </p>
                    <p class="text-muted small">
                        Le tirage se fait après que tous les membres aient cotisé.
                    </p>
                @endif
            </div>
        </div>

    </div>

    {{-- Historique cotisations dans cette tontine --}}
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold">
                    Cotisations dans cette tontine
                </span>
                <a href="{{ route('cotisations.create') }}?membre_id={{ $membre->id }}&tontine_id={{ $tontine->id }}"
                   class="btn btn-success btn-sm">
                    + Nouvelle cotisation
                </a>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Référence</th>
                            <th>Montant</th>
                            <th>Date</th>
                            <th>Mode</th>
                            <th>Type</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cotisations as $c)
                        <tr>
                            <td><code class="small">{{ $c->reference }}</code></td>
                            <td class="fw-semibold">
                                {{ number_format($c->montant, 0, ',', ' ') }} FCFA
                            </td>
                            <td>{{ $c->date_paiement->format('d/m/Y') }}</td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ str_replace('_', ' ', ucfirst($c->mode_paiement)) }}
                                </span>
                            </td>
                            <td>
                                @if($c->est_reserve)
                                    <span class="badge bg-info-subtle text-info">
                                        Réserve {{ $c->mois }}/{{ $c->annee }}
                                    </span>
                                @else
                                    <span class="badge bg-light text-dark border">
                                        Normal
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($c->statut === 'paye')
                                    <span class="badge bg-success-subtle text-success">Payé</span>
                                @elseif($c->statut === 'retard')
                                    <span class="badge bg-danger-subtle text-danger">Retard</span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning">En attente</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                Aucune cotisation dans cette tontine.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-3 d-flex justify-content-center">
            {{ $cotisations->links() }}
        </div>
    </div>

</div>
@endsection
