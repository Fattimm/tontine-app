<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — TontineApp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f0fdf4; }
        .login-card { max-width: 420px; border-radius: 16px; }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100">
<div class="login-card card shadow-sm w-100 mx-3">
    <div class="card-body p-4">
        <div class="text-center mb-4">
            <div class="fs-1">💰</div>
            <h4 class="fw-bold">TontineApp</h4>
            <p class="text-muted small">Connectez-vous à votre espace</p>
        </div>

        @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
        </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold">Email</label>
                <input type="email" name="email"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}"
                       placeholder="votre@email.com" autofocus>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Mot de passe</label>
                <input type="password" name="password"
                       class="form-control @error('password') is-invalid @enderror"
                       placeholder="••••••••">
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" name="remember" class="form-check-input" id="remember">
                <label class="form-check-label text-muted" for="remember">Se souvenir de moi</label>
            </div>

            <button type="submit" class="btn btn-success w-100 fw-bold">
                Se connecter
            </button>
        </form>
    </div>
</div>
</body>
</html>
