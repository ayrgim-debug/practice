<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'cost',
        'days',
        'total',
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFrequencyLabel(): string
    {
        return match ($this->days) {
            365 => 'Ежедневно',
            260 => 'По будням',
            104 => 'По выходным',
            52 => 'Раз в неделю',
            12 => 'Раз в месяц',
            default => $this->days . ' раз в год',
        };
    }
}