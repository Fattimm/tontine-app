<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'TontineApp')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .navbar-brand { font-weight: 700; letter-spacing: 1px; }
        .sidebar { min-height: calc(100vh - 56px); background: #fff; border-right: 1px solid #dee2e6; }
        .sidebar .nav-link { color: #495057; border-radius: 8px; margin-bottom: 4px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: #e8f5e9; color: #198754; }
        .sidebar .nav-link .icon { width: 20px; display: inline-block; text-align: center; }
        .card { border: none; box-shadow: 0 1px 4px rgba(0,0,0,.08); border-radius: 12px; }
        .table thead th { background: #f1f5f9; font-weight: 600; font-size:.85rem; text-transform:uppercase; letter-spacing:.5px; }
        .btn-action { padding: 3px 10px; font-size:.8rem; border-radius: 6px; }

        /* ✅ Pagination custom */
        .pagination { gap: 4px; }
        .pagination .page-item .page-link {
            border-radius: 8px !important;
            border: 1px solid #dee2e6;
            color: #198754;
            padding: 6px 12px;
            font-size: .85rem;
            transition: all .2s;
        }
        .pagination .page-item.active .page-link {
            background-color: #198754;
            border-color: #198754;
            color: #fff;
        }
        .pagination .page-item.disabled .page-link { color: #adb5bd; }
        .pagination .page-item .page-link:hover:not(.active) {
            background-color: #e8f5e9;
            border-color: #198754;
        }
        /* Flèches précédent/suivant */
        .pagination .page-item:first-child .page-link,
        .pagination .page-item:last-child .page-link {
            font-weight: 600;
            padding: 6px 14px;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-success">
    <div class="container-fluid px-4">
        <a class="navbar-brand" href="{{ route('home') }}">💰 TontineApp</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
    </div>
</nav>

<div class="container-fluid">
<div class="row">
    <nav class="col-md-2 sidebar py-3 px-3">
        <p class="text-muted text-uppercase fw-bold" style="font-size:.75rem;letter-spacing:1px">Menu</p>
        <a href="{{ route('membres.index') }}"    class="nav-link {{ request()->routeIs('membres*')    ? 'active' : '' }}"><span class="icon">👥</span> Membres</a>
        <a href="{{ route('tontines.index') }}"   class="nav-link {{ request()->routeIs('tontines*')   ? 'active' : '' }}"><span class="icon">💼</span> Tontines</a>
        <a href="{{ route('cotisations.index') }}" class="nav-link {{ request()->routeIs('cotisations*') ? 'active' : '' }}"><span class="icon">💳</span> Cotisations</a>
        <a href="{{ route('tours.index') }}"      class="nav-link {{ request()->routeIs('tours*')      ? 'active' : '' }}"><span class="icon">🔄</span> Tours</a>
        <hr>
        <a href="{{ route('cotisations.create') }}" class="nav-link text-success fw-bold"><span class="icon">+</span> Nouvelle cotisation</a>
        <a href="{{ route('membres.create') }}"     class="nav-link text-success fw-bold"><span class="icon">+</span> Nouveau membre</a>
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
@stack('scripts')
</body>
</html>
