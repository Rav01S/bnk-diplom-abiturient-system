<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationScore extends Model
{
    /** @var bool */
    public $timestamps = false;

    /** @var array<int, string> */
    protected $fillable = [
        'application_id',
        'subject_name',
        'score',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'score' => 'decimal:1',
        ];
    }
}
