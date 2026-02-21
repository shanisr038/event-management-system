<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', config('app.name', 'EventManager'))</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Custom Styles -->
    @stack('styles')
    
    <style>
        :root {
            --primary-color: #f53003;
            --primary-hover: #d42d00;
            --secondary-color: #6c757d;
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
        }
        
        .nav-link.active {
            color: var(--primary-color) !important;
            font-weight: 500;
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
        
        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .text-primary {
            color: var(--primary-color) !important;
        }
        
        .bg-primary {
            background-color: var(--primary-color) !important;
        }
        
        .dropdown-item:active {
            background-color: var(--primary-color);
        }
        
        .alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }
        
        .card {
            transition: transform 0.2s, box-shadow 0.2s;
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        footer {
            background-color: #f8f9fa;
            border-top: 1px solid #dee2e6;
        }
        
        /* Badge styles for different statuses */
        .badge.bg-pending {
            background-color: #ffc107;
            color: #000;
        }
        
        .badge.bg-confirmed {
            background-color: #28a745;
            color: #fff;
        }
        
        .badge.bg-cancelled {
            background-color: #dc3545;
            color: #fff;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .navbar-brand {
                font-size: 1.25rem;
            }
            
            .container {
                padding-left: 15px;
                padding-right: 15px;
            }
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
                <svg width="30" height="30" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="me-2">
                    <path d="M4 5H20V19H4V5Z" stroke="#f53003" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M8 3V7" stroke="#f53003" stroke-width="2" stroke-linecap="round"/>
                    <path d="M16 3V7" stroke="#f53003" stroke-width="2" stroke-linecap="round"/>
                    <path d="M4 11H20" stroke="#f53003" stroke-width="2" stroke-linecap="round"/>
                    <circle cx="12" cy="15" r="2" fill="#f53003"/>
                </svg>
                <span>{{ config('app.name', 'EventManager') }}</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('events.index') ? 'active' : '' }}" 
                           href="{{ route('events.index') }}">
                            <i class="bi bi-calendar-event me-1"></i> Events
                        </a>
                    </li>
                    
                    @auth
                        @can('create', App\Models\Event::class)
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('events.create') ? 'active' : '' }}" 
                                   href="{{ route('events.create') }}">
                                    <i class="bi bi-plus-circle me-1"></i> Create Event
                                </a>
                            </li>
                        @endcan
                        
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('my-registrations') ? 'active' : '' }}" 
                               href="{{ route('my-registrations') }}">
                                <i class="bi bi-ticket-perforated me-1"></i> My Registrations
                            </a>
                        </li>
                    @endauth
                </ul>
                
                <ul class="navbar-nav ms-auto">
                    @guest
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('login') ? 'active' : '' }}" 
                               href="{{ route('login') }}">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('register') ? 'active' : '' }}" 
                               href="{{ route('register') }}">
                                <i class="bi bi-person-plus me-1"></i> Register
                            </a>
                        </li>
                    @else
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" 
                               id="navbarDropdown" role="button" data-bs-toggle="dropdown" 
                               aria-expanded="false">
                                @if(Auth::user()->avatar)
                                    <img src="{{ Storage::url(Auth::user()->avatar) }}" 
                                         alt="{{ Auth::user()->name }}" 
                                         class="rounded-circle me-2" 
                                         width="30" height="30" 
                                         style="object-fit: cover;">
                                @else
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" 
                                         style="width: 30px; height: 30px;">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                    </div>
                                @endif
                                <span>{{ Auth::user()->name }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <li>
                                    <h6 class="dropdown-header">
                                        @if(Auth::user()->hasRole('admin'))
                                            <span class="badge bg-danger">Admin</span>
                                        @elseif(Auth::user()->hasRole('organizer'))
                                            <span class="badge bg-warning text-dark">Organizer</span>
                                        @else
                                            <span class="badge bg-info">Attendee</span>
                                        @endif
                                    </h6>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('dashboard') }}">
                                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('my-registrations') }}">
                                        <i class="bi bi-ticket-perforated me-2"></i> My Registrations
                                    </a>
                                </li>
                                @can('create', App\Models\Event::class)
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('events.create') }}">
                                            <i class="bi bi-plus-circle me-2"></i> Create Event
                                        </a>
                                    </li>
                                @endcan
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-box-arrow-right me-2"></i> Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <main class="flex-grow-1 py-4">
        <div class="container">
            <!-- Flash Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-check-circle-fill me-2 fs-4"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle-fill me-2 fs-4"></i>
                        <div>{{ session('error') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-exclamation-circle-fill me-2 fs-4"></i>
                        <div>
                            <strong>Please fix the following errors:</strong>
                            <ul class="mb-0 mt-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Main Content -->
            @yield('content')
        </div>
    </main>

    <footer class="bg-dark text-white py-4 mt-auto">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>{{ config('app.name', 'EventManager') }}</h5>
                    <p class="text-white-50">Your one-stop platform for discovering and managing amazing events.</p>
                </div>
                <div class="col-md-3">
                    <h6>Quick Links</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('events.index') }}" class="text-white-50 text-decoration-none">Events</a></li>
                        @auth
                            <li><a href="{{ route('dashboard') }}" class="text-white-50 text-decoration-none">Dashboard</a></li>
                            <li><a href="{{ route('my-registrations') }}" class="text-white-50 text-decoration-none">My Registrations</a></li>
                        @endauth
                    </ul>
                </div>
                <div class="col-md-3">
                    <h6>Follow Us</h6>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-white-50 text-decoration-none"><i class="bi bi-facebook fs-5"></i></a>
                        <a href="#" class="text-white-50 text-decoration-none"><i class="bi bi-twitter fs-5"></i></a>
                        <a href="#" class="text-white-50 text-decoration-none"><i class="bi bi-instagram fs-5"></i></a>
                        <a href="#" class="text-white-50 text-decoration-none"><i class="bi bi-linkedin fs-5"></i></a>
                    </div>
                </div>
            </div>
            <hr class="border-white-50">
            <div class="text-center text-white-50">
                &copy; {{ date('Y') }} {{ config('app.name', 'EventManager') }}. All rights reserved.
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom Scripts -->
    @stack('scripts')
    
    <script>
        // Auto-close alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                document.querySelectorAll('.alert').forEach(function(alert) {
                    var bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        });
    </script>
</body>
</html>