<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body        { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; }
        h1          { font-size: 18px; margin-bottom: 4px; }
        h2          { font-size: 14px; margin: 20px 0 6px; border-bottom: 2px solid #198754; padding-bottom: 4px; color: #198754; }
        .subtitle   { color: #666; font-size: 11px; margin-bottom: 16px; }
        .info-grid  { display: table; width: 100%; margin-bottom: 10px; }
        .info-row   { display: table-row; }
        .info-label { display: table-cell; width: 40%; font-weight: bold; padding: 3px 0; color: #555; }
        .info-value { display: table-cell; padding: 3px 0; }
        table       { width: 100%; border-collapse: collapse; margin-top: 6px; }
        th          { background: #198754; color: #fff; padding: 6px 8px; text-align: left; font-size: 11px; }
        td          { padding: 5px 8px; border-bottom: 1px solid #e5e5e5; font-size: 11px; }
        tr:nth-child(even) td { background: #f9f9f9; }
        .stat-box   { display: inline-block; width: 22%; text-align: center; border: 1px solid #ddd;
                      padding: 8px; margin-right: 2%; border-radius: 4px; }
        .stat-val   { font-size: 20px; font-weight: bold; color: #198754; }
        .stat-lbl   { font-size: 10px; color: #666; margin-top: 2px; }
        .footer     { margin-top: 30px; font-size: 10px; color: #999; text-align: center; }
        .badge-complete { background: #198754; color: #fff; padding: 2px 6px; border-radius: 3px; font-size: 10px; }
        .badge-attente  { background: #ffc107; color: #000; padding: 2px 6px; border-radius: 3px; font-size: 10px; }
    </style>
</head>
<body>

<h1>Récapitulatif — {{ $tontine->nom }}</h1>
<div class="subtitle">Exporté le {{ now()->translatedFormat('d F Y à H:i') }}</div>

{{-- Infos générales --}}
<h2>Informations générales</h2>
<div class="info-grid">
    <div class="info-row">
        <div class="info-label">Organisateur</div>
        <div class="info-value">{{ $tontine->organisateur->name }}</div>
    </div>
    <div class="info-row">
        <div class="info-label">Fréquence</div>
        <div class="info-value">{{ ucfirst($tontine->frequence) }}</div>
    </div>
    <div class="info-row">
        <div class="info-label">Montant cotisation</div>
        <div class="info-value">{{ number_format($tontine->montant_cotisation, 0, ',', ' ') }} FCFA</div>
    </div>
    <div class="info-row">
        <div class="info-label">Date début</div>
        <div class="info-value">{{ $tontine->date_debut->format('d/m/Y') }}</div>
    </div>
    @if($tontine->date_fin)
    <div class="info-row">
        <div class="info-label">Date fin</div>
        <div class="info-value">{{ $tontine->date_fin->format('d/m/Y') }}</div>
    </div>
    @endif
    <div class="info-row">
        <div class="info-label">Statut</div>
        <div class="info-value">{{ ucfirst($tontine->statut) }}</div>
    </div>
</div>

{{-- Statistiques --}}
<h2>Statistiques</h2>
<div style="margin-bottom:16px;">
    <div class="stat-box">
        <div class="stat-val">{{ $stats['nb_membres'] }}</div>
        <div class="stat-lbl">Membres</div>
    </div>
    <div class="stat-box">
        <div class="stat-val">{{ $stats['nb_tours_completes'] }}</div>
        <div class="stat-lbl">Tours complétés</div>
    </div>
    <div class="stat-box">
        <div class="stat-val">{{ $cotisations->count() }}</div>
        <div class="stat-lbl">Cotisations</div>
    </div>
    <div class="stat-box">
        <div class="stat-val">{{ number_format($cotisations->sum('montant'), 0, ',', ' ') }}</div>
        <div class="stat-lbl">Total collecté (FCFA)</div>
    </div>
</div>

{{-- Membres --}}
<h2>Membres ({{ $tontine->membres->count() }})</h2>
<table>
    <thead>
        <tr><th>Membre</th><th>Téléphone</th><th>Date d'adhésion</th></tr>
    </thead>
    <tbody>
        @foreach($tontine->membres as $m)
        <tr>
            <td>{{ $m->nom_complet }}</td>
            <td>{{ $m->telephone }}</td>
            <td>{{ \Carbon\Carbon::parse($m->pivot->date_adhesion)->format('d/m/Y') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

{{-- Cotisations --}}
<h2>Cotisations ({{ $cotisations->count() }})</h2>
<table>
    <thead>
        <tr><th>Membre</th><th>Montant</th><th>Date</th><th>Mode</th></tr>
    </thead>
    <tbody>
        @forelse($cotisations as $c)
        <tr>
            <td>{{ $c->membre->nom_complet }}</td>
            <td>{{ number_format($c->montant, 0, ',', ' ') }} FCFA</td>
            <td>{{ $c->date_paiement->format('d/m/Y') }}</td>
            <td>{{ ucfirst(str_replace('_', ' ', $c->mode_paiement)) }}</td>
        </tr>
        @empty
        <tr><td colspan="4" style="text-align:center;color:#999;">Aucune cotisation</td></tr>
        @endforelse
    </tbody>
</table>

{{-- Tours --}}
<h2>Tours / Tirages ({{ $tontine->tours->count() }})</h2>
<table>
    <thead>
        <tr><th>Tour</th><th>Bénéficiaire</th><th>Date prévue</th><th>Statut</th><th>Montant reçu</th></tr>
    </thead>
    <tbody>
        @forelse($tontine->tours as $t)
        <tr>
            <td>{{ $t->numero_tour }}</td>
            <td>{{ $t->membre->nom_complet }}</td>
            <td>{{ $t->date_prevue->format('d/m/Y') }}</td>
            <td>
                @if($t->statut === 'complete')
                    <span class="badge-complete">Complété</span>
                @else
                    <span class="badge-attente">En attente</span>
                @endif
            </td>
            <td>{{ $t->montant_recu ? number_format($t->montant_recu, 0, ',', ' ') . ' FCFA' : '—' }}</td>
        </tr>
        @empty
        <tr><td colspan="5" style="text-align:center;color:#999;">Aucun tour</td></tr>
        @endforelse
    </tbody>
</table>

<div class="footer">{{ config('app.name') }} — Document généré automatiquement</div>

</body>
</html>
