@extends('layouts.app')
@section('title', 'Nouvelle cotisation')

@section('content')
<div class="row justify-content-center">
<div class="col-md-7">

    @php $retour = auth()->user()->isMembre() ? route('dashboard') : route('cotisations.index'); @endphp
    <div class="d-flex align-items-center mb-4 gap-2">
        <a href="{{ $retour }}" class="btn btn-sm btn-outline-secondary">← Retour</a>
        <h4 class="mb-0 fw-bold">Enregistrer une cotisation</h4>
    </div>

    <div class="card">
        <div class="card-body p-4">
            <form action="{{ route('cotisations.store') }}" method="POST">
                @csrf

                <div class="row g-3">
                    {{-- Membre --}}
                    @if($monMembre)
                        {{-- Vue membre : champ verrouillé --}}
                        <input type="hidden" name="membre_id" value="{{ $monMembre->id }}">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Membre</label>
                            <input type="text" class="form-control bg-light"
                                   value="{{ $monMembre->nom_complet }}" disabled>
                        </div>
                    @else
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
                    @endif

                    {{-- Tontine --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Tontine <span class="text-danger">*</span></label>
                        @if($tontines->isEmpty())
                            <div class="alert alert-warning py-2 mb-0">
                                Vous n'êtes inscrit à aucune tontine active pour le moment.
                            </div>
                        @else
                            <select id="tontine_select" name="tontine_id"
                                    class="form-select @error('tontine_id') is-invalid @enderror">
                                <option value="">-- Sélectionner --</option>
                                @foreach($tontines as $t)
                                    <option value="{{ $t->id }}"
                                            data-montant="{{ (int) $t->montant_cotisation }}"
                                            {{ old('tontine_id', $monMembre && $tontines->count() === 1 ? $t->id : '') == $t->id ? 'selected' : '' }}>
                                        {{ $t->nom }} — {{ number_format($t->montant_cotisation, 0, ',', ' ') }} FCFA/période
                                    </option>
                                @endforeach
                            </select>
                            @error('tontine_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        @endif
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Montant à payer (FCFA)</label>
                        <input type="number" id="montant_input" name="montant"
                               class="form-control bg-light @error('montant') is-invalid @enderror"
                               value="{{ old('montant') }}" readonly
                               placeholder="Sélectionnez une tontine">
                        <div class="form-text">Fixé automatiquement par la tontine.</div>
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

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nature <span class="text-danger">*</span></label>
                        <div class="d-flex flex-column gap-2 mt-1">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="est_reserve"
                                       id="nature_normale" value="0"
                                       {{ old('est_reserve', '0') === '0' ? 'checked' : '' }}>
                                <label class="form-check-label" for="nature_normale">
                                    <strong>Normale</strong>
                                    <span class="text-muted small d-block">Cotisation pour la période en cours</span>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="est_reserve"
                                       id="nature_reserve" value="1"
                                       {{ old('est_reserve') === '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="nature_reserve">
                                    <strong>Réserve</strong>
                                    <span class="text-muted small d-block">Paiement en avance pour la période suivante</span>
                                </label>
                            </div>
                        </div>
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
                    <a href="{{ $retour }}" class="btn btn-outline-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
@push('scripts')
<script>
(function () {
    const select = document.getElementById('tontine_select');
    const input  = document.getElementById('montant_input');
    if (!select || !input) return;

    function syncMontant() {
        const opt = select.options[select.selectedIndex];
        const montant = opt ? opt.dataset.montant : '';
        input.value = montant || '';
    }

    select.addEventListener('change', syncMontant);
    syncMontant(); // initialiser au chargement si tontine déjà sélectionnée
}());
</script>
@endpush

@endsection
