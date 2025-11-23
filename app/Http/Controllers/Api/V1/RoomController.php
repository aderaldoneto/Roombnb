<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\City;
use App\Models\Specialty;
use Illuminate\Http\Request;

class RoomController extends Controller
{

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
