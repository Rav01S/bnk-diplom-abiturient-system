<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Specialty extends Model
{
    /** @var bool */
    public $timestamps = false;

    /** @var array<int, string> */
    protected $fillable = [
        'code',
        'name',
        'is_profession',
        'subject_1',
        'subject_2',
        'subject_3',
    ];

    public function programs(): HasMany
    {
        return $this->hasMany(Program::class);
    }

    /**
     * Массив названий предметов.
     *
     * @return array<int, string>
     */
    public function getSubjectsAttribute(): array
    {
        return [$this->subject_1, $this->subject_2, $this->subject_3];
    }

    /**
     * Код + название в одну строку.
     */
    public function getFullTitleAttribute(): string
    {
        return "{$this->code} — {$this->name}";
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'is_profession' => 'boolean',
        ];
    }
}
