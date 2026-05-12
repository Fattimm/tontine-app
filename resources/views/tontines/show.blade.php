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
        <div class="fs-4 fw-bold text-primary">{{ $stats['nb_membres'] }}</div>
        <div class="text-muted small">Membres</div>
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
            <div class="card-header bg-white fw-semibold">Membres de la tontine</div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Nom</th><th>Téléphone</th><th>Adhésion</th><th>Statut</th></tr></thead>
                    <tbody>
                        @forelse($membres as $m)
                        <tr>
                            <td>
                                {{-- ✅ Membre supprimé = grisé --}}
                                @if($m->deleted_at)
                                    <span class="text-muted fst-italic text-decoration-line-through">
                                        {{ $m->nom_complet }}
                                    </span>
                                    <span class="badge bg-danger-subtle text-danger ms-1">Supprimé</span>
                                @else
                                    <a href="{{ route('membres.show', $m) }}">{{ $m->nom_complet }}</a>
                                @endif
                            </td>
                            <td>{{ $m->telephone }}</td>
                            <td>{{ \Carbon\Carbon::parse($m->pivot->date_adhesion)->format('d/m/Y') }}</td>
                            <td>
                                @if($m->deleted_at)
                                    <span class="badge bg-danger-subtle text-danger">Inactif</span>
                                @else
                                    <span class="badge bg-success-subtle text-success">{{ $m->pivot->statut }}</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted py-3">Aucun membre.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-success">
            <div class="card-header bg-white fw-semibold">Actions rapides</div>
            <div class="card-body d-grid gap-2">
                <a href="{{ route('tontines.prochain-beneficiaire', $tontine) }}"
                   class="btn btn-success">🏆 Prochain bénéficiaire</a>
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
            <div class="card-header bg-white fw-semibold">Ajouter un membre</div>
            <div class="card-body">
                <form action="{{ route('tontines.ajouter-membre', $tontine) }}" method="POST">
                    @csrf
                    <select name="membre_id" class="form-select form-select-sm mb-2">
                        <option value="">-- Sélectionner --</option>
                        @foreach(\App\Models\Membre::actifs()->orderBy('nom')->get() as $m)
                            <option value="{{ $m->id }}">{{ $m->nom_complet }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-success btn-sm w-100">Ajouter</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
