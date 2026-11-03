<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    use HasFactory;

    public const TYPES = [
        'bug' => '🐞 Ошибка',
        'question' => '❓ Вопрос',
        'idea' => '💡 Предложение',
    ];

    public const STATUSES = [
        'new' => 'Новое',
        'in_progress' => 'В работе',
        'resolved' => 'Решено',
        'closed' => 'Закрыто',
    ];

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'type',
        'subject',
        'message',
        'page_url',
        'user_agent',
        'status',
        'admin_reply',
        'replied_at',
    ];

    protected $casts = [
        'replied_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    // Ссылка на страницу с ошибкой — только если это обычный http(s)-адрес
    public function getSafePageUrlAttribute(): ?string
    {
        if ($this->page_url && preg_match('#^https?://#i', $this->page_url)) {
            return $this->page_url;
        }

        return null;
    }
}