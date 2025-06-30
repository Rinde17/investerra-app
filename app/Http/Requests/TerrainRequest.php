<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TerrainRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Ou ajouter ta logique d'autorisation ici
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'surface_m2' => ['required', 'numeric', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
            'city' => ['required', 'string', 'max:255'],
            'zip_code' => ['required', 'string', 'max:10'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'viabilised' => ['boolean'],
            'source_url' => ['nullable', 'url', 'max:255'],
            'source_platform' => ['nullable', 'string', 'max:255'],
        ];
    }
}
