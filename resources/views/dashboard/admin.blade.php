@extends('layouts.app')
@section('title', 'Dashboard Admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><i class="bi bi-shield-check text-danger"></i> Dashboard Administrateur</h4>
    <span class="badge bg-danger px-3 py-2">Admin</span>
</div>

{{-- ✅ Stats globales sans tontines --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card text-center border-primary">
            <div class="card-body py-4">
                <div class="fs-1 fw-bold text-primary">{{ $nbUsers }}</div>
                <div class="text-muted">Utilisateurs</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center border-success">
            <div class="card-body py-4">
                <div class="fs-1 fw-bold text-success">{{ $nbOrganisateurs }}</div>
                <div class="text-muted">Organisateurs</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center border-info">
            <div class="card-body py-4">
                <div class="fs-1 fw-bold text-info">{{ $nbMembresUsers }}</div>
                <div class="text-muted">Comptes membres</div>
            </div>
        </div>
    </div>
</div>

{{-- Actions admin --}}
<div class="card">
    <div class="card-header bg-white fw-semibold">Actions</div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <a href="{{ route('admin.users') }}"
                   class="card text-decoration-none border-primary h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <span class="fs-2"><i class="bi bi-key text-warning"></i></span>
                        <div>
                            <div class="fw-semibold">Gérer les utilisateurs</div>
                            <div class="text-muted small">Créer organisateurs et membres</div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <div class="card border-0 bg-light h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <span class="fs-2"><i class="bi bi-slash-circle"></i></span>
                        <div>
                            <div class="fw-semibold text-muted">Tontines</div>
                            <div class="text-muted small">Gérées par les organisateurs</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 bg-light h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <span class="fs-2"><i class="bi bi-slash-circle"></i></span>
                        <div>
                            <div class="fw-semibold text-muted">Membres</div>
                            <div class="text-muted small">Gérés par les organisateurs</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
