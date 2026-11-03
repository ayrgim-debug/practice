<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Habit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'cost',
        'frequency',
    ];

    protected $casts = [
        'cost' => 'decimal:2',
    ];

    // ===== СВЯЗИ =====
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ===== МЕТОДЫ =====
    public function getYearlyTotal(): float
    {
        return $this->cost * $this->frequency;
    }

    public function getFrequencyLabel(): string
    {
        return match ($this->frequency) {
            365 => 'Ежедневно',
            260 => 'По будням',
            104 => 'По выходным',
            52 => 'Раз в неделю',
            12 => 'Раз в месяц',
            default => $this->frequency . ' раз в год',
        };
    }

    public static function getFrequencies(): array
    {
        return [
            365 => 'Ежедневно',
            260 => 'По будням (5/7)',
            104 => 'По выходным',
            52 => 'Раз в неделю',
            12 => 'Раз в месяц',
        ];
    }
}