<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoomRequest;
use App\Models\{Room, City, Specialty, RoomPicture};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class RoomController extends Controller
{
    public function create()
    {
        \Log::info("chegou aqui create");

        return Inertia::render('Rooms/Create', [
            'cities' => City::orderBy('name')->get(['id','name','state']),
            'specialties' => Specialty::orderBy('name')->get(['id','name']),
        ]);
    }

    public function store(StoreRoomRequest $request)
    {
        \Log::info("chegou aqui store");
        $user = $request->user();

        $data = $request->validated();

        return DB::transaction(function () use ($data, $user, $request) {
            $room = Room::create([
                'user_id'       => $user->id,
                'city_id'       => $data['city_id'],
                'specialty_id'  => $data['specialty_id'],
                'title'         => $data['title'],
                'description'   => $data['description'] ?? null,
                'price'         => $data['price'],
            ]);

            // fotos
            $coverId = null;
            if ($request->hasFile('pictures')) {
                $files = $request->file('pictures');
                foreach ($files as $i => $file) {
                    $path = $file->store('rooms', 'public'); 
                    $pic = RoomPicture::create([
                        'room_id' => $room->id,
                        'path'    => $path,
                        'is_cover'=> false,
                    ]);
                    if ((int)($data['cover_index'] ?? -1) === $i) {
                        $coverId = $pic->id;
                    }
                }
            }

            if ($coverId) {
                $room->cover_picture_id = $coverId;
                $room->save();
            } else {
                // se não escolher capa, define a primeira como capa
                $firstPic = $room->pictures()->first();
                if ($firstPic) {
                    $room->cover_picture_id = $firstPic->id;
                    $room->save();
                }
            }

            return redirect()->route('rooms.show', $room->id ?? null)->with('toast', 'Acomodação criada com sucesso!');
        });
    }
}
