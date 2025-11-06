<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Login Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <link rel="stylesheet" href="{{ asset('css/admin/login.css') }}">
</head>

<body>

    <div class="overlay"></div>

    <div class="login-card">
        <h3><i class="fa-solid fa-user-shield me-2"></i>Login Admin</h3>

        @if (session('error'))
            <div class="alert alert-danger py-2">{{ session('error') }}</div>
        @endif
        @if (session('success'))
            <div class="alert alert-success py-2">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('admin.login.post') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" placeholder="admin@gmail.com" required>
            </div>
            <div class="mb-4 position-relative">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
            </div>
            <button class="btn btn-login">
                <i class="fa-solid fa-right-to-bracket me-1"></i> Sign In
            </button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
