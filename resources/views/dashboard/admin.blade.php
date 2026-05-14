@extends('layouts.app')
@section('title', 'Dashboard Admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">🛡️ Dashboard Administrateur</h4>
    <span class="badge bg-danger">Admin</span>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card text-center border-success">
            <div class="card-body py-3">
                <div class="fs-2 fw-bold text-success">{{ $nbMembres }}</div>
                <div class="text-muted small">Membres</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center border-primary">
            <div class="card-body py-3">
                <div class="fs-2 fw-bold text-primary">{{ $nbTontines }}</div>
                <div class="text-muted small">Tontines</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center border-warning">
            <div class="card-body py-3">
                <div class="fs-2 fw-bold text-warning">{{ $nbCotisations }}</div>
                <div class="text-muted small">Cotisations</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center border-secondary">
            <div class="card-body py-3">
                <div class="fs-2 fw-bold text-secondary">{{ $nbUsers }}</div>
                <div class="text-muted small">Utilisateurs</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <a href="{{ route('membres.index') }}" class="card text-decoration-none">
            <div class="card-body d-flex align-items-center gap-3">
                <span class="fs-2">👥</span>
                <div><div class="fw-semibold">Gérer les membres</div>
                <div class="text-muted small">Créer, modifier, supprimer</div></div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="{{ route('tontines.index') }}" class="card text-decoration-none">
            <div class="card-body d-flex align-items-center gap-3">
                <span class="fs-2">💼</span>
                <div><div class="fw-semibold">Gérer les tontines</div>
                <div class="text-muted small">Créer et administrer</div></div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="{{ route('admin.users') }}" class="card text-decoration-none">
            <div class="card-body d-flex align-items-center gap-3">
                <span class="fs-2">🔑</span>
                <div><div class="fw-semibold">Gérer les utilisateurs</div>
                <div class="text-muted small">Rôles et accès</div></div>
            </div>
        </a>
    </div>
</div>
@endsection
