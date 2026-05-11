@extends('layouts.app')
@section('title', 'Nouveau membre')

@section('content')
<div class="row justify-content-center">
<div class="col-md-7">

    <div class="d-flex align-items-center mb-4 gap-2">
        <a href="{{ route('membres.index') }}" class="btn btn-sm btn-outline-secondary">← Retour</a>
        <h4 class="mb-0 fw-bold">Ajouter un membre</h4>
    </div>

    <div class="card">
        <div class="card-body p-4">
            <form action="{{ route('membres.store') }}" method="POST">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nom <span class="text-danger">*</span></label>
                        <input type="text" name="nom"
                               class="form-control @error('nom') is-invalid @enderror"
                               value="{{ old('nom') }}" placeholder="Ex: Diallo">
                        @error('nom')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Prénom <span class="text-danger">*</span></label>
                        <input type="text" name="prenom"
                               class="form-control @error('prenom') is-invalid @enderror"
                               value="{{ old('prenom') }}" placeholder="Ex: Fatou">
                        @error('prenom')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Téléphone <span class="text-danger">*</span></label>
                        <input type="text" name="telephone"
                               class="form-control @error('telephone') is-invalid @enderror"
                               value="{{ old('telephone') }}" placeholder="Ex: 771234567">
                        <div class="form-text">Format : 77XXXXXXX, 76XXXXXXX, 33XXXXXXX</div>
                        @error('telephone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" name="email"
                               class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}" placeholder="Ex: fatou@email.com">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Adresse</label>
                        <input type="text" name="adresse"
                               class="form-control @error('adresse') is-invalid @enderror"
                               value="{{ old('adresse') }}" placeholder="Ex: Dakar, Plateau">
                        @error('adresse')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <hr class="my-4">

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success px-4">
                        Enregistrer
                    </button>
                    <a href="{{ route('membres.index') }}" class="btn btn-outline-secondary">
                        Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>

</div>
</div>
@endsection