<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoomController;
use App\Models\City;
use App\Models\Room;
use App\Models\Specialty;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {

    $rooms = Room::with('city','coverPicture')
        ->latest('id')
        ->take(6)
        ->get()
        ->map(fn($r) => [
            'id' => $r->id,
            'title' => $r->title,
            'city_name' => $r->city->name . ' - ' . $r->city->state,
            'price' => $r->price, // accessor já em reais
            'rating_avg' => $r->rating_avg,
            'cover_url' => optional($r->coverPicture)->url,
        ]);

    return Inertia::render('Welcome', [
        'rooms' => $rooms,
        'cities' => City::orderBy('name')->get(['id','name','state']),
        'specialties' => Specialty::orderBy('name')->get(['id','name']),
        'filters' => [
            'city_id' => request('city_id'),
            'specialty_id' => request('specialty_id'),
            'check_in' => request('check_in'),
            'check_out' => request('check_out'),
        ],
    ]);
})->name('welcome');


Route::middleware(['auth','verified'])->group(function () {
    Route::get('/rooms/create', [RoomController::class, 'create'])->name('rooms.create');
    Route::post('/rooms', [RoomController::class, 'store'])->name('rooms.store');

    Route::get('/rooms/{room}', [RoomController::class, 'show'])->name('rooms.show');
});


// Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');



// Route::get('/', function () {
//     return Inertia::render('Welcome', [
//         'canLogin' => Route::has('login'),
//         'canRegister' => Route::has('register'),
//         'laravelVersion' => Application::VERSION,
//         'phpVersion' => PHP_VERSION,
//     ]);
// });

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
