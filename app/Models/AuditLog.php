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

    /**
     * Человекочитаемые названия действий на русском языке.
     *
     * @var array<string, string>
     */
    public const ACTION_LABELS = [
        'application.reviewed' => 'Рассмотрение заявления',
        'benefit.created' => 'Создание льготы',
        'benefit.updated' => 'Изменение льготы',
        'benefit.toggled' => 'Включение/отключение льготы',
        'benefit.deleted' => 'Удаление льготы',
        'campaign.updated' => 'Изменение приёмной кампании',
        'campaign.opened_all' => 'Открытие всех программ приёма',
        'campaign.closed_all' => 'Закрытие всех программ приёма',
        'staff.created' => 'Создание пользователя',
        'user.updated' => 'Изменение пользователя',
        'user.activated' => 'Активация пользователя',
        'user.deactivated' => 'Деактивация пользователя',
        'user.deleted' => 'Удаление пользователя',
        'user.password_reset' => 'Сброс пароля пользователя',
        'user.password_self_reset' => 'Смена собственного пароля',
    ];

    public static function actionLabel(string $action): string
    {
        return self::ACTION_LABELS[$action] ?? $action;
    }

    public function getActionLabelAttribute(): string
    {
        return self::actionLabel((string) $this->action);
    }

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
