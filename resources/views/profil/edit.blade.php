@extends('layouts.app')
@section('title', 'Mon profil')

@section('content')
<div class="row justify-content-center">
<div class="col-md-6">

    <h4 class="fw-bold mb-4">👤 Mon profil</h4>

    {{-- Infos compte --}}
    <div class="card mb-4">
        <div class="card-header bg-white fw-semibold">Informations du compte</div>
        <div class="card-body">
            <p class="mb-2">
                <span class="text-muted small d-block">Nom</span>
                <strong>{{ auth()->user()->name }}</strong>
            </p>
            <p class="mb-2">
                <span class="text-muted small d-block">Email</span>
                {{ auth()->user()->email }}
            </p>
            <p class="mb-0">
                <span class="text-muted small d-block">Rôle</span>
                <span class="badge bg-success-subtle text-success">
                    {{ ucfirst(auth()->user()->role) }}
                </span>
            </p>
        </div>
    </div>

    {{-- Changer mot de passe --}}
    <div class="card">
        <div class="card-header bg-white fw-semibold">Changer le mot de passe</div>
        <div class="card-body">
            <form action="{{ route('profil.update') }}" method="POST">
                @csrf @method('PATCH')

                <div class="mb-3">
                    <label class="form-label fw-semibold">Ancien mot de passe</label>
                    <input type="password" name="ancien_mot_de_passe"
                           class="form-control @error('ancien_mot_de_passe') is-invalid @enderror">
                    @error('ancien_mot_de_passe')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Nouveau mot de passe</label>
                    <input type="password" name="mot_de_passe"
                           class="form-control @error('mot_de_passe') is-invalid @enderror">
                    @error('mot_de_passe')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Confirmer le mot de passe</label>
                    <input type="password" name="mot_de_passe_confirmation"
                           class="form-control">
                </div>

                <button type="submit" class="btn btn-success w-100">
                    Mettre à jour
                </button>
            </form>
        </div>
    </div>

</div>
</div>
@endsection
