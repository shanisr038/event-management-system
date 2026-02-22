<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Welcome Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-2xl font-bold mb-2">Welcome back, {{ Auth::user()->name }}!</h3>
                            <p class="text-gray-600 dark:text-gray-400">
                                @if(Auth::user()->hasRole('admin'))
                                    You have full administrative access.
                                @elseif(Auth::user()->hasRole('organizer'))
                                    Manage your events, tickets, and registrations from here.
                                @else
                                    View and manage your event registrations.
                                @endif
                            </p>
                        </div>
                        <div>
                            @if(Auth::user()->hasRole('organizer') || Auth::user()->hasRole('admin'))
                                <a href="{{ route('events.create') }}" 
                                   class="inline-flex items-center px-4 py-2 bg-[#f53003] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#d42d00] focus:bg-[#d42d00] active:bg-[#b32600] focus:outline-none focus:ring-2 focus:ring-[#f53003] focus:ring-offset-2 transition ease-in-out duration-150">
                                    <i class="bi bi-plus-circle me-2"></i> Create New Event
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                @php
                    $stats = [
                        'events' => App\Models\Event::where('organizer_id', Auth::id())->count(),
                        'registrations' => App\Models\EventRegistration::where('user_id', Auth::id())->count(),
                        'upcoming' => App\Models\Event::where('organizer_id', Auth::id())
                            ->where('start_date', '>', now())
                            ->count(),
                    ];
                    
                    if(Auth::user()->hasRole('admin')) {
                        $stats['events'] = App\Models\Event::count();
                        $stats['registrations'] = App\Models\EventRegistration::count();
                        $stats['upcoming'] = App\Models\Event::where('start_date', '>', now())->count();
                    }
                @endphp

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-full">
                                <i class="bi bi-calendar-event text-2xl text-blue-600 dark:text-blue-300"></i>
                            </div>
                            <div class="ms-4">
                                <p class="text-sm text-gray-600 dark:text-gray-400">Total Events</p>
                                <p class="text-2xl font-bold">{{ $stats['events'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 bg-green-100 dark:bg-green-900 rounded-full">
                                <i class="bi bi-ticket-perforated text-2xl text-green-600 dark:text-green-300"></i>
                            </div>
                            <div class="ms-4">
                                <p class="text-sm text-gray-600 dark:text-gray-400">Total Registrations</p>
                                <p class="text-2xl font-bold">{{ $stats['registrations'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-full">
                                <i class="bi bi-rocket-takeoff text-2xl text-purple-600 dark:text-purple-300"></i>
                            </div>
                            <div class="ms-4">
                                <p class="text-sm text-gray-600 dark:text-gray-400">Upcoming Events</p>
                                <p class="text-2xl font-bold">{{ $stats['upcoming'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- My Events -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">My Events</h3>
                        @php
                            $myEvents = App\Models\Event::where('organizer_id', Auth::id())
                                ->latest()
                                ->take(5)
                                ->get();
                        @endphp

                        @if($myEvents->isNotEmpty())
                            <div class="space-y-3">
                                @foreach($myEvents as $event)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded">
                                        <div>
                                            <p class="font-medium">{{ $event->title }}</p>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                                {{ $event->start_date->format('M d, Y') }}
                                            </p>
                                        </div>
                                        <a href="{{ route('events.show', $event->slug) }}" 
                                           class="text-[#f53003] hover:underline">
                                            View
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                            
                            @if(Auth::user()->hasRole('organizer') || Auth::user()->hasRole('admin'))
                                <div class="mt-4">
                                    <a href="{{ route('events.index') }}" 
                                       class="text-[#f53003] hover:underline text-sm">
                                        View All Events →
                                    </a>
                                </div>
                            @endif
                        @else
                            <p class="text-gray-600 dark:text-gray-400">You haven't created any events yet.</p>
                            @if(Auth::user()->hasRole('organizer') || Auth::user()->hasRole('admin'))
                                <div class="mt-4">
                                    <a href="{{ route('events.create') }}" 
                                       class="inline-flex items-center px-4 py-2 bg-[#f53003] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#d42d00]">
                                        <i class="bi bi-plus-circle me-2"></i> Create Your First Event
                                    </a>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>

                <!-- My Registrations -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">My Registrations</h3>
                        @php
                            $myRegistrations = App\Models\EventRegistration::with('event', 'ticket')
                                ->where('user_id', Auth::id())
                                ->latest()
                                ->take(5)
                                ->get();
                        @endphp

                        @if($myRegistrations->isNotEmpty())
                            <div class="space-y-3">
                                @foreach($myRegistrations as $registration)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded">
                                        <div>
                                            <p class="font-medium">{{ $registration->event->title }}</p>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                                {{ $registration->ticket->name }} x {{ $registration->quantity }}
                                            </p>
                                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded 
                                                @if($registration->status === 'confirmed') bg-green-100 text-green-800
                                                @elseif($registration->status === 'cancelled') bg-red-100 text-red-800
                                                @else bg-yellow-100 text-yellow-800 @endif">
                                                {{ ucfirst($registration->status) }}
                                            </span>
                                        </div>
                                        <a href="{{ route('registrations.confirmation', $registration) }}" 
                                           class="text-[#f53003] hover:underline">
                                            View
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                            
                            <div class="mt-4">
                                <a href="{{ route('my-registrations') }}" 
                                   class="text-[#f53003] hover:underline text-sm">
                                    View All Registrations →
                                </a>
                            </div>
                        @else
                            <p class="text-gray-600 dark:text-gray-400">You haven't registered for any events yet.</p>
                            <div class="mt-4">
                                <a href="{{ route('events.index') }}" 
                                   class="inline-flex items-center px-4 py-2 bg-[#f53003] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#d42d00]">
                                    <i class="bi bi-search me-2"></i> Browse Events
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Admin Panel Section (Only for Admins) -->
            @if(auth()->user()->hasRole('admin'))
                <div class="mt-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Admin Panel</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <a href="{{ route('admin.users.index') }}" 
                               class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg text-center hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                                <i class="bi bi-people-fill text-3xl mb-2 d-block text-primary"></i>
                                <span class="font-medium">Manage Users</span>
                                <p class="text-xs text-gray-500 mt-1">Add/remove roles, manage permissions</p>
                            </a>
                            
                            <a href="{{ route('events.index') }}" 
                               class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg text-center hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                                <i class="bi bi-calendar-check text-3xl mb-2 d-block text-success"></i>
                                <span class="font-medium">All Events</span>
                                <p class="text-xs text-gray-500 mt-1">View and manage all events</p>
                            </a>
                            
                            <a href="#" 
                               class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg text-center hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                                <i class="bi bi-pie-chart text-3xl mb-2 d-block text-info"></i>
                                <span class="font-medium">Reports</span>
                                <p class="text-xs text-gray-500 mt-1">View system analytics</p>
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Old Admin Quick Actions - Remove this section -->
            {{-- 
            @can('manage', App\Models\Event::class)
                <div class="mt-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    ... old code ...
                </div>
            @endcan
            --}}
        </div>
    </div>
</x-app-layout>