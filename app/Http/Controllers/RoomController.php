<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoomRequest;
use App\Http\Requests\UpdateRoomRequest;
use App\Models\{Room, City, Specialty, RoomPicture};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Inertia\Response;

class RoomController extends Controller
{

    public function index(Request $request): Response
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

        $rooms = $query
            ->latest('id')
            ->get()
            ->map(fn ($r) => [
                'id'             => $r->id,
                'title'          => $r->title,
                'city_name'      => $r->city?->name . ' - ' . $r->city?->state,
                'specialty_name' => $r->specialty?->name,
                'price'          => $r->price,
                'rating_avg'     => $r->rating_avg,
                'cover_url'      => optional($r->coverPicture)->url,
            ])
            ->values();

        return Inertia::render('Welcome', [
            'rooms'       => $rooms,
            'cities'      => City::orderBy('name')->get(['id', 'name', 'state']),
            'specialties' => Specialty::orderBy('name')->get(['id', 'name']),
            'filters'     => [
                'city_id'      => $cityId,
                'specialty_id' => $specialtyId,
                'check_in'     => $checkIn,
                'check_out'    => $checkOut,
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('Rooms/Create', [
            'cities' => City::orderBy('name')->get(['id','name','state']),
            'specialties' => Specialty::orderBy('name')->get(['id','name']),
        ]);
    }

    public function store(StoreRoomRequest $request)
    {
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
            $coverId = (int)$data['cover_index'] ?? 0;
            if ($request->hasFile('pictures')) {
                $files = $request->file('pictures');
                foreach ($files as $i => $file) {
                    $path = $file->store('rooms', 'public'); 
                    $pic = RoomPicture::create([
                        'room_id' => $room->id,
                        'path'    => $path,
                        'is_cover'=> false,
                    ]);
                    if ($coverId === $i) {
                        $pic->update(['is_cover' => true]);
                    }
                }
            }

            return redirect()->route('rooms.show', $room->id ?? null)->with('toast', 'Acomodação criada com sucesso!');
        });
    }

