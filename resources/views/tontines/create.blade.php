@extends('layouts.app')
@section('title', 'Nouvelle tontine')

@section('content')
<div class="row justify-content-center">
<div class="col-md-7">
    <div class="d-flex align-items-center mb-4 gap-2">
        <a href="{{ route('tontines.index') }}" class="btn btn-sm btn-outline-secondary">← Retour</a>
        <h4 class="mb-0 fw-bold">Créer une tontine</h4>
    </div>
    <div class="card">
        <div class="card-body p-4">
            <form action="{{ route('tontines.store') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold">Nom <span class="text-danger">*</span></label>
                        <input type="text" name="nom" class="form-control @error('nom') is-invalid @enderror"
                               value="{{ old('nom') }}" placeholder="Ex: Tontine Dakar 2026">
                        @error('nom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Nombre max de membres <span class="text-danger">*</span>
                        </label>
                        <input type="number" name="nombre_membres_max"
                            class="form-control @error('nombre_membres_max') is-invalid @enderror"
                            value="{{ old('nombre_membres_max', 12) }}"
                            min="2" max="100"
                            placeholder="Ex: 12">
                        <div class="form-text">Entre 2 et 100 membres</div>
                        @error('nombre_membres_max')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" class="form-control" rows="2"
                                  placeholder="Description optionnelle...">{{ old('description') }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Montant à cotiser / période (FCFA) <span class="text-danger">*</span></label>
                        <input type="number" name="montant_cotisation" id="montant_cotisation"
                               class="form-control @error('montant_cotisation') is-invalid @enderror"
                               value="{{ old('montant_cotisation') }}" placeholder="Ex: 5000" min="100">
                        <div class="form-text">Ce que chaque membre paie par période</div>
                        @error('montant_cotisation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Montant du gain / tirage (FCFA) <span class="text-danger">*</span></label>
                        <input type="number" name="montant_gain" id="montant_gain"
                               class="form-control @error('montant_gain') is-invalid @enderror"
                               value="{{ old('montant_gain') }}" placeholder="Calculé automatiquement" min="100">
                        <div class="form-text text-success" id="gain-calcul"></div>
                        @error('montant_gain')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Fréquence <span class="text-danger">*</span></label>
                        <select name="frequence" class="form-select @error('frequence') is-invalid @enderror">
                            <option value="quotidien"    {{ old('frequence') === 'quotidien'    ? 'selected' : '' }}>Quotidien</option>
                            <option value="hebdomadaire" {{ old('frequence') === 'hebdomadaire' ? 'selected' : '' }}>Hebdomadaire</option>
                            <option value="mensuel"      {{ old('frequence') === 'mensuel'      ? 'selected' : '' }}>Mensuel</option>
                            <option value="trimestriel"  {{ old('frequence') === 'trimestriel'  ? 'selected' : '' }}>Trimestriel</option>
                        </select>
                        @error('frequence')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Date de début <span class="text-danger">*</span></label>
                        <input type="date" name="date_debut"
                               class="form-control @error('date_debut') is-invalid @enderror"
                               value="{{ old('date_debut', date('Y-m-d')) }}">
                        @error('date_debut')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Date de fin</label>
                        <input type="date" name="date_fin" class="form-control"
                               value="{{ old('date_fin') }}">
                    </div>
                </div>
                <hr class="my-4">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success px-4">Créer</button>
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
            gain.value  = total;
            info.textContent = c.toLocaleString('fr-FR') + ' × ' + n + ' membres = ' + total.toLocaleString('fr-FR') + ' FCFA';
        } else {
            info.textContent = '';
        }
    }

    cotis.addEventListener('input', calculer);
    nbMax.addEventListener('input', calculer);
    calculer();
}());
</script>
@endpush

@endsection
