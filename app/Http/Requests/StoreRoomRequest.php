<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'title'         => ['required','string','max:255'],
            'description'   => ['nullable','string','max:5000'],
            'city_id'       => ['required','exists:cities,id'],
            'specialty_id'  => ['required','exists:specialties,id'],
            'price'         => ['required','integer','min:0'], 

            // imagens
            'pictures'      => ['array','max:10'],
            'pictures.*'    => ['file','mimes:jpg,jpeg,png,webp','max:4096'],
            'cover_index'   => ['nullable','integer','min:0','max:9'],
        ];
    }
}
