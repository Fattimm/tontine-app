@extends('layouts.app')
@section('title', 'Tirage — ' . $tontine->nom)

@section('content')
<div class="d-flex align-items-center mb-4 gap-2">
    <a href="{{ route('tontines.show', $tontine) }}" class="btn btn-sm btn-outline-secondary">← Retour</a>
    <h4 class="mb-0 fw-bold">🎲 Tirage — {{ $tontine->nom }}</h4>
</div>

<div class="row justify-content-center">
<div class="col-md-8">

    {{-- Statut cotisations ce mois --}}
    <div class="card mb-4">
        <div class="card-header bg-white fw-semibold">
            État des cotisations — {{ now()->translatedFormat('F Y') }}
        </div>
        <div class="card-body">
            <div class="row g-2">
                @foreach($membresAvecStatut as $m)
                <div class="col-md-6">
                    <div class="d-flex align-items-center gap-2 p-2 rounded border
                        {{ $m->a_cotise ? 'border-success bg-success-subtle' : 'border-warning bg-warning-subtle' }}">
                        <span>{{ $m->a_cotise ? '✅' : '⏳' }}</span>
                        <span class="fw-semibold">{{ $m->nom_complet }}</span>
                        <span class="badge ms-auto {{ $m->a_cotise ? 'bg-success' : 'bg-warning text-dark' }}">
                            {{ $m->a_cotise ? 'Cotisé' : 'En attente' }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>

            @if(!$peutFaireTirage && !$tourEnAttente)
                <div class="alert alert-warning mt-3 mb-0">
                    ⚠️ Le tirage sera disponible quand tous les membres auront cotisé pour cette période.
                </div>
            @endif
        </div>
    </div>

    {{-- Zone principale --}}
    <div class="card shadow-sm">
        <div class="card-body p-4 text-center">

            @if(session('gagnant_nom'))
                {{-- Résultat --}}
                <div class="py-3">
                    <div class="display-1 mb-2 anim-bounce">🏆</div>
                    <h2 class="fw-bold text-success anim-apparaitre">{{ session('gagnant_nom') }}</h2>
                    <p class="text-muted fs-5">est le bénéficiaire de cette période !</p>
                    <div class="alert alert-success mt-3 d-inline-block px-4">
                        Il doit recevoir les fonds avant le
                        <strong>{{ $tourEnAttente?->date_prevue->format('d/m/Y') ?? now()->addDays(2)->format('d/m/Y') }}</strong>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('tours.index') }}" class="btn btn-success px-4">Voir dans les tirages →</a>
                        <a href="{{ route('tontines.show', $tontine) }}" class="btn btn-outline-secondary ms-2">Retour à la tontine</a>
                    </div>
                </div>

            @elseif($tourEnAttente)
                @php $delaiDepasse = $tourEnAttente->date_prevue->endOfDay()->isPast(); @endphp
                <div class="py-3">
                    @if($delaiDepasse)
                        <div class="display-1 mb-2">🚨</div>
                        <h4 class="fw-bold text-danger">Délai dépassé — versement en retard</h4>
                        <div class="alert alert-danger mt-2 px-4">
                            Le versement à <strong>{{ $tourEnAttente->membre->nom_complet }}</strong>
                            était prévu le <strong>{{ $tourEnAttente->date_prevue->format('d/m/Y') }}</strong>
                            et n'a pas encore été confirmé.
                            <div class="mt-1 small">⚠️ Le prochain tirage est bloqué jusqu'à confirmation.</div>
                        </div>
                    @else
                        <div class="display-1 mb-2">⏳</div>
                        <h4 class="fw-bold text-warning">En attente de versement</h4>
                        <div class="alert alert-warning d-inline-block px-4 mt-2">
                            <strong>{{ $tourEnAttente->membre->nom_complet }}</strong>
                            doit recevoir les fonds avant le
                            <strong>{{ $tourEnAttente->date_prevue->format('d/m/Y') }}</strong>
                        </div>
                    @endif

                    <p class="text-muted small mt-3">
                        Une fois le versement effectué, confirmez ci-dessous pour débloquer le prochain tirage.
                    </p>
                    <form action="{{ route('tours.confirmer', $tourEnAttente) }}" method="POST"
                          data-confirm="Confirmer que {{ $tourEnAttente->membre->nom_complet }} a bien reçu sa part ? Cette action est définitive."
                          data-confirm-titre="Confirmer le versement"
                          data-confirm-type="success"
                          data-confirm-btn="Oui, confirmer">
                        @csrf @method('PATCH')
                        <button type="submit"
                                class="btn btn-lg px-5 fw-bold shadow-sm {{ $delaiDepasse ? 'btn-danger' : 'btn-success' }}">
                            ✅ Confirmer que {{ $tourEnAttente->membre->nom_complet }} a reçu sa part
                        </button>
                    </form>
                </div>

            @elseif($eligibles->isEmpty())
                <div class="py-4">
                    <div class="display-1 mb-3">🎉</div>
                    <h4 class="text-success fw-bold">Tontine terminée !</h4>
                    <p class="text-muted">Tous les membres ont bénéficié de la tontine.</p>
                    <a href="{{ route('tontines.show', $tontine) }}" class="btn btn-outline-secondary mt-2">Retour</a>
                </div>

            @else
                {{-- Roue --}}
                <h5 class="fw-bold mb-1">{{ $eligibles->count() }} membre(s) éligible(s)</h5>
                <p class="text-muted small mb-4">L'organisateur lance la roue — elle détermine le bénéficiaire de cette période.</p>

                {{-- Pointeur + Canvas --}}
                <div class="d-flex justify-content-center mb-1">
                    <div class="tirage-arrow">▼</div>
                </div>
                <div class="d-flex justify-content-center mb-4">
                    <canvas id="wheel-canvas" width="360" height="360" class="canvas-roue"></canvas>
                </div>

                {{-- Nom mis en avant pendant l'animation --}}
                <div id="nom-anime" class="fs-3 fw-bold text-success mb-3 tirage-nom"></div>

                @if($peutFaireTirage)
                    <button type="button" id="btn-tirage"
                            onclick="lancerRoue()"
                            class="btn btn-success btn-lg px-5 fw-bold shadow-sm">
                        🎲 Lancer le tirage
                    </button>
                    <form action="{{ route('tontines.tirage.executer', $tontine) }}"
                          method="POST" id="form-tirage">
                        @csrf
                    </form>
                @else
                    <button class="btn btn-secondary btn-lg px-5" disabled>
                        🔒 En attente des cotisations
                    </button>
                @endif
            @endif

        </div>
    </div>

    {{-- Historique --}}
    @if($tontine->tours->count() > 0)
    <div class="card mt-4">
        <div class="card-header bg-white fw-semibold">
            Historique des bénéficiaires
            <span class="badge bg-secondary ms-2">{{ $tontine->tours->count() }} tour(s)</span>
        </div>
        <div class="card-body p-0">
            <table class="table table-sm mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Bénéficiaire</th>
                        <th>Versement prévu</th>
                        <th>Montant</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tontine->tours()->with('membre')->orderBy('numero_tour')->get() as $tour)
                    <tr>
                        <td class="text-muted">{{ $tour->numero_tour }}</td>
                        <td class="fw-semibold">{{ $tour->membre->nom_complet }}</td>
                        <td>{{ $tour->date_prevue->format('d/m/Y') }}</td>
                        <td>
                            {{ number_format(
                                $tontine->montant_gain ?? ($tontine->montant_cotisation * $tontine->membres()->count()),
                                0, ',', ' '
                            ) }} FCFA
                        </td>
                        <td>
                            @if($tour->statut === 'complete')
                                <span class="badge bg-success-subtle text-success">✅ Complété</span>
                            @elseif($tour->statut === 'en_attente')
                                <span class="badge bg-info-subtle text-info">⏳ En attente</span>
                            @else
                                <span class="badge bg-warning-subtle text-warning">Reporté</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const canvas = document.getElementById('wheel-canvas');
    if (!canvas) return;

    const ctx  = canvas.getContext('2d');
    const W    = canvas.width;
    const H    = canvas.height;
    const cx   = W / 2;
    const cy   = H / 2;
    const R    = Math.min(cx, cy) - 12;

    const noms   = @json($eligibles->pluck('nom_complet')->values());
    const n      = noms.length;
    const colors = [
        '#198754','#0d6efd','#dc3545','#fd7e14',
        '#6f42c1','#20c997','#e83e8c','#0dcaf0',
        '#ffc107','#495057'
    ];

    // Rotation initiale : premier segment pointe vers le haut (−π/2)
    let currentRot = -Math.PI / 2;
    let enCours    = false;

    function dessinerRoue(rotation) {
        ctx.clearRect(0, 0, W, H);

        const seg = (2 * Math.PI) / n;

        for (let i = 0; i < n; i++) {
            const debut = rotation + i * seg;
            const fin   = debut + seg;

            // Segment coloré
            ctx.beginPath();
            ctx.moveTo(cx, cy);
            ctx.arc(cx, cy, R, debut, fin);
            ctx.closePath();
            ctx.fillStyle = colors[i % colors.length];
            ctx.fill();
            ctx.strokeStyle = '#fff';
            ctx.lineWidth   = 2;
            ctx.stroke();

            // Texte dans le segment
            ctx.save();
            ctx.translate(cx, cy);
            ctx.rotate(debut + seg / 2);
            ctx.textAlign    = 'right';
            ctx.fillStyle    = '#fff';
            ctx.font         = 'bold 13px system-ui, sans-serif';
            ctx.shadowColor  = 'rgba(0,0,0,.4)';
            ctx.shadowBlur   = 3;
            const label = noms[i].length > 14 ? noms[i].slice(0, 13) + '…' : noms[i];
            ctx.fillText(label, R - 12, 5);
            ctx.restore();
        }

        // Disque central
        ctx.beginPath();
        ctx.arc(cx, cy, 24, 0, 2 * Math.PI);
        ctx.fillStyle   = '#fff';
        ctx.shadowColor = 'rgba(0,0,0,.2)';
        ctx.shadowBlur  = 6;
        ctx.fill();
        ctx.shadowBlur  = 0;
        ctx.strokeStyle = '#dee2e6';
        ctx.lineWidth   = 2;
        ctx.stroke();

        // Icône centre
        ctx.font         = '18px system-ui';
        ctx.textAlign    = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillStyle    = '#198754';
        ctx.fillText('★', cx, cy);
    }

    dessinerRoue(currentRot);

    window.lancerRoue = function () {
        if (enCours) return;
        enCours = true;

        const btn = document.getElementById('btn-tirage');
        btn.disabled    = true;
        btn.textContent = '⏳ En cours…';

        // Nombre de tours + atterrissage aléatoire
        const toursSupp  = 6 + Math.floor(Math.random() * 4);   // 6–9 tours complets
        const atterrissage = Math.random() * Math.PI * 2;
        const totalAngle = toursSupp * Math.PI * 2 + atterrissage;

        const duree   = 5000;   // ms
        const depart  = performance.now();
        const rotBase = currentRot;

        const seg = (2 * Math.PI) / n;

        function easeOut(t) {
            // cubic ease-out
            return 1 - Math.pow(1 - t, 3);
        }

        function frame(now) {
            const elapsed = now - depart;
            const t       = Math.min(elapsed / duree, 1);
            const rot     = rotBase + totalAngle * easeOut(t);

            dessinerRoue(rot);

            // Afficher le nom sous le pointeur (segment en haut = angle 0 par rapport à −π/2)
            const anglePointe = ((-rot - Math.PI / 2) % (Math.PI * 2) + Math.PI * 2) % (Math.PI * 2);
            const idx         = Math.floor(anglePointe / seg) % n;
            const nomAnim     = document.getElementById('nom-anime');
            if (nomAnim) nomAnim.textContent = noms[idx] || '';

            if (t < 1) {
                requestAnimationFrame(frame);
            } else {
                currentRot = rot;
                // Petite pause visuelle puis soumettre
                setTimeout(() => {
                    document.getElementById('form-tirage').submit();
                }, 900);
            }
        }

        requestAnimationFrame(frame);
    };
}());
</script>

@endpush
