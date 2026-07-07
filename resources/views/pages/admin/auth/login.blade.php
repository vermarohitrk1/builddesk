<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Login - BuildDesk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('assets/css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/custom.css') }}" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .login-container {
            max-width: 420px;
            margin: 0 auto;
            margin-top: 100px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row align-items-center justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="login-container">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h4 class="fw-bold">Super Admin Login</h4>
                            <p class="text-muted">Isolated multi-auth backend</p>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger border-0">
                                <ul class="mb-0 fs-6 small">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('admin.login.post') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label text-muted fw-semibold">Super Admin Email</label>
                                <input type="email" class="form-control form-control-lg bg-light border-0" id="email" name="email" value="{{ old('email') }}" required autofocus>
                            </div>
                            
                            <div class="mb-4">
                                <label for="password" class="form-label text-muted fw-semibold">Password</label>
                                <input type="password" class="form-control form-control-lg bg-light border-0" id="password" name="password" required>
                            </div>

                            <div class="mb-4 form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label text-muted" for="remember">Remember Me</label>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold">Sign In to Dashboard</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
