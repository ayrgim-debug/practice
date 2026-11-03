<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HabitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'cost' => ['required', 'numeric', 'min:0.01'],
            'frequency' => ['required', 'integer', 'in:365,260,104,52,12'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Введите название привычки.',
            'name.max' => 'Название не должно превышать 100 символов.',
            'cost.required' => 'Введите стоимость.',
            'cost.numeric' => 'Стоимость должна быть числом.',
            'cost.min' => 'Стоимость должна быть больше 0.',
            'frequency.required' => 'Выберите частоту.',
            'frequency.in' => 'Выберите корректную частоту.',
        ];
    }
}