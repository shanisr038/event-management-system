<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Home Route
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');

/*
|--------------------------------------------------------------------------
| Dashboard Routes (Auth Required)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])
    ->prefix('dashboard')
    ->name('dashboard.')
    ->group(function () {

        Route::get('/', [DashboardController::class, 'index'])->name('index');
        Route::get('/upcoming', [DashboardController::class, 'upcomingEvents'])->name('upcoming');
        Route::get('/history', [DashboardController::class, 'registrationHistory'])->name('history');
        Route::get('/analytics/{event?}', [DashboardController::class, 'analytics'])->name('analytics');
    });

/*
|--------------------------------------------------------------------------
| Category Routes
|--------------------------------------------------------------------------
*/

Route::resource('categories', CategoryController::class)->except(['show']);

Route::get('/categories/{category}/events', 
    [CategoryController::class, 'events']
)->name('categories.events');

/*
|--------------------------------------------------------------------------
| PUBLIC EVENT ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/events', 
    [EventController::class, 'index']
)->name('events.index');

Route::get('/events/category/{category}', 
    [EventController::class, 'byCategory']
)->name('events.category');

/*
|--------------------------------------------------------------------------
| Registration (Public)
|--------------------------------------------------------------------------
*/

Route::get('/events/{event:slug}/register', 
    [RegistrationController::class, 'create']
)->name('events.register');

Route::post('/events/{event:slug}/register', 
    [RegistrationController::class, 'store']
)->name('events.register.store');

/*
|--------------------------------------------------------------------------
| AUTHENTICATED EVENT MANAGEMENT
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
// Admin User Management Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/users', [App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}/edit', [App\Http\Controllers\Admin\UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
    
    // Quick actions
    Route::post('/users/{user}/make-organizer', [App\Http\Controllers\Admin\UserController::class, 'makeOrganizer'])->name('users.make-organizer');
    Route::post('/users/{user}/remove-organizer', [App\Http\Controllers\Admin\UserController::class, 'removeOrganizer'])->name('users.remove-organizer');
    Route::post('/users/{user}/make-admin', [App\Http\Controllers\Admin\UserController::class, 'makeAdmin'])->name('users.make-admin');
    Route::post('/users/{user}/remove-admin', [App\Http\Controllers\Admin\UserController::class, 'removeAdmin'])->name('users.remove-admin');
});
    // Event CRUD
    Route::get('/events/create', 
        [EventController::class, 'create']
    )->name('events.create');

    Route::post('/events', 
        [EventController::class, 'store']
    )->name('events.store');

    Route::get('/events/{event:slug}/edit', 
        [EventController::class, 'edit']
    )->name('events.edit');

    Route::put('/events/{event:slug}', 
        [EventController::class, 'update']
    )->name('events.update');

    Route::delete('/events/{event:slug}', 
        [EventController::class, 'destroy']
    )->name('events.destroy');


    // Registrations list (Organizer)
    Route::get('/events/{event:slug}/registrations',
        [RegistrationController::class, 'index']
    )->name('events.registrations.index');


    // Check-in
    Route::post('/events/{event:slug}/registrations/{registration}/check-in',
        [RegistrationController::class, 'checkIn']
    )->name('events.registrations.check-in');


    // Ticket Management
    Route::prefix('events/{event:slug}')
        ->name('events.tickets.')
        ->group(function () {

            Route::get('/tickets', 
                [TicketController::class, 'index']
            )->name('index');

            Route::get('/tickets/create', 
                [TicketController::class, 'create']
            )->name('create');

            Route::post('/tickets', 
                [TicketController::class, 'store']
            )->name('store');

            Route::get('/tickets/{ticket}/edit', 
                [TicketController::class, 'edit']
            )->name('edit');

            Route::put('/tickets/{ticket}', 
                [TicketController::class, 'update']
            )->name('update');

            Route::delete('/tickets/{ticket}', 
                [TicketController::class, 'destroy']
            )->name('destroy');
        });


    // User Registrations
    Route::get('/my-registrations',
        [RegistrationController::class, 'myRegistrations']
    )->name('my-registrations');

    Route::get('/registrations/{registration}/confirmation',
        [RegistrationController::class, 'confirmation']
    )->name('registrations.confirmation');

    Route::post('/registrations/{registration}/cancel',
        [RegistrationController::class, 'cancel']
    )->name('registrations.cancel');
});

/*
|--------------------------------------------------------------------------
| IMPORTANT: SHOW ROUTE MUST BE LAST
|--------------------------------------------------------------------------
*/

Route::get('/events/{event:slug}', 
    [EventController::class, 'show']
)->name('events.show');


/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';