    public function show(Room $room)
    {
        $room->load([
            'city:id,name,state', 'specialty:id,name', 'user:id,name,email', 'pictures' => function($q){
                $q->orderByDesc('id');
        }]);

        $mapPic = function (RoomPicture $p) {
            return [
                'id'      => $p->id,
                'url'     => Storage::disk('public')->url($p->path),
                'is_cover'=> (bool) $p->is_cover || false,
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

        return Inertia::render('Rooms/Show', [
            'room' => [
                'id'           => $room->id,
                'user_id'      => $room->user_id,
                'title'        => $room->title,
                'description'  => $room->description,
                'price'        => (int) $room->price,
                'rating_avg'   => $room->rating_avg,
                'city_id'      => $room->city_id,
                'specialty_id' => $room->specialty_id,
                'city'         => $room->city ? [
                    'id' => $room->city->id,
                    'name' => $room->city->name,
                    'state'=> $room->city->state,
                ] : null,
                'specialty'    => $room->specialty ? [
                    'id' => $room->specialty->id,
                    'name' => $room->specialty->name,
                ] : null,
                'owner'        => $room->user ? [
                    'id' => $room->user->id,
                    'name' => $room->user->name,
                    'email'=> $room->user->email,
                ] : null,
                'pictures'     => $room->pictures->map($mapPic)->values(),
                'cover_picture_id' => $room->cover_picture_id,
                'cover_url'    => $coverUrl,
            ],
        ]);
    }

    public function list(Request $request)
    {
        $user = $request->user();

        $rooms = Room::with(['city', 'coverPicture'])
            ->where('user_id', $user->id)
            ->latest('id')
            ->get()
            ->map(function ($room) {
                return [
                    'id'         => $room->id,
                    'title'      => $room->title,
                    'city_name'  => $room->city
                        ? $room->city->name . ' - ' . $room->city->state
                        : '—',
                    'price'      => $room->price,
                    'cover_url'  => $room->coverPicture?->url,
                ];
            });

        return Inertia::render('Rooms/List', [
            'rooms' => $rooms,
        ]);
    }



    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Room $room
     * @return \Inertia\Response
     */
    public function edit(Request $request, Room $room): \Inertia\Response
    {
        abort_if($request->user()->id !== $room->user_id, 403);

        $room->load([
            'city:id,name,state', 'specialty:id,name', 'pictures' => function($q){
                $q->orderByDesc('id');
        }]);
        
        $existingPictures = $room->pictures->map(function (RoomPicture $p) {
            return [
                'id'       => $p->id,
                'url'      => Storage::disk('public')->url($p->path),
                'is_cover' => (bool) $p->is_cover || $p->id === $p->room->cover_picture_id,
            ];
        })->values();

        return Inertia::render('Rooms/Edit', [
            'room' => [
                'id'               => $room->id,
                'title'            => $room->title,
                'description'      => $room->description,
                'price'            => (int) $room->price,
                'city_id'          => $room->city_id,
                'specialty_id'     => $room->specialty_id,
                'pictures'         => $existingPictures,
                'cover_picture_id' => $room->cover_picture_id,
                'cover_url'        => $existingPictures->firstWhere('id', $room->cover_picture_id)['url'] ?? null,
            ],
            'cities'      => City::orderBy('name')->get(['id','name','state']),
            'specialties' => Specialty::orderBy('name')->get(['id','name']),
        ]);
    }

    /**
     * @param UpdateRoomRequest $request
     * @param Room $room
     * @return mixed
     */
    public function update(UpdateRoomRequest $request, Room $room): mixed
    {
        abort_if($request->user()->id !== $room->user_id, 403);
        $data = $request->validated();

        return DB::transaction(function () use ($data, $request, $room) {
            $room->update([
                'title'        => $data['title'],
                'description'  => $data['description'] ?? null,
                'city_id'      => $data['city_id'],
                'specialty_id' => $data['specialty_id'],
                'price'        => $data['price'],
            ]);

            $deleteIds = collect($data['delete_pictures'] ?? [])
                ->map(fn($v) => (int)$v)
                ->filter();

            if ($deleteIds->isNotEmpty()) {
                $pics = RoomPicture::where('room_id', $room->id)
                    ->whereIn('id', $deleteIds)
                    ->get();

                foreach ($pics as $p) {
                    if ($room->id === $p->id) {
                        if ($room->is_cover) {
                            $room->is_cover = null;
                        }
                    }
                    Storage::disk('public')->delete($p->path);
                    $p->delete();
                }
            }

            // Novas fotos 
            $createdPics = [];
            if ($request->hasFile('new_pictures')) { 
                $files = $request->file('new_pictures');
                foreach ($files as $file) {
                    $path = $file->store('rooms', 'public');
                    $createdPics[] = RoomPicture::create([
                        'room_id' => $room->id,
                        'path'    => $path,
                        'is_cover'=> false,
                    ]);
                }
            }  

            // Seleção de capa
            $coverNewIndex = $data['cover_new_index'] ?? null; 
            $coverId       = $data['cover_id'] ?? null; 

            if ($coverNewIndex !== null && $coverNewIndex !== '') {
                $idx = (int) $coverNewIndex;
                $chosen = $createdPics[$idx] ?? null;
                if ($chosen) {
                    $pic = RoomPicture::where('id', $chosen->id);
                    if ($pic->exists()) {
                        $pic->update(['is_cover' => true]);
                    } 
                } 
            } elseif ($coverId !== null && $coverId !== '') {
                $coverId = (int) $coverId;
                $pics = RoomPicture::where([
                    'room_id' => $room->id, 
                    'is_cover' => true])->get();
                foreach ($pics as $pic) {
                    $pic->update(['is_cover' => false]);
                }

                $pic = RoomPicture::where('room_id', $room->id)->where('id', $coverId);
                if ($pic->exists()) {
                    $pic->update(['is_cover' => true]);
                }
            }

            // Se ainda não há capa, define a primeira disponível
            $pics = RoomPicture::where([
                    'room_id' => $room->id, 
                    'is_cover' => true])->get();
                    
            if ($pics->isEmpty()) {
                $first = $room->pictures()->first();
                if ($first) {
                    RoomPicture::where('id', $first->id)
                        ->update(['is_cover' => true]);
                }
            } 


            $room->save();

            return redirect()->route('rooms.show', $room->id)
                ->with('toast', 'Acomodação atualizada com sucesso!');
        });
    }

}
