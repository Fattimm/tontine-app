@extends('layouts.app')
@section('title', 'Mon espace')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">👋 Bonjour, {{ auth()->user()->name }}</h4>
    <span class="badge bg-info px-3 py-2">Membre</span>
</div>

@if($membre)

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body py-3">
                <div class="fs-2 fw-bold text-success">
                    {{ $membre->tontines()->whereNull('tontines.deleted_at')->count() }}
                </div>
                <div class="text-muted small">Mes tontines</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body py-3">
                <div class="fs-2 fw-bold text-primary">
                    {{ $membre->cotisations()
                        ->whereHas('tontine', fn($q) => $q->whereNull('deleted_at'))
                        ->count() }}
                </div>
                <div class="text-muted small">Mes cotisations</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body py-3">
                <div class="fs-2 fw-bold text-warning">
                    {{ $membre->tours()->where('statut', 'en_attente')->count() }}
                </div>
                <div class="text-muted small">Tours en attente</div>
            </div>
        </div>
    </div>
</div>

{{-- Mes tontines --}}
<div class="row g-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-white fw-semibold">Mes tontines</div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Tontine</th>
                            <th>Montant</th>
                            <th>Mon statut</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($membre->tontines()->whereNull('tontines.deleted_at')->get() as $t)
                        @php
                            $aCotiseCeMois = \App\Models\Cotisation::where('membre_id', $membre->id)
                                ->where('tontine_id', $t->id)
                                ->where('mois', now()->month)
                                ->where('annee', now()->year)
                                ->where('est_reserve', false)
                                ->where('statut', 'paye')
                                ->exists();

                            $monTour = \App\Models\Tour::where('membre_id', $membre->id)
                                ->where('tontine_id', $t->id)
                                ->first();
                        @endphp
                        <tr>
                            <td class="fw-semibold">{{ $t->nom }}</td>
                            <td>{{ number_format($t->montant_cotisation, 0, ',', ' ') }} FCFA</td>
                            <td>
                                @if($monTour?->statut === 'complete')
                                    <span class="badge bg-warning-subtle text-warning">🏆 Bénéficié</span>
                                @elseif($monTour?->statut === 'en_attente')
                                    <span class="badge bg-info-subtle text-info">⏳ Tour #{{ $monTour->numero_tour }}</span>
                                @elseif($aCotiseCeMois)
                                    <span class="badge bg-success-subtle text-success">✅ Cotisé ce mois</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger">⚠️ Pas encore cotisé</span>
                                @endif
                            </td>
                            <td class="text-end d-flex gap-1 justify-content-end">
                                <a href="{{ route('membres.tontine-detail', [$membre, $t]) }}"
                                   class="btn btn-outline-primary btn-sm">Détail</a>
                                @if(!$aCotiseCeMois)
                                    <a href="{{ route('cotisations.create') }}?membre_id={{ $membre->id }}&tontine_id={{ $t->id }}"
                                       class="btn btn-success btn-sm">Cotiser</a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                Vous n'êtes dans aucune tontine.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Tour en attente --}}
    <div class="col-md-4">
        @php
            $prochainTour = $membre->tours()
                ->where('statut', 'en_attente')
                ->with('tontine')
                ->first();
        @endphp

        <div class="card {{ $prochainTour ? 'border-info' : '' }}">
            <div class="card-header bg-white fw-semibold">Mon prochain tour</div>
            <div class="card-body text-center">
                @if($prochainTour)
                    <div class="display-4 mb-2">⏳</div>
                    <h5 class="fw-bold text-info">Tour #{{ $prochainTour->numero_tour }}</h5>
                    <p class="text-muted small mb-1">{{ $prochainTour->tontine->nom }}</p>
                    <p class="fw-bold text-success">
                        {{ number_format(
                            $prochainTour->tontine->montant_gain ?? ($prochainTour->tontine->montant_cotisation * $prochainTour->tontine->membres()->count()),
                            0, ',', ' '
                        ) }} FCFA
                    </p>
                    <p class="text-muted small">
                        Prévu le {{ $prochainTour->date_prevue->format('d/m/Y') }}
                    </p>
                @else
                    <div class="display-4 mb-2">🎲</div>
                    <p class="text-muted small">Pas encore de tour assigné.</p>
                @endif
            </div>
        </div>
    </div>
</div>

@else
<div class="alert alert-warning">
    ⚠️ Votre compte n'est pas encore lié à un membre.
    Contactez votre organisateur.
</div>
@endif
@endsection
