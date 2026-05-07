<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Application extends Model
{
    /** @var array<int, string> */
    protected $fillable = [
        'applicant_id',
        'program_id',
        'priority',
        'status',
        'revision',
        'doc_type',
        'study_form',
        'funding_type',
        'is_benefit',
        'benefit_type',
        'needs_dorm',
        'is_first_spo',
        'signed_doc_photo',
        'rejection_reason',
        'cancelled_at',
        // Снапшот данных абитуриента
        'app_last_name',
        'app_first_name',
        'app_middle_name',
        'app_birth_date',
        'app_passport_series',
        'app_passport_number',
        'app_passport_issued_by',
        'app_snils',
        'app_prev_education',
        'app_edu_doc_series',
        'app_edu_doc_number',
        'app_edu_doc_issued_by',
        'app_edu_issue_date',
        'app_phone',
        'app_photo_passport',
        'app_photo_snils',
        'app_photo_edu_1',
        'app_photo_edu_2',
        'app_photo_edu_3',
    ];

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function scores(): HasMany
    {
        return $this->hasMany(ApplicationScore::class);
    }

    /**
     * Активные заявления (не отменённые и не черновики).
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNotIn('status', ['cancelled', 'draft']);
    }

    /**
     * Фильтр по статусу.
     */
    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Только поданные (ожидают проверки).
     */
    public function scopeSubmitted(Builder $query): Builder
    {
        return $query->where('status', 'submitted');
    }

    /**
     * ФИО из снапшота.
     */
    public function getAppFullNameAttribute(): string
    {
        return trim(implode(' ', array_filter([
            $this->app_last_name,
            $this->app_first_name,
            $this->app_middle_name,
        ])));
    }

    /**
     * Сумма баллов.
     */
    public function getTotalScoreAttribute(): float
    {
        return $this->scores->sum('score');
    }

    /**
     * Средний балл по первым трём предметам для ранжирования.
     */
    public function getAverageScoreAttribute(): float
    {
        $scores = $this->scores->take(3);

        if ($scores->isEmpty()) {
            return 0;
        }

        return round($scores->avg('score'), 2);
    }

    /**
     * Можно ли редактировать заявление.
     */
    public function isEditable(): bool
    {
        return in_array($this->status, ['draft', 'submitted', 'rejected', 'rework_needed']);
    }

    /**
     * Можно ли отменить заявление.
     */
    public function isCancellable(): bool
    {
        return in_array($this->status, ['draft', 'submitted', 'accepted']);
    }

    /**
     * Можно ли удалить заявление.
     */
    public function isDeletable(): bool
    {
        return true;
    }

    /**
     * Создание снапшота из данных профиля.
     */
    public function fillSnapshot(Applicant $applicant): void
    {
        $this->app_last_name = $applicant->last_name;
        $this->app_first_name = $applicant->first_name;
        $this->app_middle_name = $applicant->middle_name;
        $this->app_birth_date = $applicant->birth_date;
        $this->app_passport_series = $applicant->passport_series;
        $this->app_passport_number = $applicant->passport_number;
        $this->app_passport_issued_by = $applicant->passport_issued_by;
        $this->app_snils = $applicant->snils;
        $this->app_prev_education = $applicant->prev_education;
        $this->app_edu_doc_series = $applicant->edu_doc_series;
        $this->app_edu_doc_number = $applicant->edu_doc_number;
        $this->app_edu_doc_issued_by = $applicant->edu_doc_issued_by;
        $this->app_edu_issue_date = $applicant->edu_issue_date;
        $this->app_phone = $applicant->phone;
        $this->app_photo_passport = $applicant->photo_passport;
        $this->app_photo_snils = $applicant->photo_snils;
        $this->app_photo_edu_1 = $applicant->photo_edu_1;
        $this->app_photo_edu_2 = $applicant->photo_edu_2;
        $this->app_photo_edu_3 = $applicant->photo_edu_3;
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'is_benefit' => 'boolean',
            'needs_dorm' => 'boolean',
            'is_first_spo' => 'boolean',
            'app_birth_date' => 'date',
            'app_edu_issue_date' => 'date',
            'cancelled_at' => 'datetime',
        ];
    }
}
