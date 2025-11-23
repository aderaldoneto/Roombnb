<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\City;
use App\Models\Specialty;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;


class RoomController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/v1/rooms",
     *     tags={"Rooms"},
     *     summary="Lista rooms disponíveis",
     *     @OA\Parameter(
     *         name="city_id",
     *         in="query",
     *         description="Filtrar por cidade",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="specialty_id",
     *         in="query",
     *         description="Filtrar por especialidade",
     *         required=false,
     *         @OA\Schema(type="integer", example=2)
     *     ),
     *     @OA\Parameter(
     *         name="check_in",
     *         in="query",
     *         description="Data de check-in (YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(type="string", format="date", example="2025-02-11")
     *     ),
     *     @OA\Parameter(
     *         name="check_out",
     *         in="query",
     *         description="Data de check-out (YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(type="string", format="date", example="2025-02-12")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de rooms",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="Consultório Centro"),
     *                     @OA\Property(property="price", type="integer", example=15000),
     *                     @OA\Property(property="rating_avg", type="integer", example=5),
     *                     @OA\Property(property="cover_url", type="string", nullable=true)
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $cityId      = $request->integer('city_id');
        $specialtyId = $request->integer('specialty_id');
        $checkIn     = $request->input('check_in');
        $checkOut    = $request->input('check_out');

        $query = Room::query()
            ->with(['city', 'specialty', 'coverPicture']);

        if ($cityId) {
            $query->where('city_id', $cityId);
        }

        if ($specialtyId) {
            $query->where('specialty_id', $specialtyId);
        }

        if ($checkIn && $checkOut) {
            $query->whereDoesntHave('reservations', function ($q) use ($checkIn, $checkOut) {
                $q->whereIn('status', ['pending', 'confirmed'])
                    ->where(function ($q2) use ($checkIn, $checkOut) {
                        $q2->where('check_in', '<', $checkOut)
                           ->where('check_out', '>', $checkIn);
                    });
            });
        }

        $perPage = $request->integer('per_page', 10);

        $rooms = $query->paginate($perPage);

        $data = $rooms->getCollection()->map(function (Room $r) {
            return [
                'id'             => $r->id,
                'title'          => $r->title,
                'description'    => $r->description,
                'city'           => $r->city ? [
                    'id'    => $r->city->id,
                    'name'  => $r->city->name,
                    'state' => $r->city->state,
                ] : null,
                'specialty'      => $r->specialty ? [
                    'id'   => $r->specialty->id,
                    'name' => $r->specialty->name,
                ] : null,
                'price'          => (int) $r->price,
                'rating_avg'     => $r->rating_avg,
                'cover_url'      => optional($r->coverPicture)->url,
            ];
        });

        return response()->json([
            'data' => $data,
            'meta' => [
                'current_page' => $rooms->currentPage(),
                'last_page'    => $rooms->lastPage(),
                'per_page'     => $rooms->perPage(),
                'total'        => $rooms->total(),
            ],
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/rooms/{room}",
     *     tags={"Rooms"},
     *     summary="Exibe os detalhes completos de um room",
     *
     *     @OA\Parameter(
     *         name="room",
     *         in="path",
     *         required=true,
     *         description="ID do room",
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Detalhes do room",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=10),
     *                 @OA\Property(property="title", type="string", example="Consultório no Centro"),
     *                 @OA\Property(property="description", type="string", example="Sala climatizada com recepção."),
     *
     *                 @OA\Property(property="city", type="object",
     *                     nullable=true,
     *                     @OA\Property(property="id", type="integer", example=3),
     *                     @OA\Property(property="name", type="string", example="Salvador"),
     *                     @OA\Property(property="state", type="string", example="BA")
     *                 ),
     *
     *                 @OA\Property(property="specialty", type="object",
     *                     nullable=true,
     *                     @OA\Property(property="id", type="integer", example=7),
     *                     @OA\Property(property="name", type="string", example="Fisioterapia")
     *                 ),
     *
     *                 @OA\Property(property="price", type="integer", example=15000, description="Preço em centavos"),
     *                 @OA\Property(property="rating_avg", type="integer", example=4),
     *
     *                 @OA\Property(property="cover_url", type="string", nullable=true, example="https://site.com/storage/rooms/abc.jpg"),
     *
     *                 @OA\Property(property="pictures", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=55),
     *                         @OA\Property(property="url", type="string", example="https://site.com/storage/rooms/pic1.jpg"),
     *                         @OA\Property(property="is_cover", type="boolean", example=false)
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Room não encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Room not found.")
     *         )
     *     )
     * )
     */
    public function show(Room $room)
    {
        $room->load(['city', 'specialty', 'coverPicture', 'pictures']);

        return response()->json([
            'data' => [
                'id'          => $room->id,
                'title'       => $room->title,
                'description' => $room->description,
                'city'        => $room->city ? [
                    'id'    => $room->city->id,
                    'name'  => $room->city->name,
                    'state' => $room->city->state,
                ] : null,
                'specialty'   => $room->specialty ? [
                    'id'   => $room->specialty->id,
                    'name' => $room->specialty->name,
                ] : null,
                'price'       => (int) $room->price,
                'rating_avg'  => $room->rating_avg,
                'cover_url'   => optional($room->coverPicture)->url,
                'pictures'    => $room->pictures->map(fn ($p) => [
                    'id'  => $p->id,
                    'url' => $p->url,
                    'is_cover' => (bool) $p->is_cover,
                ]),
            ],
        ]);
    }
}
