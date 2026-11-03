<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'dream_name',
        'dream_price',
        'role',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'dream_price' => 'decimal:2',
    ];

    // ===== СВЯЗИ =====
    public function habits()
    {
        return $this->hasMany(Habit::class);
    }

    public function histories()
    {
        return $this->hasMany(History::class);
    }

    public function userTips()
    {
        return $this->hasMany(UserTip::class);
    }

    public function savedTips()
    {
        return $this->belongsToMany(Tip::class, 'user_tips');
    }

    public function achievements()
    {
        return $this->belongsToMany(Achievement::class, 'user_achievements')->withTimestamps();
    }

    // ===== МЕТОДЫ =====
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function hasDream(): bool
    {
        return !empty($this->dream_name) && $this->dream_price > 0;
    }

    public function getTotalSavings(): float
    {
        return $this->histories()->sum('total');
    }

    public function getHabitsCount(): int
    {
        return $this->habits()->count();
    }

    public function getHistoryCount(): int
    {
        return $this->histories()->count();
    }
}