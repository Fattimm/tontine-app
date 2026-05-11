@extends('layouts.app')
@section('title', 'Nouvelle cotisation')

@section('content')
<div class="row justify-content-center">
<div class="col-md-7">

    <div class="d-flex align-items-center mb-4 gap-2">
        <a href="{{ route('cotisations.index') }}" class="btn btn-sm btn-outline-secondary">← Retour</a>
        <h4 class="mb-0 fw-bold">Enregistrer une cotisation</h4>
    </div>

    <div class="card">
        <div class="card-body p-4">
            <form action="{{ route('cotisations.store') }}" method="POST">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Membre <span class="text-danger">*</span></label>
                        <select name="membre_id" class="form-select @error('membre_id') is-invalid @enderror">
                            <option value="">-- Sélectionner --</option>
                            @foreach($membres as $m)
                                <option value="{{ $m->id }}"
                                    {{ (old('membre_id', request('membre_id')) == $m->id) ? 'selected' : '' }}>
                                    {{ $m->nom_complet }} — {{ $m->telephone }}
                                </option>
                            @endforeach
                        </select>
                        @error('membre_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Tontine <span class="text-danger">*</span></label>
                        <select name="tontine_id" class="form-select @error('tontine_id') is-invalid @enderror">
                            <option value="">-- Sélectionner --</option>
                            @foreach($tontines as $t)
                                <option value="{{ $t->id }}" {{ old('tontine_id') == $t->id ? 'selected' : '' }}>
                                    {{ $t->nom }} ({{ number_format($t->montant_cotisation, 0, ',', ' ') }} FCFA)
                                </option>
                            @endforeach
                        </select>
                        @error('tontine_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Tour (optionnel)</label>
                        <select name="tour_id" class="form-select">
                            <option value="">-- Aucun tour --</option>
                            @foreach($tours as $tour)
                                <option value="{{ $tour->id }}" {{ old('tour_id') == $tour->id ? 'selected' : '' }}>
                                    Tour #{{ $tour->numero_tour }} — {{ $tour->tontine->nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Montant (FCFA) <span class="text-danger">*</span></label>
                        <input type="number" name="montant"
                               class="form-control @error('montant') is-invalid @enderror"
                               value="{{ old('montant') }}" placeholder="Ex: 25000" min="100">
                        @error('montant')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Date de paiement <span class="text-danger">*</span></label>
                        <input type="date" name="date_paiement"
                               class="form-control @error('date_paiement') is-invalid @enderror"
                               value="{{ old('date_paiement', date('Y-m-d')) }}"
                               max="{{ date('Y-m-d') }}">
                        @error('date_paiement')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Mode de paiement <span class="text-danger">*</span></label>
                        <select name="mode_paiement" class="form-select @error('mode_paiement') is-invalid @enderror">
                            <option value="espece"       {{ old('mode_paiement') === 'espece'       ? 'selected' : '' }}>Espèce</option>
                            <option value="mobile_money" {{ old('mode_paiement') === 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                            <option value="virement"     {{ old('mode_paiement') === 'virement'     ? 'selected' : '' }}>Virement</option>
                        </select>
                        @error('mode_paiement')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Notes</label>
                        <textarea name="notes" class="form-control" rows="2"
                                  placeholder="Remarques éventuelles...">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <hr class="my-4">

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success px-4">Enregistrer</button>
                    <a href="{{ route('cotisations.index') }}" class="btn btn-outline-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
@endsection
