<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié — TontineApp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100 auth-bg">
<div class="auth-card card shadow-sm w-100 mx-3">
    <div class="card-body p-4">
        <div class="text-center mb-4">
            <div class="fs-1"><i class="bi bi-wallet2"></i></div>
            <h4 class="fw-bold">TontineApp</h4>
            <p class="text-muted small">Entrez votre email pour recevoir un lien de réinitialisation</p>
        </div>

        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
        </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold">Adresse email</label>
                <input type="email" name="email"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}"
                       placeholder="votre@email.com" autofocus>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <button type="submit" class="btn btn-success w-100 fw-bold mb-3">
                Envoyer le lien
            </button>
        </form>

        <div class="text-center">
            <a href="{{ route('login') }}" class="small text-muted">← Retour à la connexion</a>
        </div>
    </div>
</div>
</body>
</html>
