<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accès refusé — TontineApp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100 auth-bg">
<div class="auth-card card shadow-sm w-100 mx-3 text-center">
    <div class="card-body p-5">
        <div class="display-1 fw-bold text-warning mb-2">403</div>
        <h4 class="fw-bold mb-2">Accès refusé</h4>
        <p class="text-muted mb-4">Vous n'avez pas la permission d'accéder à cette ressource.</p>
        @auth
            <a href="{{ route('dashboard') }}" class="btn btn-success">← Retour au tableau de bord</a>
        @else
            <a href="{{ route('login') }}" class="btn btn-success">← Retour à la connexion</a>
        @endauth
    </div>
</div>
</body>
</html>
