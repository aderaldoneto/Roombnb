<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use OpenApi\Annotations as OA;


class ReservationController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/v1/reservations",
     *     tags={"Reservations"},
     *     summary="Lista reservas de um usuário",
     *     description="Retorna todas as reservas associadas ao user_id informado.",
     *
     *     @OA\Parameter(
     *         name="user_id",
     *         in="query",
     *         required=true,
     *         description="ID do usuário (tenant) dono das reservas",
     *         @OA\Schema(type="integer", example=4)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Lista de reservas do usuário",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=15),
     *                     @OA\Property(property="status", type="string", example="pending"),
     *                     @OA\Property(property="payment_method", type="string", nullable=true, example="pix"),
     *                     @OA\Property(property="check_in", type="string", format="date", example="2025-02-11"),
     *                     @OA\Property(property="check_out", type="string", format="date", example="2025-02-12"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-01-10T12:34:56"),
     *
     *                     @OA\Property(
     *                         property="room",
     *                         type="object",
     *                         nullable=true,
     *                         @OA\Property(property="id", type="integer", example=7),
     *                         @OA\Property(property="title", type="string", example="Consultório no Centro"),
     *                         @OA\Property(
     *                             property="city",
     *                             type="object",
     *                             nullable=true,
     *                             @OA\Property(property="name", type="string", example="Salvador"),
     *                             @OA\Property(property="state", type="string", example="BA")
     *                         ),
     *                         @OA\Property(
     *                             property="specialty",
     *                             type="object",
     *                             nullable=true,
     *                             @OA\Property(property="name", type="string", example="Psicologia")
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Parâmetro user_id ausente ou inválido",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="user_id is required")
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/v1/rooms/{room}/reservations",
     *     tags={"Reservations"},
     *     summary="Cria uma nova reserva para um room",
     *     @OA\Parameter(
     *         name="room",
     *         in="path",
     *         required=true,
     *         description="ID do room",
     *         @OA\Schema(type="integer", example=7)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id","check_in","check_out"},
     *             @OA\Property(property="user_id", type="integer", example=4),
     *             @OA\Property(property="check_in", type="string", format="date", example="2025-02-11"),
     *             @OA\Property(property="check_out", type="string", format="date", example="2025-02-12"),
     *             @OA\Property(property="payment_method", type="string", enum={"credit_card","cash","pix"}, example="pix")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Reserva criada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=10),
     *                 @OA\Property(property="status", type="string", example="pending"),
     *                 @OA\Property(property="payment_method", type="string", example="pix"),
     *                 @OA\Property(property="check_in", type="string", format="date", example="2025-02-11"),
     *                 @OA\Property(property="check_out", type="string", format="date", example="2025-02-12")
     *             ),
     *             @OA\Property(property="message", type="string", example="Reserva criada com sucesso.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação ou conflito de datas",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Os dados enviados são inválidos.")
     *         )
     *     )
     * )
     */
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
