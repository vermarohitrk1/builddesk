<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'BuildDesk') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base_url" content="{{ url('/') }}">
    <meta name="favicon" content="{{ asset('favicon.ico') }}">
    <!-- Simple Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <style>
        :root {
            --bs-theme: {{ App\Helpers\BrandingHelper::getBranding()['theme_color'] }};
            --bs-theme-rgb: {{ App\Helpers\BrandingHelper::getBranding()['theme_color_rgb'] }};
            --bs-theme-hover: {{ App\Helpers\BrandingHelper::getBranding()['theme_color_hover'] }};
            --bs-theme-active: {{ App\Helpers\BrandingHelper::getBranding()['theme_color_active'] }};
        }
    </style>
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
    @stack('styles')

    
    <script>
        const BASE_URL = "{{ url('/') }}";
    </script>
</head>
<body>
    <div id="wrapper">
        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Page Content -->
        <div id="content">
            <nav class="navbar navbar-expand-lg navbar-light bg-white mb-4">
                <div class="container-fluid">
                    <div class="d-flex align-items-center">
                        <span class="navbar-text me-3">
                            Welcome, <strong>{{ Auth::user()->name ?? 'Guest' }}</strong>
                        </span>

                        @if(session()->has('impersonated_by'))
                            <div class="alert alert-warning py-1 px-3 m-0 d-flex align-items-center">
                                <i class="fas fa-user-secret me-2"></i>
                                Impersonating: {{ Auth::user()->organisation->name }}
                                <a href="{{ route('tenant.stop-impersonation') }}" class="btn btn-sm btn-danger ms-3">
                                    Return to Admin
                                </a>
                            </div>
                        @elseif(Auth::user()->role === 'super_admin')
                            <span class="badge bg-dark">SYSTEM ADMIN</span>
                        @else
                            <span class="badge bg-secondary">
                                {{ Auth::user()->organisation->name ?? 'No Organisation' }}
                            </span>
                        @endif
                    </div>
                </div>
            </nav>

            <div class="container-fluid">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('assets/js/ajax.js') }}"></script>
    <script src="{{ asset('assets/js/fileUpload.js') }}"></script>
    <script src="{{ asset('assets/js/custom.js') }}"></script>
    @stack('scripts')
</body>
</html>
