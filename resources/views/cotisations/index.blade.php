@extends('layouts.app')
@section('title', 'Cotisations')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold">💳 Cotisations</h4>
        <small class="text-muted">{{ $cotisations->total() }} cotisation(s)</small>
    </div>
    <a href="{{ route('cotisations.create') }}" class="btn btn-success btn-sm">+ Nouvelle cotisation</a>
</div>

<x-filtres
    :route="route('cotisations.index')"
    :champs="['tontine', 'statut_cotisation', 'mode_paiement']"
/>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Référence</th>
                    <th>Membre</th>
                    <th>Tontine</th>
                    <th>Montant</th>
                    <th>Date</th>
                    <th>Mode</th>
                    <th>Type</th>
                    <th>Statut</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cotisations as $c)
                <tr>
                    <td><code class="small">{{ $c->reference }}</code></td>

                    {{-- ✅ Protection membre null --}}
                    <td>
                        @if($c->membre)
                            <a href="{{ route('membres.show', $c->membre->id) }}">
                                {{ $c->membre->nom_complet }}
                            </a>
                        @else
                            <span class="text-muted fst-italic">Membre supprimé</span>
                        @endif
                    </td>

                    {{-- ✅ Protection tontine null --}}
                    <td>
                        @if($c->tontine)
                            {{ $c->tontine->nom }}
                        @else
                            <span class="text-muted fst-italic">—</span>
                        @endif
                    </td>

                    <td class="fw-semibold">{{ number_format($c->montant, 0, ',', ' ') }} FCFA</td>
                    <td>{{ $c->date_paiement->format('d/m/Y') }}</td>
                    <td>
                        <span class="badge bg-light text-dark border">
                            {{ str_replace('_', ' ', $c->mode_paiement) }}
                        </span>
                    </td>
                    <td>
                        @if($c->est_reserve)
                            <span class="badge bg-info-subtle text-info">Réserve</span>
                        @else
                            <span class="badge bg-light text-dark border">Normal</span>
                        @endif
                    </td>
                    <td>
                        @if($c->statut === 'paye')
                            <span class="badge bg-success-subtle text-success">Payé</span>
                        @elseif($c->statut === 'retard')
                            <span class="badge bg-danger-subtle text-danger">Retard</span>
                        @else
                            <span class="badge bg-warning-subtle text-warning">En attente</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('cotisations.show', $c) }}"
                           class="btn btn-outline-info btn-action">Voir</a>
                        <form action="{{ route('cotisations.destroy', $c) }}" method="POST"
                              class="d-inline"
                              onsubmit="return confirm('Supprimer cette cotisation ?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-outline-danger btn-action">Supprimer</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center text-muted py-4">
                        Aucune cotisation enregistrée.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3 d-flex justify-content-center">
    {{ $cotisations->withQueryString()->links() }}
</div>
@endsection
