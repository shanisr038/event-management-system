<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'EventManager') }} - Discover Amazing Events</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    
    <style>
        /* Custom styles for better appearance */
        .event-card {
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .event-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.1);
        }
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 60px 0;
        }
    </style>
</head>
<body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] flex p-6 lg:p-8 items-center lg:justify-center min-h-screen flex-col">
    <header class="w-full lg:max-w-7xl max-w-[335px] text-sm mb-6">
        <nav class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M4 5H20V19H4V5Z" stroke="#f53003" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M8 3V7" stroke="#f53003" stroke-width="2" stroke-linecap="round"/>
                    <path d="M16 3V7" stroke="#f53003" stroke-width="2" stroke-linecap="round"/>
                    <path d="M4 11H20" stroke="#f53003" stroke-width="2" stroke-linecap="round"/>
                    <circle cx="12" cy="15" r="2" fill="#f53003"/>
                </svg>
                <span class="font-bold text-xl">{{ config('app.name', 'EventManager') }}</span>
            </div>
            
            <div class="flex items-center gap-4">
                <a href="{{ route('events.index') }}" 
                   class="inline-block px-4 py-2 dark:text-[#EDEDEC] text-[#1b1b18] hover:text-[#f53003] transition">
                    Browse Events
                </a>
                
                @auth
                    <a href="{{ route('dashboard') }}"
                       class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] text-[#1b1b18] border border-transparent hover:border-[#19140035] dark:hover:border-[#3E3E3A] rounded-sm text-sm leading-normal">
                        Log in
                    </a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                           class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal">
                            Register
                        </a>
                    @endif
                @endauth
            </div>
        </nav>
    </header>

    <main class="w-full lg:max-w-7xl">
        <!-- Hero Section -->
        <div class="hero-section rounded-lg mb-8 p-8 text-center">
            <h1 class="text-4xl font-bold mb-4">Discover Amazing Events</h1>
            <p class="text-xl mb-6">Find and register for the best events in your area</p>
            <a href="{{ route('events.index') }}" 
               class="inline-block px-8 py-3 bg-white text-purple-600 font-semibold rounded-lg hover:bg-gray-100 transition">
                Browse Events
            </a>
        </div>

        <!-- Featured Events -->
        <div class="mb-8">
            <h2 class="text-2xl font-semibold mb-4">Featured Events</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @php
                    $featuredEvents = App\Models\Event::with('organizer', 'categories')
                        ->published()
                        ->upcoming()
                        ->latest()
                        ->take(3)
                        ->get();
                @endphp

                @forelse($featuredEvents as $event)
                    <div class="event-card bg-white dark:bg-[#161615] rounded-lg shadow-md overflow-hidden">
                        @if($event->banner_image)
                            <img src="{{ Storage::url($event->banner_image) }}" 
                                 alt="{{ $event->title }}"
                                 class="w-full h-48 object-cover">
                        @else
                            <div class="w-full h-48 bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                <i class="bi bi-calendar-event text-4xl text-gray-400"></i>
                            </div>
                        @endif
                        
                        <div class="p-4">
                            <h3 class="font-semibold text-lg mb-2">{{ $event->title }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                <i class="bi bi-calendar"></i> {{ $event->start_date->format('M d, Y') }}
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                                <i class="bi bi-geo-alt"></i> {{ $event->venue }}
                            </p>
                            <a href="{{ route('events.show', $event->slug) }}" 
                               class="inline-block w-full text-center px-4 py-2 bg-[#f53003] text-white rounded hover:bg-[#d42d00] transition">
                                View Details
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-8">
                        <p class="text-gray-500">No featured events at the moment.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Categories -->
        <div class="mb-8">
            <h2 class="text-2xl font-semibold mb-4">Browse by Category</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @php
                    $categories = App\Models\Category::active()->get();
                @endphp

                @foreach($categories as $category)
                    <a href="{{ route('events.category', $category) }}" 
                       class="block p-4 bg-white dark:bg-[#161615] rounded-lg shadow text-center hover:shadow-md transition">
                        @if($category->icon)
                            <i class="{{ $category->icon }} text-2xl mb-2" style="color: {{ $category->color ?? '#f53003' }}"></i>
                        @else
                            <i class="bi bi-tag text-2xl mb-2" style="color: {{ $category->color ?? '#f53003' }}"></i>
                        @endif
                        <span class="block font-medium">{{ $category->name }}</span>
                        <span class="text-sm text-gray-500">{{ $category->events_count ?? 0 }} events</span>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- CTA Section -->
        @guest
            <div class="bg-gradient-to-r from-purple-500 to-pink-500 rounded-lg p-8 text-center text-white">
                <h2 class="text-3xl font-bold mb-4">Ready to start your event journey?</h2>
                <p class="text-xl mb-6">Join our community today and discover amazing events!</p>
                <a href="{{ route('register') }}" 
                   class="inline-block px-8 py-3 bg-white text-purple-600 font-semibold rounded-lg hover:bg-gray-100 transition">
                    Get Started
                </a>
            </div>
        @endguest
    </main>

    <footer class="w-full lg:max-w-7xl mt-12 pt-8 border-t border-gray-200 dark:border-gray-700">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div>
                <h3 class="font-semibold mb-3">About Us</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Your one-stop platform for discovering and managing amazing events.</p>
            </div>
            <div>
                <h3 class="font-semibold mb-3">Quick Links</h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('events.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-[#f53003]">Browse Events</a></li>
                    @auth
                        <li><a href="{{ route('dashboard') }}" class="text-gray-600 dark:text-gray-400 hover:text-[#f53003]">Dashboard</a></li>
                        <li><a href="{{ route('my-registrations') }}" class="text-gray-600 dark:text-gray-400 hover:text-[#f53003]">My Registrations</a></li>
                    @endauth
                </ul>
            </div>
            <div>
                <h3 class="font-semibold mb-3">Categories</h3>
                <ul class="space-y-2 text-sm">
                    @foreach($categories->take(4) as $category)
                        <li><a href="{{ route('events.category', $category) }}" class="text-gray-600 dark:text-gray-400 hover:text-[#f53003]">{{ $category->name }}</a></li>
                    @endforeach
                </ul>
            </div>
            <div>
                <h3 class="font-semibold mb-3">Follow Us</h3>
                <div class="flex space-x-4">
                    <a href="#" class="text-gray-600 dark:text-gray-400 hover:text-[#f53003]"><i class="bi bi-facebook text-xl"></i></a>
                    <a href="#" class="text-gray-600 dark:text-gray-400 hover:text-[#f53003]"><i class="bi bi-twitter text-xl"></i></a>
                    <a href="#" class="text-gray-600 dark:text-gray-400 hover:text-[#f53003]"><i class="bi bi-instagram text-xl"></i></a>
                    <a href="#" class="text-gray-600 dark:text-gray-400 hover:text-[#f53003]"><i class="bi bi-linkedin text-xl"></i></a>
                </div>
            </div>
        </div>
        <div class="text-center mt-8 pt-4 text-sm text-gray-600 dark:text-gray-400">
            &copy; {{ date('Y') }} {{ config('app.name', 'EventManager') }}. All rights reserved.
        </div>
    </footer>
</body>
</html>