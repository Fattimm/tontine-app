@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><i class="bi bi-hand-wave"></i> Bonjour, {{ auth()->user()->name }}</h4>
    <span class="badge bg-success">Organisateur</span>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card text-center border-success">
            <div class="card-body py-3">
                <div class="fs-2 fw-bold text-success">{{ $nbMembres }}</div>
                <div class="text-muted small">Membres actifs</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center border-primary">
            <div class="card-body py-3">
                <div class="fs-2 fw-bold text-primary">{{ $nbTontines }}</div>
                <div class="text-muted small">Tontines actives</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center border-warning">
            <div class="card-body py-3">
                <div class="fs-2 fw-bold text-warning">{{ $nbCotisations }}</div>
                <div class="text-muted small">Cotisations ce mois</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-3">
        <a href="{{ route('membres.create') }}" class="card text-decoration-none border-success">
            <div class="card-body text-center py-3">
                <div class="fs-2"><i class="bi bi-plus-circle text-success"></i></div>
                <div class="fw-semibold small">Nouveau membre</div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('tontines.create') }}" class="card text-decoration-none border-primary">
            <div class="card-body text-center py-3">
                <div class="fs-2"><i class="bi bi-briefcase text-primary"></i></div>
                <div class="fw-semibold small">Nouvelle tontine</div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('cotisations.create') }}" class="card text-decoration-none border-warning">
            <div class="card-body text-center py-3">
                <div class="fs-2"><i class="bi bi-credit-card text-warning"></i></div>
                <div class="fw-semibold small">Enregistrer cotisation</div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('tontines.index') }}" class="card text-decoration-none">
            <div class="card-body text-center py-3">
                <div class="fs-2"><i class="bi bi-shuffle text-secondary"></i></div>
                <div class="fw-semibold small">Voir les tontines</div>
            </div>
        </a>
    </div>
</div>
@endsection
