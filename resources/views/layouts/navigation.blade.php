@props(['user'])

<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="d-flex align-items-center text-decoration-none">
                        <svg width="30" height="30" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="me-2">
                            <path d="M4 5H20V19H4V5Z" stroke="#f53003" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M8 3V7" stroke="#f53003" stroke-width="2" stroke-linecap="round"/>
                            <path d="M16 3V7" stroke="#f53003" stroke-width="2" stroke-linecap="round"/>
                            <path d="M4 11H20" stroke="#f53003" stroke-width="2" stroke-linecap="round"/>
                            <circle cx="12" cy="15" r="2" fill="#f53003"/>
                        </svg>
                        <span class="font-bold text-gray-800">{{ config('app.name', 'EventManager') }}</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        <i class="bi bi-speedometer2 me-1"></i> {{ __('Dashboard') }}
                    </x-nav-link>
                    
                    <x-nav-link :href="route('events.index')" :active="request()->routeIs('events.*')">
                        <i class="bi bi-calendar-event me-1"></i> {{ __('Events') }}
                    </x-nav-link>
                    
                    @can('create', App\Models\Event::class)
                        <x-nav-link :href="route('events.create')" :active="request()->routeIs('events.create')">
                            <i class="bi bi-plus-circle me-1"></i> {{ __('Create Event') }}
                        </x-nav-link>
                    @endcan
                    
                    <x-nav-link :href="route('my-registrations')" :active="request()->routeIs('my-registrations')">
                        <i class="bi bi-ticket-perforated me-1"></i> {{ __('My Registrations') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <!-- Role Badge -->
                <div class="me-3">
                    @if($user->hasRole('admin'))
                        <span class="badge bg-danger">Admin</span>
                    @elseif($user->hasRole('organizer'))
                        <span class="badge bg-warning text-dark">Organizer</span>
                    @else
                        <span class="badge bg-info">Attendee</span>
                    @endif
                </div>
                
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div class="d-flex align-items-center">
                                @if($user->avatar)
                                    <img src="{{ Storage::url($user->avatar) }}" 
                                         alt="{{ $user->name }}" 
                                         class="rounded-circle me-2" 
                                         width="32" height="32" 
                                         style="object-fit: cover;">
                                @else
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" 
                                         style="width: 32px; height: 32px; font-size: 14px;">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                @endif
                                <span>{{ $user->name }}</span>
                            </div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-4 py-2 text-xs text-gray-400">
                            Signed in as
                        </div>
                        <div class="px-4 py-1 text-sm font-medium text-gray-700">
                            {{ $user->email }}
                        </div>
                        
                        <x-dropdown-link :href="route('profile.edit')" class="d-flex align-items-center">
                            <i class="bi bi-person-circle me-2"></i> {{ __('Profile') }}
                        </x-dropdown-link>
                        
                        <x-dropdown-link :href="route('dashboard')" class="d-flex align-items-center">
                            <i class="bi bi-speedometer2 me-2"></i> {{ __('Dashboard') }}
                        </x-dropdown-link>
                        
                        <x-dropdown-link :href="route('my-registrations')" class="d-flex align-items-center">
                            <i class="bi bi-ticket-perforated me-2"></i> {{ __('My Registrations') }}
                        </x-dropdown-link>
                        
                        @can('create', App\Models\Event::class)
                            <x-dropdown-link :href="route('events.create')" class="d-flex align-items-center">
                                <i class="bi bi-plus-circle me-2"></i> {{ __('Create Event') }}
                            </x-dropdown-link>
                        @endcan

                        <div class="border-t border-gray-200"></div>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();"
                                    class="d-flex align-items-center text-danger">
                                <i class="bi bi-box-arrow-right me-2"></i> {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                <i class="bi bi-speedometer2 me-2"></i> {{ __('Dashboard') }}
            </x-responsive-nav-link>
            
            <x-responsive-nav-link :href="route('events.index')" :active="request()->routeIs('events.index')">
                <i class="bi bi-calendar-event me-2"></i> {{ __('Events') }}
            </x-responsive-nav-link>
            
            @can('create', App\Models\Event::class)
                <x-responsive-nav-link :href="route('events.create')" :active="request()->routeIs('events.create')">
                    <i class="bi bi-plus-circle me-2"></i> {{ __('Create Event') }}
                </x-responsive-nav-link>
            @endcan
            
            <x-responsive-nav-link :href="route('my-registrations')" :active="request()->routeIs('my-registrations')">
                <i class="bi bi-ticket-perforated me-2"></i> {{ __('My Registrations') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="d-flex align-items-center mb-2">
                    @if($user->avatar)
                        <img src="{{ Storage::url($user->avatar) }}" 
                             alt="{{ $user->name }}" 
                             class="rounded-circle me-3" 
                             width="40" height="40" 
                             style="object-fit: cover;">
                    @else
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" 
                             style="width: 40px; height: 40px; font-size: 16px;">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <div class="font-medium text-base text-gray-800">{{ $user->name }}</div>
                        <div class="font-medium text-sm text-gray-500">{{ $user->email }}</div>
                    </div>
                </div>
                
                <div class="mt-2">
                    @if($user->hasRole('admin'))
                        <span class="badge bg-danger">Admin</span>
                    @elseif($user->hasRole('organizer'))
                        <span class="badge bg-warning text-dark">Organizer</span>
                    @else
                        <span class="badge bg-info">Attendee</span>
                    @endif
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    <i class="bi bi-person-circle me-2"></i> {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();"
                            class="text-danger">
                        <i class="bi bi-box-arrow-right me-2"></i> {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>