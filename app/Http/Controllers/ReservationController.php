<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomPicture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class ReservationController extends Controller
{

    public function index(Request $request): Response
    {
        $user = $request->user();

        $reservations = Reservation::with(['room.city', 'room.specialty', 'room.coverPicture'])
            ->where('user_id', $user->id)
            ->latest('id')
            ->get()
            ->map(function (Reservation $reservation) {
                $room      = $reservation->room;
                $city      = $room?->city;
                $specialty = $room?->specialty;

                return [
                    'id'             => $reservation->id,
                    'status'         => $reservation->status,
                    'payment_method' => $reservation->payment_method,
                    'check_in'       => $reservation->check_in?->toDateString(),
                    'check_out'      => $reservation->check_out?->toDateString(),
                    'created_at'     => $reservation->created_at?->toDateString(),
                    'room'           => $room ? [
                        'id'          => $room->id,
                        'title'       => $room->title,
                        'city'        => $city ? [
                            'name'  => $city->name,
                            'state' => $city->state,
                        ] : null,
                        'specialty'   => $specialty ? [
                            'name' => $specialty->name,
                        ] : null,
                        'cover_url'   => $room->coverPicture?->url,
                    ] : null,
                ];
            });

        return Inertia::render('Reservations/List', [
            'reservations' => $reservations,
        ]);
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Room  $room
     * @return \Inertia\Response
     */
    public function create(Request $request, Room $room): Response
    {
        // Impedir que o dono do quarto reserve o próprio quarto:
        if ($request->user()->id === $room->user_id) {
            abort(403, 'Você não pode reservar o seu próprio anúncio.');
        }

        $room->load([
            'city:id,name,state',
            'specialty:id,name',
            'pictures' => function ($q) {
                $q->orderByDesc('id');
            },
        ]);

        $mapPic = function (RoomPicture $p) {
            return [
                'id'       => $p->id,
                'url'      => Storage::disk('public')->url($p->path),
                'is_cover' => (bool) $p->is_cover || false,
            ];
        };

        $coverUrl = null;
        if ($room->cover_picture_id) {
            $pic = $room->pictures->firstWhere('id', $room->cover_picture_id);
            $coverUrl = $pic ? Storage::disk('public')->url($pic->path) : null;
        } else {
            $first = $room->pictures->first();
            $coverUrl = $first ? Storage::disk('public')->url($first->path) : null;
        }

        return Inertia::render('Reservations/Create', [
            'room' => [
                'id'           => $room->id,
                'title'        => $room->title,
                'description'  => $room->description,
                'price'        => (int) $room->price,
                'rating_avg'   => $room->rating_avg,
                'city_id'      => $room->city_id,
                'specialty_id' => $room->specialty_id,
                'city'         => $room->city ? [
                    'id'    => $room->city->id,
                    'name'  => $room->city->name,
                    'state' => $room->city->state,
                ] : null,
                'specialty'    => $room->specialty ? [
                    'id'   => $room->specialty->id,
                    'name' => $room->specialty->name,
                ] : null,
                'pictures'         => $room->pictures->map($mapPic)->values(),
                'cover_picture_id' => $room->cover_picture_id,
                'cover_url'        => $coverUrl,
            ],
        ]);
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Room  $room
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, Room $room): RedirectResponse
    {
        $user = $request->user();

        // if ($user->id === $room->user_id) {
        //     abort(403, 'Você não pode reservar o seu próprio anúncio.');
        // }

        $data = $request->validate([
            'check_in'       => ['required', 'date', 'after_or_equal:today'],
            'check_out'      => ['required', 'date', 'after:check_in'],
            'payment_method' => ['required', 'in:credit_card,cash,pix'],
        ]);

        return DB::transaction(function () use ($data, $room, $user) {
            // Verificar conflito de datas (reservas pendentes ou confirmadas)
            $hasConflict = Reservation::where('room_id', $room->id)
                ->whereIn('status', ['pending', 'confirmed'])
                ->where(function ($query) use ($data) {
                    $query
                        ->where('check_in', '<', $data['check_out'])
                        ->where('check_out', '>', $data['check_in']);
                })
                ->exists();

            if ($hasConflict) {
                return back()
                    ->withErrors([
                        'check_in' => 'Este quarto já está reservado neste período.',
                        'check_out' => 'Este quarto já está reservado neste período.',
                    ])
                    ->withInput();
            }

            Reservation::create([
                'room_id'        => $room->id,
                'user_id'        => $user->id,
                'check_in'       => $data['check_in'],
                'check_out'      => $data['check_out'],
                'status'         => 'pending', // ou deixa para o default da migration
                'payment_method' => $data['payment_method'],
            ]);

            return redirect()
                ->route('rooms.show', $room->id)
                ->with('toast', 'Reserva criada com sucesso! 🎉');
        });
    }
}
