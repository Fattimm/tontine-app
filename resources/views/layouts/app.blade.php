<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'TontineApp')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-success">
    <div class="container-fluid px-4">
        <a class="navbar-brand" href="{{ route('dashboard') }}">💰 TontineApp</a>
        <div class="navbar-nav ms-auto d-flex align-items-center gap-2">
            <a href="{{ route('profil.edit') }}"
            class="navbar-text text-white text-decoration-none small">
                {{ auth()->user()->name }}
                <span class="badge bg-white text-success ms-1">
                    {{ ucfirst(auth()->user()->role) }}
                </span>
            </a>
            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                @csrf
                <button class="btn btn-outline-light btn-sm">Déconnexion</button>
            </form>
        </div>
    </div>
</nav>

<div class="container-fluid">
<div class="row">
    <nav class="col-md-2 sidebar py-3 px-3">
        <p class="text-muted text-uppercase fw-bold sidebar-title">Menu</p>

        {{-- Dashboard pour tous --}}
        <a href="{{ route('dashboard') }}"
        class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <span class="icon">📊</span> Dashboard
        </a>

        {{-- Admin uniquement --}}
        @if(auth()->user()->isAdmin())
            <hr>
            <p class="text-muted text-uppercase fw-bold sidebar-section">Administration</p>
            <a href="{{ route('admin.users') }}"
            class="nav-link {{ request()->routeIs('admin.users') ? 'active' : '' }}">
                <span class="icon">🔑</span> Utilisateurs
            </a>
            <a href="{{ route('admin.users.supprimes') }}"
            class="nav-link {{ request()->routeIs('admin.users.supprimes') ? 'active' : '' }}">
                <span class="icon">🗑️</span> Corbeille
                @php $nbSupp = \App\Models\User::onlyTrashed()->count(); @endphp
                @if($nbSupp > 0)
                    <span class="badge bg-danger ms-1">{{ $nbSupp }}</span>
                @endif
            </a>
        @endif

        {{-- Organisateur uniquement --}}
        @if(auth()->user()->isOrganisateur())
            <hr>
            <p class="text-muted text-uppercase fw-bold sidebar-section">Gestion</p>
            <a href="{{ route('membres.index') }}"
            class="nav-link {{ request()->routeIs('membres.index') || request()->routeIs('membres.create') || request()->routeIs('membres.show') || request()->routeIs('membres.edit') ? 'active' : '' }}">
                <span class="icon">👥</span> Membres
            </a>
            <a href="{{ route('tontines.index') }}"
            class="nav-link {{ request()->routeIs('tontines.index') || request()->routeIs('tontines.show') || request()->routeIs('tontines.create') || request()->routeIs('tontines.edit') || request()->routeIs('tontines.tirage') ? 'active' : '' }}">
                <span class="icon">💼</span> Mes tontines
            </a>
            <a href="{{ route('cotisations.index') }}"
            class="nav-link {{ request()->routeIs('cotisations.index') || request()->routeIs('cotisations.show') ? 'active' : '' }}">
                <span class="icon">💳</span> Cotisations
            </a>
            <a href="{{ route('tours.index') }}"
            class="nav-link {{ request()->routeIs('tours*') ? 'active' : '' }}">
                <span class="icon">🔄</span> Tours
            </a>
            <hr>
            <p class="text-muted text-uppercase fw-bold sidebar-section">Corbeille</p>
            @php
                $nbMembresSupp    = \App\Models\Membre::onlyTrashed()->count();
                $nbTontinesSupp   = \App\Models\Tontine::onlyTrashed()->where('organisateur_id', auth()->id())->count();
                $nbCotisSupp      = \App\Models\Cotisation::onlyTrashed()
                    ->whereHas('tontine', fn($q) => $q->withTrashed()->where('organisateur_id', auth()->id()))
                    ->count();
            @endphp
            <a href="{{ route('membres.supprimes') }}"
            class="nav-link {{ request()->routeIs('membres.supprimes') ? 'active' : '' }}">
                <span class="icon">👥</span> Membres
                @if($nbMembresSupp > 0)
                    <span class="badge bg-danger ms-1">{{ $nbMembresSupp }}</span>
                @endif
            </a>
            <a href="{{ route('tontines.supprimees') }}"
            class="nav-link {{ request()->routeIs('tontines.supprimees') ? 'active' : '' }}">
                <span class="icon">💼</span> Tontines
                @if($nbTontinesSupp > 0)
                    <span class="badge bg-danger ms-1">{{ $nbTontinesSupp }}</span>
                @endif
            </a>
            <a href="{{ route('cotisations.supprimees') }}"
            class="nav-link {{ request()->routeIs('cotisations.supprimees') ? 'active' : '' }}">
                <span class="icon">💳</span> Cotisations
                @if($nbCotisSupp > 0)
                    <span class="badge bg-danger ms-1">{{ $nbCotisSupp }}</span>
                @endif
            </a>
        @endif

        {{-- Membre uniquement --}}
        @if(auth()->user()->isMembre())
            <hr>
            <p class="text-muted text-uppercase fw-bold sidebar-section">Mon espace</p>
            @if(auth()->user()->membre)
                <a href="{{ route('membres.tontine-detail', [auth()->user()->membre, auth()->user()->membre->tontines()->first()]) }}"
                class="nav-link">
                    <span class="icon">💼</span> Mes tontines
                </a>
            @endif
            <a href="{{ route('cotisations.create') }}" class="nav-link text-success fw-bold">
                <span class="icon">💳</span> Cotiser
            </a>
        @endif
    </nav>
    <main class="col-md-10 py-4 px-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-3">
                ✅ {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show rounded-3">
                ⚠️ {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show rounded-3">
                ❌ {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger rounded-3">
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        @yield('content')
    </main>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

{{-- Modal de confirmation réutilisable --}}
<div class="modal fade" id="modalConfirm" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-1">
                <h6 class="modal-title fw-bold" id="modalConfirmTitre">Confirmation</h6>
                <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-2 text-muted small" id="modalConfirmMessage">
                Êtes-vous sûr de vouloir continuer ?
            </div>
            <div class="modal-footer border-0 pt-1 gap-2">
                <button type="button" class="btn btn-outline-secondary btn-sm px-3"
                        data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-sm px-3 fw-semibold"
                        id="modalConfirmBtn">Confirmer</button>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const modalEl  = document.getElementById('modalConfirm');
    const modal    = new bootstrap.Modal(modalEl);
    const titre    = document.getElementById('modalConfirmTitre');
    const message  = document.getElementById('modalConfirmMessage');
    const btn      = document.getElementById('modalConfirmBtn');
    let   cible    = null;

    document.addEventListener('submit', function (e) {
        const form = e.target;
        if (!form.dataset.confirm) return;

        e.preventDefault();

        titre.textContent   = form.dataset.confirmTitre   || 'Confirmation';
        message.textContent = form.dataset.confirm;
        btn.className       = 'btn btn-sm px-3 fw-semibold btn-' + (form.dataset.confirmType || 'danger');
        btn.textContent     = form.dataset.confirmBtn     || 'Confirmer';
        cible               = form;

        modal.show();
    });

    btn.addEventListener('click', function () {
        modal.hide();
        if (cible) {
            const tmp = cible;
            cible = null;
            tmp.submit();
        }
    });
}());
</script>

@stack('scripts')
</body>
</html>
