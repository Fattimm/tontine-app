@extends('layouts.app')
@section('title', 'Modifier ' . $tontine->nom)

@section('content')
<div class="row justify-content-center">
<div class="col-md-7">
    <div class="d-flex align-items-center mb-4 gap-2">
        <a href="{{ route('tontines.index') }}" class="btn btn-sm btn-outline-secondary">← Retour</a>
        <h4 class="mb-0 fw-bold">Modifier : {{ $tontine->nom }}</h4>
    </div>
    <div class="card">
        <div class="card-body p-4">
            <form action="{{ route('tontines.update', $tontine) }}" method="POST">
                @csrf @method('PUT')
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold">Nom <span class="text-danger">*</span></label>
                        <input type="text" name="nom" class="form-control @error('nom') is-invalid @enderror"
                               value="{{ old('nom', $tontine->nom) }}">
                        @error('nom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nombre max de membres <span class="text-danger">*</span></label>
                        <input type="number" name="nombre_membres_max"
                            class="form-control @error('nombre_membres_max') is-invalid @enderror"
                            value="{{ old('nombre_membres_max', $tontine->nombre_membres_max) }}"
                            min="{{ $tontine->membres()->count() }}"
                            max="100">
                        <div class="form-text">
                            Actuellement {{ $tontine->membres()->count() }} membre(s) — minimum {{ $tontine->membres()->count() }}
                        </div>
                        @error('nombre_membres_max')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" class="form-control" rows="2">{{ old('description', $tontine->description) }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Montant à cotiser / période (FCFA) <span class="text-danger">*</span></label>
                        <input type="number" name="montant_cotisation" id="montant_cotisation"
                               class="form-control @error('montant_cotisation') is-invalid @enderror"
                               value="{{ old('montant_cotisation', $tontine->montant_cotisation) }}" min="100">
                        <div class="form-text">Ce que chaque membre paie par période</div>
                        @error('montant_cotisation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Montant du gain / tirage (FCFA) <span class="text-danger">*</span></label>
                        <input type="number" name="montant_gain" id="montant_gain"
                               class="form-control @error('montant_gain') is-invalid @enderror"
                               value="{{ old('montant_gain', $tontine->montant_gain) }}" min="100">
                        <div class="form-text text-success" id="gain-calcul"></div>
                        @error('montant_gain')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Fréquence</label>
                        <select name="frequence" class="form-select">
                            <option value="quotidien"    {{ $tontine->frequence === 'quotidien'    ? 'selected' : '' }}>Quotidien</option>
                            <option value="hebdomadaire" {{ $tontine->frequence === 'hebdomadaire' ? 'selected' : '' }}>Hebdomadaire</option>
                            <option value="mensuel"      {{ $tontine->frequence === 'mensuel'      ? 'selected' : '' }}>Mensuel</option>
                            <option value="trimestriel"  {{ $tontine->frequence === 'trimestriel'  ? 'selected' : '' }}>Trimestriel</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Date début</label>
                        <input type="date" name="date_debut" class="form-control"
                               value="{{ old('date_debut', $tontine->date_debut?->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Date fin</label>
                        <input type="date" name="date_fin" class="form-control"
                               value="{{ old('date_fin', $tontine->date_fin?->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Statut</label>
                        <select name="statut" class="form-select">
                            <option value="active"    {{ $tontine->statut === 'active'    ? 'selected' : '' }}>Active</option>
                            <option value="suspendue" {{ $tontine->statut === 'suspendue' ? 'selected' : '' }}>Suspendue</option>
                            <option value="terminee"  {{ $tontine->statut === 'terminee'  ? 'selected' : '' }}>Terminée</option>
                        </select>
                    </div>
                </div>
                <hr class="my-4">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-warning px-4">Mettre à jour</button>
                    <a href="{{ route('tontines.index') }}" class="btn btn-outline-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
@push('scripts')
<script>
(function () {
    const cotis  = document.getElementById('montant_cotisation');
    const nbMax  = document.querySelector('[name="nombre_membres_max"]');
    const gain   = document.getElementById('montant_gain');
    const info   = document.getElementById('gain-calcul');

    function calculer() {
        const c = parseFloat(cotis.value) || 0;
        const n = parseInt(nbMax.value)   || 0;
        if (c > 0 && n > 0) {
            const total = c * n;
            info.textContent = c.toLocaleString('fr-FR') + ' × ' + n + ' = ' + total.toLocaleString('fr-FR') + ' FCFA (calculé)';
        }
    }

    cotis.addEventListener('input', calculer);
    nbMax.addEventListener('input', calculer);
}());
</script>
@endpush

@endsection
