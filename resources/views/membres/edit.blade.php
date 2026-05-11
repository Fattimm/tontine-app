@extends('layouts.app')
@section('title', 'Modifier ' . $membre->nom_complet)

@section('content')
<div class="row justify-content-center">
<div class="col-md-7">

    <div class="d-flex align-items-center mb-4 gap-2">
        <a href="{{ route('membres.index') }}" class="btn btn-sm btn-outline-secondary">← Retour</a>
        <h4 class="mb-0 fw-bold">Modifier : {{ $membre->nom_complet }}</h4>
    </div>

    <div class="card">
        <div class="card-body p-4">
            <form action="{{ route('membres.update', $membre) }}" method="POST">
                @csrf @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nom <span class="text-danger">*</span></label>
                        <input type="text" name="nom"
                               class="form-control @error('nom') is-invalid @enderror"
                               value="{{ old('nom', $membre->nom) }}">
                        @error('nom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Prénom <span class="text-danger">*</span></label>
                        <input type="text" name="prenom"
                               class="form-control @error('prenom') is-invalid @enderror"
                               value="{{ old('prenom', $membre->prenom) }}">
                        @error('prenom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Téléphone <span class="text-danger">*</span></label>
                        <input type="text" name="telephone"
                               class="form-control @error('telephone') is-invalid @enderror"
                               value="{{ old('telephone', $membre->telephone) }}">
                        <div class="form-text">Format : 77XXXXXXX, 76XXXXXXX, 33XXXXXXX</div>
                        @error('telephone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" name="email"
                               class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email', $membre->email) }}">
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Adresse</label>
                        <input type="text" name="adresse"
                               class="form-control"
                               value="{{ old('adresse', $membre->adresse) }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Statut</label>
                        <select name="statut" class="form-select">
                            <option value="actif"   {{ $membre->statut === 'actif'   ? 'selected' : '' }}>Actif</option>
                            <option value="inactif" {{ $membre->statut === 'inactif' ? 'selected' : '' }}>Inactif</option>
                        </select>
                    </div>
                </div>

                <hr class="my-4">

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-warning px-4">Mettre à jour</button>
                    <a href="{{ route('membres.index') }}" class="btn btn-outline-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
@endsection
