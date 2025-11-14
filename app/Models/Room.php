<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'city_id',
        'specialty_id',
        'title',
        'description',
        'price',
        'rating_avg',
    ];

    protected $casts = [
        'rating_avg' => 'integer',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function host(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return BelongsTo<City, $this>
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * @return BelongsTo<Specialty, Room>
     */
    public function specialty(): BelongsTo
    {
        return $this->belongsTo(Specialty::class);
    }

    /**
     * @return HasMany<Reservation, $this>
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * @return HasMany<RoomPicture, Room>
     */
    public function pictures(): HasMany
    {
        return $this->hasMany(RoomPicture::class)->orderBy('sort_order')->orderBy('id');
    }

    /**
     * @return HasOne<RoomPicture, Room>
     */
    public function coverPicture(): HasOne
    {
        return $this->hasOne(RoomPicture::class)->where('is_cover', true);
    }

    /**
     * @return BelongsTo<User, Room>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
