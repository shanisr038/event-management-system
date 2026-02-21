<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class EventController extends Controller
{

    public function index()
    {
        $events = Event::with('organizer', 'categories')
            ->published()
            ->upcoming()
            ->latest()
            ->paginate(9);

        return view('events.index', compact('events'));
    }

    public function create()
    {
        // Get the current user
        $user = Auth::user();

        // Simple role check
        if (!$user->hasRole('organizer') && !$user->hasRole('admin')) {
            abort(403, 'You need to be an organizer or admin to create events.');
        }

        // Get ALL categories (not just active) to match your test
        $categories = Category::all();

        // Log for debugging
        Log::info('Create page loaded with ' . $categories->count() . ' categories');

        return view ('events.create', compact('categories'));
        // return view('events.create', compact('categories'));
    }
    public function testView()
    {
        $user = Auth::user();

        if (!$user->hasRole('organizer') && !$user->hasRole('admin')) {
            abort(403);
        }

        $categories = Category::all();
        return view('events.test', compact('categories'));
    }
    /**
     * Store a newly created event in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Event::class);

        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
            'start_date' => 'required|date|after:now',
            'end_date' => 'required|date|after:start_date',
            'venue' => 'required|max:255', // Changed from 'location' to match migration
            'capacity' => 'required|integer|min:1', // Changed from 'max_attendees' to match migration
            'banner_image' => 'nullable|image|max:2048', // Added image validation
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'is_published' => 'sometimes|boolean'
        ]);

        // Handle banner image upload
        if ($request->hasFile('banner_image')) {
            $path = $request->file('banner_image')->store('events/banners', 'public');
            $validated['banner_image'] = $path;
        }

        $validated['organizer_id'] = Auth::id();
        $validated['slug'] = Str::slug($validated['title']);

        $event = Event::create($validated);

        if (isset($validated['categories'])) {
            $event->categories()->attach($validated['categories']);
        }

        return redirect()->route('events.show', $event->slug)
            ->with('success', 'Event created successfully!');
    }

    /**
     * Display the specified event.
     */
    public function show(Event $event)
    {
        // Only show published events to everyone
        if (!$event->is_published && Auth::id() !== $event->organizer_id) {
            abort(404);
        }

        $event->load(['organizer', 'categories', 'tickets' => function ($query) {
            $query->available();
        }]);

        $userRegistration = null;
        if (Auth::check()) {
            $userRegistration = $event->registrations()
                ->where('user_id', Auth::id())
                ->where('status', 'confirmed')
                ->first();
        }

        return view('events.show', compact('event', 'userRegistration'));
    }

    /**
     * Show the form for editing the specified event.
     */
    public function edit(Event $event)
    {
        $this->authorize('update', $event);

        $categories = Category::active()->get();
        $eventCategories = $event->categories->pluck('id')->toArray();

        return view('events.edit', compact('event', 'categories', 'eventCategories'));
    }

    /**
     * Update the specified event in storage.
     */
    public function update(Request $request, Event $event)
    {
        $this->authorize('update', $event);

        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'venue' => 'required|max:255',
            'capacity' => 'required|integer|min:1',
            'banner_image' => 'nullable|image|max:2048',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'is_published' => 'sometimes|boolean'
        ]);

        // Handle banner image upload
        if ($request->hasFile('banner_image')) {
            $path = $request->file('banner_image')->store('events/banners', 'public');
            $validated['banner_image'] = $path;
        }

        // Update slug only if title changed
        if ($event->title !== $validated['title']) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $event->update($validated);

        if (isset($validated['categories'])) {
            $event->categories()->sync($validated['categories']);
        } else {
            $event->categories()->detach();
        }

        return redirect()->route('events.show', $event->slug)
            ->with('success', 'Event updated successfully!');
    }

    /**
     * Remove the specified event from storage.
     */
    public function destroy(Event $event)
    {
        $this->authorize('delete', $event);

        // Check if event has registrations
        if ($event->registrations()->where('status', 'confirmed')->exists()) {
            return back()->with('error', 'Cannot delete event with confirmed registrations.');
        }

        $event->delete();

        return redirect()->route('events.index')
            ->with('success', 'Event deleted successfully!');
    }

    /**
     * Display events by category.
     */
    public function byCategory(Category $category)
    {
        $events = $category->events()
            ->published()
            ->upcoming()
            ->with('organizer')
            ->latest()
            ->paginate(9);

        return view('events.index', compact('events', 'category'));
    }
    /**
     * Test method to verify categories are accessible
     */
    public function testCategories()
    {
        try {
            $categories = Category::all();
            return response()->json([
                'success' => true,
                'count' => $categories->count(),
                'categories' => $categories->map(function ($cat) {
                    return ['id' => $cat->id, 'name' => $cat->name];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
