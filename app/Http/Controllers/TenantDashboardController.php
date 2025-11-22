<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Room;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TenantDashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        $rooms = Room::where('user_id', $user->id)->pluck('id');

        $stats = [
            'total_reservations' => Reservation::whereIn('room_id', $rooms)->count(),
            'pending'            => Reservation::whereIn('room_id', $rooms)->where('status', 'pending')->count(),
            'confirmed'          => Reservation::whereIn('room_id', $rooms)->where('status', 'confirmed')->count(),
            'canceled'           => Reservation::whereIn('room_id', $rooms)->where('status', 'canceled')->count(),
            'total_rooms'        => $rooms->count(),
        ];

        return Inertia::render('Tenant/Dashboard', [
            'stats' => $stats,
        ]);
    }
}
