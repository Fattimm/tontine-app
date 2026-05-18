{{-- Composant filtres dynamiques réutilisable --}}
{{-- Usage : <x-filtres :route="route('membres.index')" :champs="$champs" /> --}}

<div class="card mb-4">
    <div class="card-body py-2">
        <form method="GET" action="{{ $route }}" id="form-filtres">
            <div class="row g-2 align-items-center">

                {{-- Recherche texte --}}
                @if(in_array('search', $champs))
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control form-control-sm"
                           placeholder="{{ $placeholder ?? 'Rechercher...' }}"
                           value="{{ request('search') }}"
                           oninput="debounceSubmit()">
                </div>
                @endif

                {{-- Filtre statut membre --}}
                @if(in_array('statut_membre', $champs))
                <div class="col-md-2">
                    <select name="statut" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Tous statuts</option>
                        <option value="actif"   {{ request('statut') === 'actif'   ? 'selected' : '' }}>Actif</option>
                        <option value="inactif" {{ request('statut') === 'inactif' ? 'selected' : '' }}>Inactif</option>
                    </select>
                </div>
                @endif

                {{-- Filtre statut tontine --}}
                @if(in_array('statut_tontine', $champs))
                <div class="col-md-2">
                    <select name="statut" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Tous statuts</option>
                        <option value="active"    {{ request('statut') === 'active'    ? 'selected' : '' }}>Active</option>
                        <option value="suspendue" {{ request('statut') === 'suspendue' ? 'selected' : '' }}>Suspendue</option>
                        <option value="terminee"  {{ request('statut') === 'terminee'  ? 'selected' : '' }}>Terminée</option>
                    </select>
                </div>
                @endif

                {{-- Filtre statut cotisation --}}
                @if(in_array('statut_cotisation', $champs))
                <div class="col-md-2">
                    <select name="statut" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Tous statuts</option>
                        <option value="paye"       {{ request('statut') === 'paye'       ? 'selected' : '' }}>Payé</option>
                        <option value="en_attente" {{ request('statut') === 'en_attente' ? 'selected' : '' }}>En attente</option>
                        <option value="retard"     {{ request('statut') === 'retard'     ? 'selected' : '' }}>Retard</option>
                    </select>
                </div>
                @endif

                {{-- Filtre mode paiement --}}
                @if(in_array('mode_paiement', $champs))
                <div class="col-md-2">
                    <select name="mode_paiement" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Tous modes</option>
                        <option value="espece"       {{ request('mode_paiement') === 'espece'       ? 'selected' : '' }}>Espèce</option>
                        <option value="mobile_money" {{ request('mode_paiement') === 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                        <option value="virement"     {{ request('mode_paiement') === 'virement'     ? 'selected' : '' }}>Virement</option>
                    </select>
                </div>
                @endif

                {{-- Filtre par tontine --}}
                @if(in_array('tontine', $champs))
                <div class="col-md-3">
                    <select name="tontine_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Toutes les tontines</option>
                        {{-- ✅ whereNull exclut les soft deleted --}}
                        @foreach(\App\Models\Tontine::whereNull('deleted_at')->orderBy('nom')->get() as $t)
                            <option value="{{ $t->id }}" {{ request('tontine_id') == $t->id ? 'selected' : '' }}>
                                {{ $t->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                {{-- Filtre statut tour --}}
                @if(in_array('statut_tour', $champs))
                <div class="col-md-2">
                    <select name="statut" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Tous statuts</option>
                        <option value="en_attente" {{ request('statut') === 'en_attente' ? 'selected' : '' }}>En attente</option>
                        <option value="complete"   {{ request('statut') === 'complete'   ? 'selected' : '' }}>Complété</option>
                        <option value="reporte"    {{ request('statut') === 'reporte'    ? 'selected' : '' }}>Reporté</option>
                    </select>
                </div>
                @endif

                {{-- Filtre fréquence --}}
                @if(in_array('frequence', $champs))
                <div class="col-md-2">
                    <select name="frequence" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Toutes fréquences</option>
                        <option value="mensuel"      {{ request('frequence') === 'mensuel'      ? 'selected' : '' }}>Mensuel</option>
                        <option value="hebdomadaire" {{ request('frequence') === 'hebdomadaire' ? 'selected' : '' }}>Hebdomadaire</option>
                        <option value="trimestriel"  {{ request('frequence') === 'trimestriel'  ? 'selected' : '' }}>Trimestriel</option>
                    </select>
                </div>
                @endif

                {{-- Réinitialiser --}}
                @if(request()->hasAny(['search','statut','mode_paiement','frequence','tontine_id']))
                <div class="col-auto">
                    <a href="{{ $route }}" class="btn btn-outline-secondary btn-sm">✕ Réinitialiser</a>
                </div>
                @endif

            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
let debounceTimer;
function debounceSubmit() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        document.getElementById('form-filtres').submit();
    }, 400); // ✅ soumet 400ms après arrêt de frappe
}
</script>
@endpush
