@extends('layouts.app')
@section('title', 'Nouveau tour')

@section('content')
<div class="row justify-content-center">
<div class="col-md-7">
    <div class="d-flex align-items-center mb-4 gap-2">
        <a href="{{ route('tours.index') }}" class="btn btn-sm btn-outline-secondary">← Retour</a>
        <h4 class="mb-0 fw-bold">Créer un tour</h4>
    </div>
    <div class="card">
        <div class="card-body p-4">
            <form action="{{ route('tours.store') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Tontine <span class="text-danger">*</span></label>
                        <select name="tontine_id" class="form-select @error('tontine_id') is-invalid @enderror">
                            <option value="">-- Sélectionner --</option>
                            @foreach($tontines as $t)
                                <option value="{{ $t->id }}" {{ old('tontine_id') == $t->id ? 'selected' : '' }}>
                                    {{ $t->nom }}
                                </option>
                            @endforeach
                        </select>
                        @error('tontine_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Bénéficiaire <span class="text-danger">*</span></label>
                        <select name="membre_id" class="form-select @error('membre_id') is-invalid @enderror">
                            <option value="">-- Sélectionner --</option>
                            @foreach($membres as $m)
                                <option value="{{ $m->id }}" {{ old('membre_id') == $m->id ? 'selected' : '' }}>
                                    {{ $m->nom_complet }}
                                </option>
                            @endforeach
                        </select>
                        @error('membre_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Numéro du tour <span class="text-danger">*</span></label>
                        <input type="number" name="numero_tour"
                               class="form-control @error('numero_tour') is-invalid @enderror"
                               value="{{ old('numero_tour') }}" min="1" placeholder="Ex: 1">
                        @error('numero_tour')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Date prévue <span class="text-danger">*</span></label>
                        <input type="date" name="date_prevue"
                               class="form-control @error('date_prevue') is-invalid @enderror"
                               value="{{ old('date_prevue') }}">
                        @error('date_prevue')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Statut</label>
                        <select name="statut" class="form-select">
                            <option value="en_attente" {{ old('statut') === 'en_attente' ? 'selected' : '' }}>En attente</option>
                            <option value="complete"   {{ old('statut') === 'complete'   ? 'selected' : '' }}>Complété</option>
                            <option value="reporte"    {{ old('statut') === 'reporte'    ? 'selected' : '' }}>Reporté</option>
                        </select>
                    </div>
                </div>

                <hr class="my-4">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success px-4">Créer</button>
                    <a href="{{ route('tours.index') }}" class="btn btn-outline-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
@endsection