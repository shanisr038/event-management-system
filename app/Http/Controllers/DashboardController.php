<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\EventTicket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Show the dashboard based on user role.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            return $this->adminDashboard();
        } elseif ($user->hasRole('organizer')) {
            return $this->organizerDashboard();
        } else {
            return $this->attendeeDashboard();
        }
    }

    /**
     * Admin dashboard with system-wide statistics.
     */
    private function adminDashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_events' => Event::count(),
            'total_registrations' => EventRegistration::count(),
            'total_revenue' => EventRegistration::where('status', 'confirmed')->sum('total_amount'),
            'published_events' => Event::where('is_published', true)->count(),
            'upcoming_events' => Event::where('start_date', '>', now())->count(),
            'pending_registrations' => EventRegistration::where('status', 'pending')->count(),
            'cancelled_registrations' => EventRegistration::where('status', 'cancelled')->count(),
        ];

        $recent_events = Event::with('organizer')
            ->latest()
            ->take(5)
            ->get();

        $recent_users = User::latest()
            ->take(5)
            ->get();

        $popular_categories = DB::table('event_category')
            ->join('categories', 'event_category.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('count(*) as total'))
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $registrations_by_status = [
            'confirmed' => EventRegistration::where('status', 'confirmed')->count(),
            'pending' => EventRegistration::where('status', 'pending')->count(),
            'cancelled' => EventRegistration::where('status', 'cancelled')->count(),
        ];

        $monthly_registrations = EventRegistration::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as total')
        )
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('dashboard.admin', compact(
            'stats',
            'recent_events',
            'recent_users',
            'popular_categories',
            'registrations_by_status',
            'monthly_registrations'
        ));
    }

    /**
     * Organizer dashboard with event-specific statistics.
     */
    private function organizerDashboard()
    {
        $user = Auth::user();

        $stats = [
            'my_events' => Event::where('organizer_id', $user->id)->count(),
            'published_events' => Event::where('organizer_id', $user->id)
                ->where('is_published', true)
                ->count(),
            'upcoming_events' => Event::where('organizer_id', $user->id)
                ->where('start_date', '>', now())
                ->count(),
            'total_registrations' => EventRegistration::whereIn('event_id', 
                Event::where('organizer_id', $user->id)->pluck('id')
            )->count(),
            'confirmed_registrations' => EventRegistration::whereIn('event_id', 
                Event::where('organizer_id', $user->id)->pluck('id')
            )->where('status', 'confirmed')->count(),
            'total_revenue' => EventRegistration::whereIn('event_id', 
                Event::where('organizer_id', $user->id)->pluck('id')
            )->where('status', 'confirmed')->sum('total_amount'),
            'total_tickets_sold' => EventRegistration::whereIn('event_id', 
                Event::where('organizer_id', $user->id)->pluck('id')
            )->where('status', 'confirmed')->sum('quantity'),
        ];

        $my_events = Event::where('organizer_id', $user->id)
            ->withCount(['registrations as confirmed_registrations' => function ($query) {
                $query->where('status', 'confirmed');
            }])
            ->latest()
            ->take(5)
            ->get();

        $recent_registrations = EventRegistration::with(['event', 'user', 'ticket'])
            ->whereIn('event_id', Event::where('organizer_id', $user->id)->pluck('id'))
            ->latest()
            ->take(10)
            ->get();

        $upcoming_events = Event::where('organizer_id', $user->id)
            ->where('start_date', '>', now())
            ->where('is_published', true)
            ->orderBy('start_date')
            ->take(5)
            ->get();

        $registration_trend = EventRegistration::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as total')
        )
            ->whereIn('event_id', Event::where('organizer_id', $user->id)->pluck('id'))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('dashboard.organizer', compact(
            'stats',
            'my_events',
            'recent_registrations',
            'upcoming_events',
            'registration_trend'
        ));
    }

    /**
     * Attendee dashboard with personal registration information.
     */
    private function attendeeDashboard()
    {
        $user = Auth::user();

        $stats = [
            'total_registrations' => EventRegistration::where('user_id', $user->id)->count(),
            'confirmed_registrations' => EventRegistration::where('user_id', $user->id)
                ->where('status', 'confirmed')
                ->count(),
            'cancelled_registrations' => EventRegistration::where('user_id', $user->id)
                ->where('status', 'cancelled')
                ->count(),
            'upcoming_events' => EventRegistration::where('user_id', $user->id)
                ->whereHas('event', function ($query) {
                    $query->where('start_date', '>', now());
                })
                ->where('status', 'confirmed')
                ->count(),
            'past_events' => EventRegistration::where('user_id', $user->id)
                ->whereHas('event', function ($query) {
                    $query->where('end_date', '<', now());
                })
                ->where('status', 'confirmed')
                ->count(),
            'total_spent' => EventRegistration::where('user_id', $user->id)
                ->where('status', 'confirmed')
                ->sum('total_amount'),
        ];

        $upcoming_registrations = EventRegistration::with(['event', 'ticket'])
            ->where('user_id', $user->id)
            ->whereHas('event', function ($query) {
                $query->where('start_date', '>', now());
            })
            ->where('status', 'confirmed')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $past_registrations = EventRegistration::with(['event', 'ticket'])
            ->where('user_id', $user->id)
            ->whereHas('event', function ($query) {
                $query->where('end_date', '<', now());
            })
            ->where('status', 'confirmed')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $recent_activities = EventRegistration::with(['event'])
            ->where('user_id', $user->id)
            ->latest()
            ->take(10)
            ->get();

        $favorite_categories = DB::table('event_registrations')
            ->join('events', 'event_registrations.event_id', '=', 'events.id')
            ->join('event_category', 'events.id', '=', 'event_category.event_id')
            ->join('categories', 'event_category.category_id', '=', 'categories.id')
            ->where('event_registrations.user_id', $user->id)
            ->select('categories.name', DB::raw('count(*) as total'))
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return view('dashboard.attendee', compact(
            'stats',
            'upcoming_registrations',
            'past_registrations',
            'recent_activities',
            'favorite_categories'
        ));
    }

    /**
     * Get upcoming events for the authenticated user.
     */
    public function upcomingEvents()
    {
        $user = Auth::user();

        if ($user->hasRole('organizer') || $user->hasRole('admin')) {
            $events = Event::where('organizer_id', $user->id)
                ->where('start_date', '>', now())
                ->orderBy('start_date')
                ->paginate(10);
        } else {
            $events = EventRegistration::where('user_id', $user->id)
                ->whereHas('event', function ($query) {
                    $query->where('start_date', '>', now());
                })
                ->with('event')
                ->paginate(10)
                ->pluck('event');
        }

        return view('dashboard.upcoming', compact('events'));
    }

    /**
     * Get registration history for the authenticated user.
     */
    public function registrationHistory()
    {
        $user = Auth::user();

        $registrations = EventRegistration::with(['event', 'ticket'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('dashboard.history', compact('registrations'));
    }

    /**
     * Get event analytics for organizers.
     */
    public function analytics(Event $event = null)
    {
        $this->authorize('viewReports', $event ?? Event::class);

        $user = Auth::user();

        if ($event) {
            // Analytics for specific event
            $data = $this->getEventAnalytics($event);
        } else {
            // Analytics for all user's events
            $events = Event::where('organizer_id', $user->id)->get();
            $data = $this->getAggregatedAnalytics($events);
        }

        return view('dashboard.analytics', compact('data', 'event'));
    }

    /**
     * Get analytics for a specific event.
     */
    private function getEventAnalytics(Event $event)
    {
        return [
            'event' => $event,
            'total_registrations' => $event->registrations()->count(),
            'confirmed_registrations' => $event->registrations()->where('status', 'confirmed')->count(),
            'checked_in' => $event->registrations()->where('checked_in', true)->count(),
            'revenue' => $event->registrations()->where('status', 'confirmed')->sum('total_amount'),
            'registrations_by_date' => $event->registrations()
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
            'ticket_sales' => $event->tickets()
                ->withCount(['registrations as sold_count' => function ($query) {
                    $query->where('status', 'confirmed');
                }])
                ->get(),
        ];
    }

    /**
     * Get aggregated analytics for multiple events.
     */
    private function getAggregatedAnalytics($events)
    {
        $eventIds = $events->pluck('id');

        return [
            'total_events' => $events->count(),
            'total_registrations' => EventRegistration::whereIn('event_id', $eventIds)->count(),
            'confirmed_registrations' => EventRegistration::whereIn('event_id', $eventIds)
                ->where('status', 'confirmed')
                ->count(),
            'total_revenue' => EventRegistration::whereIn('event_id', $eventIds)
                ->where('status', 'confirmed')
                ->sum('total_amount'),
            'average_registrations_per_event' => EventRegistration::whereIn('event_id', $eventIds)
                ->count() / max(1, $events->count()),
            'popular_categories' => DB::table('event_category')
                ->join('categories', 'event_category.category_id', '=', 'categories.id')
                ->whereIn('event_category.event_id', $eventIds)
                ->select('categories.name', DB::raw('count(*) as total'))
                ->groupBy('categories.id', 'categories.name')
                ->orderByDesc('total')
                ->limit(5)
                ->get(),
        ];
    }
}