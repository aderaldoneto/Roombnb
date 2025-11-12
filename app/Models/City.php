<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    use HasFactory;
    protected $fillable = ['name','state'];

    /**
     * @return HasMany<Room, City>
     */
    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    /**
     * @param mixed $query
     * @param string $uf
     * @return mixed
     */
    public function scopeByUf($query, string $uf): mixed
    {
        return $query->where('state', strtoupper($uf));
    }
}
