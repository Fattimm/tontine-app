<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body        { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; }
        h1          { font-size: 18px; margin-bottom: 4px; }
        .subtitle   { color: #666; font-size: 11px; margin-bottom: 20px; }
        table       { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th          { background: #198754; color: #fff; padding: 7px 8px; text-align: left; font-size: 11px; }
        td          { padding: 6px 8px; border-bottom: 1px solid #e5e5e5; font-size: 11px; }
        tr:nth-child(even) td { background: #f9f9f9; }
        .badge-reserve  { background: #ffc107; color: #000; padding: 2px 6px; border-radius: 3px; font-size: 10px; }
        .badge-normal   { background: #198754; color: #fff; padding: 2px 6px; border-radius: 3px; font-size: 10px; }
        .total      { text-align: right; margin-top: 12px; font-weight: bold; font-size: 13px; }
        .footer     { margin-top: 30px; font-size: 10px; color: #999; text-align: center; }
    </style>
</head>
<body>

<h1>Liste des cotisations</h1>
<div class="subtitle">
    Exporté le {{ now()->translatedFormat('d F Y à H:i') }}
    @if($tontineFiltree) &nbsp;·&nbsp; Tontine : <strong>{{ $tontineFiltree->nom }}</strong> @endif
</div>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Membre</th>
            <th>Tontine</th>
            <th>Montant</th>
            <th>Date</th>
            <th>Mode</th>
            <th>Type</th>
        </tr>
    </thead>
    <tbody>
        @forelse($cotisations as $c)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $c->membre->nom_complet }}</td>
            <td>{{ $c->tontine->nom }}</td>
            <td>{{ number_format($c->montant, 0, ',', ' ') }} FCFA</td>
            <td>{{ $c->date_paiement->format('d/m/Y') }}</td>
            <td>{{ ucfirst(str_replace('_', ' ', $c->mode_paiement)) }}</td>
            <td>
                @if($c->est_reserve)
                    <span class="badge-reserve">Réserve</span>
                @else
                    <span class="badge-normal">Normal</span>
                @endif
            </td>
        </tr>
        @empty
        <tr><td colspan="7" style="text-align:center;color:#999;">Aucune cotisation</td></tr>
        @endforelse
    </tbody>
</table>

<div class="total">
    Total : {{ number_format($cotisations->sum('montant'), 0, ',', ' ') }} FCFA
    &nbsp;·&nbsp; {{ $cotisations->count() }} cotisation(s)
</div>

<div class="footer">{{ config('app.name') }} — Document généré automatiquement</div>

</body>
</html>
