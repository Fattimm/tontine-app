@extends('layouts.app')
@section('title', 'Mon espace')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">👋 Mon espace</h4>
    <span class="badge bg-info">Membre</span>
</div>

@if($membre)
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
                    {{ $membre->cotisations()->count() }}
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

<div class="card">
    <div class="card-header bg-white fw-semibold">Mes tontines</div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr><th>Tontine</th><th>Montant</th><th>Statut</th><th class="text-end">Détail</th></tr>
            </thead>
            <tbody>
                @forelse($membre->tontines()->whereNull('tontines.deleted_at')->get() as $t)
                <tr>
                    <td class="fw-semibold">{{ $t->nom }}</td>
                    <td>{{ number_format($t->montant_cotisation, 0, ',', ' ') }} FCFA</td>
                    <td><span class="badge bg-success-subtle text-success">{{ $t->statut }}</span></td>
                    <td class="text-end">
                        <a href="{{ route('membres.tontine-detail', [$membre, $t]) }}"
                           class="btn btn-outline-primary btn-sm">Voir →</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center text-muted py-3">Aucune tontine.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@else
<div class="alert alert-warning">
    Votre compte n'est pas encore lié à un membre.
    Contactez l'administrateur.
</div>
@endif
@endsection
