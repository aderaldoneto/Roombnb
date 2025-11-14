<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->room->user_id === auth()->id();
    }

    public function rules(): array
    {
        return [
            'title'         => ['required', 'string', 'max:255'],
            'description'   => ['nullable', 'string', 'max:65000'],
            'city_id'       => ['required', 'integer', 'exists:cities,id'],
            'specialty_id'  => ['required', 'integer', 'exists:specialties,id'],
            'price'         => ['required', 'integer', 'min:1'],

            'delete_pictures'   => ['array'],
            'delete_pictures.*' => ['integer', 'exists:room_pictures,id'],

            'cover_id'          => ['nullable', 'integer', 'exists:room_pictures,id'],
            'cover_new_index'   => ['nullable', 'string'], 

            'new_pictures'      => ['array'],
            'new_pictures.*'    => ['file', 'image', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'price.integer' => 'O preço deve ser enviado em centavos (inteiro).',
            'pictures.*.image' => 'Cada arquivo deve ser uma imagem válida.',
            'pictures.*.max' => 'Cada imagem deve ter no máximo 4MB.',
        ];
    }
}
