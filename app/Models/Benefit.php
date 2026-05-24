<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class Benefit extends Model
{
    private const CacheKey = 'benefits.all';

    public const BoostReplace = 'replace';

    public const BoostAdd = 'add';

    /** @var array<int, string> */
    protected $fillable = [
        'key',
        'label',
        'gives_priority',
        'boost_mode',
        'boost_value',
        'sort_order',
        'is_active',
    ];

    protected static function booted(): void
    {
        $flush = static function (): void {
            Cache::forget(self::CacheKey);
        };

        static::saved($flush);
        static::deleted($flush);
    }

    /** @return Collection<int, self> */
    public static function allCached(): Collection
    {
        $cached = Cache::get(self::CacheKey);

        if ($cached instanceof Collection) {
            return $cached;
        }

        $fresh = self::query()->orderBy('sort_order')->orderBy('label')->get();
        Cache::forever(self::CacheKey, $fresh);

        return $fresh;
    }

    /** @return Collection<int, self> */
    public static function activeCached(): Collection
    {
        return self::allCached()->where('is_active', true)->values();
    }

    public static function findByKey(?string $key): ?self
    {
        if ($key === null || $key === '') {
            return null;
        }

        return self::allCached()->firstWhere('key', $key);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'gives_priority' => 'boolean',
            'is_active' => 'boolean',
            'boost_value' => 'decimal:2',
        ];
    }
}
