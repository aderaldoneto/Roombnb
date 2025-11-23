<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{

    public function index(Request $request)
    {
        $userId = $request->integer('user_id');

        if (!$userId) {
            return response()->json([
                'message' => 'user_id is required',
            ], 422);
        }

        $reservations = Reservation::with(['room.city', 'room.specialty'])
            ->where('user_id', $userId)
            ->latest('id')
            ->get()
            ->map(function (Reservation $r) {
                $room = $r->room;
                return [
                    'id'             => $r->id,
                    'status'         => $r->status,
                    'payment_method' => $r->payment_method,
                    'check_in'       => optional($r->check_in)->toDateString(),
                    'check_out'      => optional($r->check_out)->toDateString(),
                    'created_at'     => optional($r->created_at)->toDateTimeString(),
                    'room'           => $room ? [
                        'id'    => $room->id,
                        'title' => $room->title,
                        'city'  => $room->city ? [
                            'name'  => $room->city->name,
                            'state' => $room->city->state,
                        ] : null,
                        'specialty' => $room->specialty ? [
                            'name' => $room->specialty->name,
                        ] : null,
                    ] : null,
                ];
            });

        return response()->json([
            'data' => $reservations,
        ]);
    }

    public function store(Request $request, Room $room)
    {
        $validator = Validator::make($request->all(), [
            'user_id'        => ['required', 'integer', 'exists:users,id'],
            'check_in'       => ['required', 'date', 'before:check_out'],
            'check_out'      => ['required', 'date', 'after:check_in'],
            'payment_method' => ['nullable', Rule::in(['credit_card', 'cash', 'pix'])],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Dados inválidos!',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        $hasConflict = Reservation::where('room_id', $room->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where(function ($q) use ($data) {
                $q->where('check_in', '<', $data['check_out'])
                  ->where('check_out', '>', $data['check_in']);
            })
            ->exists();

        if ($hasConflict) {
            return response()->json([
                'message' => 'Esta acomodação não está disponível neste período.',
            ], 422);
        }

        $reservation = Reservation::create([
            'room_id'        => $room->id,
            'user_id'        => $data['user_id'],
            'check_in'       => $data['check_in'],
            'check_out'      => $data['check_out'],
            'status'         => 'pending',
            'payment_method' => $data['payment_method'] ?? null,
        ]);

        return response()->json([
            'data'    => [
                'id'             => $reservation->id,
                'status'         => $reservation->status,
                'payment_method' => $reservation->payment_method,
                'check_in'       => $reservation->check_in->toDateString(),
                'check_out'      => $reservation->check_out->toDateString(),
            ],
            'message' => 'Reserva criada com sucesso.',
        ], 201);
    }
}
