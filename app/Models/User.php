<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /** @var array<int, string> */
    protected $fillable = [
        'email',
        'password_hash',
        'role',
        'full_name',
        'is_active',
    ];

    /** @var array<int, string> */
    protected $hidden = [
        'password_hash',
    ];

    /** @var bool */
    public $timestamps = false;

    /**
     * Колонка пароля для Auth.
     */
    public function getAuthPassword(): string
    {
        return $this->password_hash;
    }

    /**
     * Профиль абитуриента.
     */
    public function applicant(): HasOne
    {
        return $this->hasOne(Applicant::class);
    }

    /**
     * Проверка роли пользователя.
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function isApplicant(): bool
    {
        return $this->hasRole('applicant');
    }

    public function isCommission(): bool
    {
        return $this->hasRole('commission');
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Полное имя из профиля (для абитуриента) или email.
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->full_name) {
            return $this->full_name;
        }

        if ($this->isApplicant() && $this->applicant) {
            $parts = array_filter([
                $this->applicant->last_name,
                $this->applicant->first_name,
                $this->applicant->middle_name,
            ]);

            return implode(' ', $parts) ?: $this->email;
        }

        return $this->email;
    }

    /**
     * Инициалы для аватара.
     */
    public function getInitialsAttribute(): string
    {
        if ($this->display_name && $this->display_name !== $this->email) {
            $initials = collect(explode(' ', $this->display_name))
                ->filter()
                ->take(2)
                ->map(fn (string $part): string => mb_substr($part, 0, 1))
                ->join('');

            if ($initials !== '') {
                return mb_strtoupper($initials);
            }
        }

        if ($this->isApplicant() && $this->applicant && $this->applicant->last_name) {
            $initials = mb_substr($this->applicant->last_name, 0, 1);
            if ($this->applicant->first_name) {
                $initials .= mb_substr($this->applicant->first_name, 0, 1);
            }

            return mb_strtoupper($initials);
        }

        return mb_strtoupper(mb_substr($this->email, 0, 2));
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }
}
