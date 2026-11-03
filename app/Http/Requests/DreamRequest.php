<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DreamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'dream_name' => ['nullable', 'string', 'max:255'],
            'dream_price' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'dream_name.max' => 'Название мечты не должно превышать 255 символов.',
            'dream_price.numeric' => 'Стоимость мечты должна быть числом.',
            'dream_price.min' => 'Стоимость мечты должна быть больше 0.',
        ];
    }
}