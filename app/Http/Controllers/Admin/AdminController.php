<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Room;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminController extends Controller
{
    /**
     * Dashboard principal do painel admin
     */
    public function index(Request $request): Response
    {
        // Contagens básicas
        $totalUsers        = User::count();
        $totalRooms        = Room::count();
        $totalReservations = Reservation::count();

        // Contagem por role
        $totalAdmins  = User::whereHas('roles', fn ($q) => $q->where('slug', 'admin'))->count();
        $totalHosts   = User::whereHas('roles', fn ($q) => $q->where('slug', 'host'))->count();
        $totalTenants = User::whereHas('roles', fn ($q) => $q->where('slug', 'tenant'))->count();

        // Reservas por status
        $reservationsByStatus = Reservation::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status'); // ['pending' => X, 'confirmed' => Y, 'canceled' => Z]

        $stats = [
            'total_users'         => $totalUsers,
            'total_admins'        => $totalAdmins,
            'total_hosts'         => $totalHosts,
            'total_tenants'       => $totalTenants,
            'total_rooms'         => $totalRooms,
            'total_reservations'  => $totalReservations,
            'reservations_status' => [
                'pending'   => $reservationsByStatus['pending']   ?? 0,
                'confirmed' => $reservationsByStatus['confirmed'] ?? 0,
                'canceled'  => $reservationsByStatus['canceled']  ?? 0,
            ],
        ];

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
        ]);
    }

    /**
     * Form para criar novo usuário admin
     */
    public function create(): Response
    {
        return Inertia::render('Admin/Users/Create');
    }

    /**
     * Salva novo usuário e adiciona role admin
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        $user->assignRole('admin');

        return redirect()
            ->route('admin.dashboard')
            ->with('toast', 'Usuário administrador criado com sucesso!');
    }
}
