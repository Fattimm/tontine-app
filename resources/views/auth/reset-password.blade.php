<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Définir votre mot de passe — TontineApp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100 auth-bg">
<div class="auth-card card shadow-sm w-100 mx-3">
    <div class="card-body p-4">
        <div class="text-center mb-4">
            <div class="fs-1">💰</div>
            <h4 class="fw-bold">TontineApp</h4>
            <p class="text-muted small">Définissez votre mot de passe pour activer votre compte</p>
        </div>

        @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
        </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="mb-3">
                <label class="form-label fw-semibold">Email</label>
                <input type="email" name="email"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email', $email) }}" readonly>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Nouveau mot de passe</label>
                <input type="password" name="password"
                       class="form-control @error('password') is-invalid @enderror"
                       placeholder="Minimum 6 caractères" autofocus>
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Confirmer le mot de passe</label>
                <input type="password" name="password_confirmation"
                       class="form-control"
                       placeholder="Répétez votre mot de passe">
            </div>

            <button type="submit" class="btn btn-success w-100 fw-bold">
                Activer mon compte
            </button>
        </form>
    </div>
</div>
</body>
</html>
