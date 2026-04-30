<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;
use Throwable;

class AuditLog extends Model
{
    /** @var array<int, string> */
    protected $fillable = [
        'user_id',
        'action',
        'subject',
        'details',
        'ip_address',
        'user_agent',
    ];

    public static function record(Request $request, string $action, ?string $subject = null, array $details = []): void
    {
        try {
            self::create([
                'user_id' => $request->user()?->id,
                'action' => $action,
                'subject' => $subject,
                'details' => $details ?: null,
                'ip_address' => $request->ip(),
                'user_agent' => mb_substr((string) $request->userAgent(), 0, 500),
            ]);
        } catch (Throwable) {
            // The audit table may not exist until migrations are run.
        }
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'details' => 'array',
            'created_at' => 'datetime',
        ];
    }
}
