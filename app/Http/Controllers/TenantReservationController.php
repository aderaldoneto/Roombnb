<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class TenantReservationController extends Controller
{
    /**
     * Lista as reservas dos quartos do tenant logado.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();

        $reservations = Reservation::with(['room.city'])
            // quartos cujo dono é o usuário logado (tenant)
            ->whereHas('room', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->latest('id')
            ->get()
            ->map(function (Reservation $reservation) {
                return [
                    'id'             => $reservation->id,
                    'status'         => $reservation->status,
                    'payment_method' => $reservation->payment_method,
                    'check_in'       => $reservation->check_in?->toDateString(),
                    'check_out'      => $reservation->check_out?->toDateString(),
                    'created_at'     => $reservation->created_at?->toDateString(),
                    'room'           => $reservation->room ? [
                        'id'    => $reservation->room->id,
                        'title' => $reservation->room->title,
                        'city'  => $reservation->room->city ? [
                            'name'  => $reservation->room->city->name,
                            'state' => $reservation->room->city->state,
                        ] : null,
                    ] : null,
                ];
            });

        return Inertia::render('Tenant/Index', [
            'reservations' => $reservations,
        ]);
    }

    /**
     * Tenant aceita ou recusa reserva em um quarto que é dele.
     */
    public function update(Request $request, Reservation $reservation): RedirectResponse
    {
        $user = $request->user();

        // garante que o quarto da reserva pertence ao tenant logado
        $reservation->loadMissing('room');
        if (! $reservation->room || $reservation->room->user_id !== $user->id) {
            abort(403);
        }

        $data = $request->validate([
            'status' => ['required', 'in:confirmed,canceled'],
        ]);

        // só permite mudar se ainda estiver pendente
        if ($reservation->status !== 'pending') {
            return back()->with('toast', 'Esta reserva já foi processada.');
        }

        $reservation->status = $data['status'];
        $reservation->save();

        $message = $data['status'] === 'confirmed'
            ? 'Reserva confirmada com sucesso!'
            : 'Reserva cancelada com sucesso.';

        return back()->with('toast', $message);
    }
}
