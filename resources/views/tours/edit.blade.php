@extends('layouts.app')
@section('title', 'Modifier le tour')

@section('content')
<div class="row justify-content-center">
<div class="col-md-7">
    <div class="d-flex align-items-center mb-4 gap-2">
        <a href="{{ route('tours.index') }}" class="btn btn-sm btn-outline-secondary">← Retour</a>
        <h4 class="mb-0 fw-bold">Modifier le tour #{{ $tour->numero_tour }}</h4>
    </div>
    <div class="card">
        <div class="card-body p-4">
            <form action="{{ route('tours.update', $tour) }}" method="POST">
                @csrf @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Tontine</label>
                        <input type="text" class="form-control" value="{{ $tour->tontine->nom }}" disabled>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Bénéficiaire <span class="text-danger">*</span></label>
                        <select name="membre_id" class="form-select">
                            @foreach($membres as $m)
                                <option value="{{ $m->id }}"
                                    {{ old('membre_id', $tour->membre_id) == $m->id ? 'selected' : '' }}>
                                    {{ $m->nom_complet }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Numéro du tour <span class="text-danger">*</span></label>
                        <input type="number" name="numero_tour" class="form-control"
                               value="{{ old('numero_tour', $tour->numero_tour) }}" min="1">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Date prévue <span class="text-danger">*</span></label>
                        <input type="date" name="date_prevue" class="form-control"
                               value="{{ old('date_prevue', $tour->date_prevue->format('Y-m-d')) }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Date effective</label>
                        <input type="date" name="date_effective" class="form-control"
                               value="{{ old('date_effective', $tour->date_effective?->format('Y-m-d')) }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Montant reçu (FCFA)</label>
                        <input type="number" name="montant_recu" class="form-control"
                               value="{{ old('montant_recu', $tour->montant_recu) }}" min="0">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Statut</label>
                        <select name="statut" class="form-select">
                            <option value="en_attente" {{ old('statut', $tour->statut) === 'en_attente' ? 'selected' : '' }}>En attente</option>
                            <option value="complete"   {{ old('statut', $tour->statut) === 'complete'   ? 'selected' : '' }}>Complété</option>
                            <option value="reporte"    {{ old('statut', $tour->statut) === 'reporte'    ? 'selected' : '' }}>Reporté</option>
                        </select>
                    </div>
                </div>

                <hr class="my-4">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-warning px-4">Mettre à jour</button>
                    <a href="{{ route('tours.index') }}" class="btn btn-outline-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
@endsection