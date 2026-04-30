<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Applicant extends Model
{
    /** @var array<int, string> */
    protected $fillable = [
        'user_id',
        'last_name',
        'first_name',
        'middle_name',
        'birth_date',
        'passport_series',
        'passport_number',
        'passport_issued_by',
        'snils',
        'phone',
        'prev_education',
        'edu_doc_series',
        'edu_doc_number',
        'edu_doc_issued_by',
        'edu_issue_date',
        'avg_cert_score',
        'photo_passport',
        'photo_snils',
        'photo_edu_1',
        'photo_edu_2',
        'photo_edu_3',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    /**
     * ФИО одной строкой.
     */
    public function getFullNameAttribute(): string
    {
        return trim(implode(' ', array_filter([
            $this->last_name,
            $this->first_name,
            $this->middle_name,
        ])));
    }

    /**
     * Проверка заполненности обязательных полей профиля.
     */
    public function isProfileComplete(): bool
    {
        return $this->last_name
            && $this->first_name
            && $this->birth_date
            && $this->passport_series
            && $this->passport_number
            && $this->snils;
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'edu_issue_date' => 'date',
            'avg_cert_score' => 'decimal:2',
        ];
    }
}
