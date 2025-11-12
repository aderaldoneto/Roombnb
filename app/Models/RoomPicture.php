<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class RoomPicture extends Model
{
    protected $fillable = [
        'room_id', 'disk','path', 'original_name', 'mime_type', 'size',
        'width', 'height', 'is_cover', 'sort_order', 'caption',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * @return string|null
     */
    public function getUrlAttribute(): ?string
    {
        // return Storage::url($this->path); 
        return $this->path
            ? Storage::url($this->path)
            : null;
    }
}
