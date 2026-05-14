@extends('layouts.app')
@section('title', $tontine->nom)

@section('content')
<div class="d-flex align-items-center mb-4 gap-2">
    <a href="{{ route('tontines.index') }}" class="btn btn-sm btn-outline-secondary">← Retour</a>
    <h4 class="mb-0 fw-bold">{{ $tontine->nom }}</h4>
    <span class="badge bg-success-subtle text-success">{{ ucfirst($tontine->statut) }}</span>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card text-center"><div class="card-body py-3">
        <div class="fs-4 fw-bold text-success">{{ number_format($tontine->montant_cotisation, 0, ',', ' ') }}</div>
        <div class="text-muted small">FCFA / cotisation</div>
    </div></div></div>
    <div class="col-md-3"><div class="card text-center"><div class="card-body py-3">
        <div class="fs-4 fw-bold text-primary">
            {{ $stats['nb_membres'] }}
            <span class="text-muted fs-6">/ {{ $tontine->nombre_membres_max }}</span>
        </div>
        <div class="text-muted small">Membres</div>
        {{-- ✅ Barre de progression --}}
        <div class="progress mt-2" style="height:6px">
            <div class="progress-bar bg-primary"
                style="width: {{ ($stats['nb_membres'] / $tontine->nombre_membres_max) * 100 }}%">
            </div>
        </div>
        @if($tontine->placesRestantes() > 0)
            <div class="text-success small mt-1">{{ $tontine->placesRestantes() }} place(s) restante(s)</div>
        @else
            <div class="text-danger small mt-1">Complet</div>
        @endif
    </div></div></div>
    <div class="col-md-3"><div class="card text-center"><div class="card-body py-3">
        <div class="fs-4 fw-bold text-warning">{{ $stats['nb_tours_attente'] }}</div>
        <div class="text-muted small">Tours en attente</div>
    </div></div></div>
    <div class="col-md-3"><div class="card text-center"><div class="card-body py-3">
        <div class="fs-4 fw-bold">{{ number_format($stats['total_collecte'], 0, ',', ' ') }}</div>
        <div class="text-muted small">FCFA collectés</div>
    </div></div></div>
</div>


<div class="row g-3">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold">
                    Membres de la tontine
                </span>
                <span class="badge bg-secondary">
                    {{ $membres->total() }} / {{ $tontine->nombre_membres_max }}
                </span>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nom</th>
                            <th>Téléphone</th>
                            <th>Adhésion</th>
                            <th>Statut</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($membres as $index => $m)
                        <tr>
                            <td class="text-muted small">
                                {{ ($membres->currentPage() - 1) * $membres->perPage() + $loop->iteration }}
                            </td>
                            <td>
                                @if($m->deleted_at)
                                    <span class="text-muted fst-italic text-decoration-line-through">
                                        {{ $m->nom_complet }}
                                    </span>
                                    <span class="badge bg-danger-subtle text-danger ms-1">Supprimé</span>
                                @else
                                    <a href="{{ route('membres.show', $m) }}">
                                        {{ $m->nom_complet }}
                                    </a>
                                @endif
                            </td>
                            <td>{{ $m->telephone }}</td>
                            <td class="text-muted small">
                                {{ \Carbon\Carbon::parse($m->pivot->date_adhesion)->format('d/m/Y') }}
                            </td>
                            <td>
                                @if($m->deleted_at)
                                    <span class="badge bg-danger-subtle text-danger">Supprimé</span>
                                @elseif($m->pivot->statut === 'actif')
                                    <span class="badge bg-success-subtle text-success">Actif</span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning">Suspendu</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @if(!$m->deleted_at)
                                    {{-- ✅ Voir détail membre dans cette tontine --}}
                                    <a href="{{ route('membres.tontine-detail', [$m, $tontine]) }}"
                                    class="btn btn-outline-info btn-action">Détail</a>

                                    {{-- ✅ Retirer le membre --}}
                                    <form action="{{ route('tontines.retirer-membre', [$tontine, $m]) }}"
                                        method="POST" class="d-inline"
                                        onsubmit="return confirm('Retirer {{ $m->nom_complet }} de cette tontine ?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-outline-danger btn-action">Retirer</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                Aucun membre dans cette tontine.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ✅ Pagination --}}
            @if($membres->hasPages())
            <div class="card-footer bg-white d-flex justify-content-center py-2">
                {{ $membres->withQueryString()->links() }}
            </div>
            @endif

        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-success">
            <div class="card-header bg-white fw-semibold">Actions rapides</div>
            <div class="card-body d-grid gap-2">
                {{-- ✅ Bouton tirage conditionnel --}}
                @php
                    $pret = app(\App\Services\TontineService::class)->tousOntCotiseCeMois($tontine);
                @endphp

                @if($pret)
                    <a href="{{ route('tontines.tirage', $tontine) }}"
                    class="btn btn-success fw-bold">
                        🎲 Lancer le tirage du mois
                    </a>
                @else
                    <a href="{{ route('tontines.tirage', $tontine) }}"
                    class="btn btn-outline-secondary">
                        🎲 Voir le tirage
                    </a>
                @endif

                <a href="{{ route('cotisations.par-tontine', $tontine) }}"
                   class="btn btn-outline-primary">💳 Voir les cotisations</a>
                <a href="{{ route('cotisations.create') }}"
                   class="btn btn-outline-success">+ Enregistrer cotisation</a>
                <a href="{{ route('tontines.edit', $tontine) }}"
                   class="btn btn-outline-warning">✏️ Modifier</a>
            </div>
        </div>

        {{-- Ajouter un membre --}}
        <div class="card mt-3">
            <div class="card-header bg-white fw-semibold d-flex justify-content-between">
                <span>Ajouter un membre</span>
                <span class="badge {{ $tontine->peutAjouterMembre() ? 'bg-success' : 'bg-danger' }}">
                    {{ $tontine->membres()->count() }}/{{ $tontine->nombre_membres_max }}
                </span>
            </div>
            <div class="card-body">
                @if($tontine->peutAjouterMembre())
                    <form action="{{ route('tontines.ajouter-membre', $tontine) }}" method="POST">
                        @csrf
                        <select name="membre_id" class="form-select form-select-sm mb-2">
                            <option value="">-- Sélectionner --</option>
                            @foreach(\App\Models\Membre::actifs()->orderBy('nom')->get() as $m)
                                @if(!$tontine->membres->contains($m->id))
                                    <option value="{{ $m->id }}">{{ $m->nom_complet }}</option>
                                @endif
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-success btn-sm w-100">Ajouter</button>
                    </form>
                @else
                    <div class="alert alert-warning mb-0 text-center">
                        🔒 Tontine complète ({{ $tontine->nombre_membres_max }} membres max)
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
