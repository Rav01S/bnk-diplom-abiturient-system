<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Program extends Model
{
    /** @var bool */
    public $timestamps = false;

    /** @var array<int, string> */
    protected $fillable = [
        'specialty_id',
        'campaign_year',
        'has_study_form',
        'plan_count',
        'plan_count_paid',
        'is_open',
        'open_from',
        'open_until',
    ];

    public function specialty(): BelongsTo
    {
        return $this->belongsTo(Specialty::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    /**
     * Только открытые программы в текущий период.
     */
    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('is_open', true)
            ->where(function (Builder $q): void {
                $q->whereNull('open_from')
                    ->orWhere('open_from', '<=', now()->toDateString());
            })
            ->where(function (Builder $q): void {
                $q->whereNull('open_until')
                    ->orWhere('open_until', '>=', now()->toDateString());
            });
    }

    /**
     * Проверка доступности для подачи.
     */
    public function isAcceptingApplications(): bool
    {
        if (! $this->is_open) {
            return false;
        }

        $today = now()->toDateString();

        if ($this->open_from && $this->open_from > $today) {
            return false;
        }

        if ($this->open_until && $this->open_until < $today) {
            return false;
        }

        return true;
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'has_study_form' => 'boolean',
            'is_open' => 'boolean',
            'open_from' => 'date',
            'open_until' => 'date',
        ];
    }
}
