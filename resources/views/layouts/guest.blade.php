<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'EventManager') }} - {{ $title ?? 'Welcome' }}</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        :root {
            --primary-color: #f53003;
            --primary-hover: #d42d00;
        }
        
        body {
            font-family: 'Figtree', sans-serif;
        }
        
        .guest-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .guest-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }
        
        .guest-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .guest-logo {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary-color) 0%, #ff6b4a 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }
        
        .guest-logo svg {
            width: 30px;
            height: 30px;
            color: white;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover,
        .btn-primary:focus,
        .btn-primary:active {
            background-color: var(--primary-hover) !important;
            border-color: var(--primary-hover) !important;
        }
        
        .text-primary {
            color: var(--primary-color) !important;
        }
        
        a {
            color: var(--primary-color);
            text-decoration: none;
        }
        
        a:hover {
            color: var(--primary-hover);
            text-decoration: underline;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(245, 48, 3, 0.25);
        }
        
        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
    </style>
    
    @stack('styles')
</head>
<body class="font-sans antialiased">
    <div class="guest-container d-flex align-items-center justify-content-center p-3">
        <div class="guest-card p-4 p-md-5">
            <div class="guest-header">
                <div class="guest-logo">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M4 5H20V19H4V5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M8 3V7" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        <path d="M16 3V7" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        <path d="M4 11H20" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        <circle cx="12" cy="15" r="2" fill="currentColor"/>
                    </svg>
                </div>
                <h2 class="fw-bold mb-1">{{ config('app.name', 'EventManager') }}</h2>
                <p class="text-muted">{{ $subtitle ?? 'Welcome back!' }}</p>
            </div>

            <!-- Flash Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{ $slot }}
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
</body>
</html>