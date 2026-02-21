<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index()
    {
        $categories = Category::withCount('events')
            ->orderBy('name')
            ->paginate(12);

        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        $this->authorize('create', Category::class);
        
        return view('categories.create');
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Category::class);

        $validated = $request->validate([
            'name' => 'required|max:255|unique:categories',
            'description' => 'nullable|max:1000',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:50',
            'is_active' => 'sometimes|boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        Category::create($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Category created successfully.');
    }

    /**
     * Display the specified category and its events.
     */
    public function show(Category $category)
    {
        $events = $category->events()
            ->with('organizer')
            ->published()
            ->upcoming()
            ->latest()
            ->paginate(9);

        return view('categories.show', compact('category', 'events'));
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(Category $category)
    {
        $this->authorize('update', $category);
        
        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, Category $category)
    {
        $this->authorize('update', $category);

        $validated = $request->validate([
            'name' => 'required|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|max:1000',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:50',
            'is_active' => 'sometimes|boolean',
        ]);

        // Update slug only if name changed
        if ($category->name !== $validated['name']) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $category->update($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(Category $category)
    {
        $this->authorize('delete', $category);

        // Check if category has events
        if ($category->events()->count() > 0) {
            return back()->with('error', 'Cannot delete category with associated events.');
        }

        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', 'Category deleted successfully.');
    }

    /**
     * Display events by category (alternative to show method).
     */
    public function events(Category $category)
    {
        $events = $category->events()
            ->with('organizer', 'categories')
            ->published()
            ->upcoming()
            ->latest()
            ->paginate(9);

        return view('events.index', [
            'events' => $events,
            'category' => $category,
        ]);
    }
}