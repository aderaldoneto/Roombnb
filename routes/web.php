<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\TenantDashboardController;
use App\Http\Controllers\TenantReservationController;
use App\Models\City;
use App\Models\Room;
use App\Models\Specialty;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::prefix('admin')->name('admin.')->group(function () {

    Route::get('/', function () {
        if (!auth()->check()) {
            return redirect()->route('admin.login');
        }

        if (!auth()->user()->hasRole('admin')) {
            abort(403);
        }

        return redirect()->route('admin.dashboard');
    })->name('home');

    Route::middleware('guest')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'create'])
            ->name('login');
        Route::post('/login', [AdminAuthController::class, 'store'])
            ->name('login.store');
    });

    Route::post('/logout', [AdminAuthController::class, 'destroy'])
        ->middleware('auth')
        ->name('logout');

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/dashboard', [AdminController::class, 'index'])
            ->name('dashboard');
        
       Route::get('/users/create', [AdminController::class, 'create'])
            ->name('users.create');

        Route::post('/users', [AdminController::class, 'store'])
            ->name('users.store');

    });
});

Route::get('/', function () {
    $rooms = Room::with(['city', 'specialty', 'coverPicture'])
        ->latest('id')
        ->take(6)
        ->get()
        ->map(fn ($r) => [
            'id'             => $r->id,
            'title'          => $r->title,
            'city_name'      => $r->city->name . ' - ' . $r->city->state,
            'specialty_name' => $r->specialty->name,
            'price'          => $r->price,  
            'rating_avg'     => $r->rating_avg,
            'cover_url'      => optional($r->coverPicture)->url,
        ]);

    return Inertia::render('Welcome', [
        'rooms'       => $rooms,
        'cities'      => City::orderBy('name')->get(['id', 'name', 'state']),
        'specialties' => Specialty::orderBy('name')->get(['id', 'name']),
        'filters'     => [
            'city_id'      => request('city_id'),
            'specialty_id' => request('specialty_id'),
            'check_in'     => request('check_in'),
            'check_out'    => request('check_out'),
        ],
        'canLogin'      => Route::has('login'),
        'canRegister'   => Route::has('register'),
        'laravelVersion'=> Application::VERSION,
        'phpVersion'    => PHP_VERSION,
    ]);
})->name('welcome');

Route::get('/rooms/{room}', [RoomController::class, 'show'])
    ->whereNumber('room')
    ->name('rooms.show');


Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/rooms', [RoomController::class, 'index'])
        ->name('rooms.index');
    Route::get('/rooms/create', [RoomController::class, 'create'])
        ->name('rooms.create');
    Route::post('/rooms', [RoomController::class, 'store'])
        ->name('rooms.store');
    Route::get('/rooms/{room}/edit', [RoomController::class, 'edit'])
        ->name('rooms.edit');
    Route::put('/rooms/{room}', [RoomController::class, 'update'])
        ->name('rooms.update');
    Route::delete('/rooms/{room}', [RoomController::class, 'destroy'])
        ->name('rooms.destroy');

    Route::get('/rooms/{room}/reservations/create', [ReservationController::class, 'create'])
        ->name('reservations.create');
    Route::post('/rooms/{room}/reservations', [ReservationController::class, 'store'])
        ->name('reservations.store');
    Route::get('/rooms/list', [RoomController::class, 'list'])
        ->name('rooms.list');

    Route::get('/tenant/dashboard', [TenantDashboardController::class, 'index'])
        ->name('tenant.dashboard');
    Route::get('/reservations', [TenantReservationController::class, 'index'])
    ->name('tenant.index');

    // Route::put('/reservations/{reservation}', [TenantReservationController::class, 'update'])
    //     ->name('reservations.update');

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});


require __DIR__ . '/auth.php';
