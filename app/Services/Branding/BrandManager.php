<?php

namespace App\Services\Branding;

use App\Models\SystemSetting;
use App\Models\User;
use App\Services\Audit\AuditLogger;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use InvalidArgumentException;
use Throwable;

/**
 * Single source of truth for branding.
 *
 * Effective brand = config('edudev.brand') defaults, overlaid with any
 * admin overrides stored in system_settings (group "brand"). The merged
 * result is cached and the cache is cleared whenever an override changes.
 */
class BrandManager
{
    public const GROUP = 'brand';

    public const CACHE_KEY = 'edudev:brand:v1';

    private ?array $resolved = null;

    public function __construct(
        private readonly Cache $cache,
        private readonly AuditLogger $audit,
    ) {}

    public function get(string $key, mixed $default = null): mixed
    {
        return Arr::get($this->all(), $key, $default);
    }

    public function all(): array
    {
        return $this->resolved ??= $this->cache->rememberForever(
            self::CACHE_KEY,
            fn () => $this->merge(config('edudev.brand', []), $this->overrides()),
        );
    }

    /** Admin overrides only, keyed by dot-notation key. */
    public function overrides(): array
    {
        try {
            if (! Schema::hasTable('system_settings')) {
                return [];
            }

            return SystemSetting::query()
                ->where('group', self::GROUP)
                ->pluck('value', 'key')
                ->all();
        } catch (Throwable) {
            // Database unavailable (e.g. during install). Fall back to the
            // defaults rather than taking the whole site down.
            return [];
        }
    }

    /**
     * Save admin overrides. An empty value, or a value equal to the config
     * default, removes the override so the default applies again.
     *
     * @param  array<string, mixed>  $values  dot-notation key => value
     */
    public function update(array $values, ?User $actor = null): void
    {
        $editable = config('edudev.editable_brand_keys', []);

        foreach (array_keys($values) as $key) {
            if (! in_array($key, $editable, true)) {
                throw new InvalidArgumentException("Brand key [{$key}] is not editable.");
            }
        }

        $before = $this->all();

        DB::transaction(function () use ($values, $actor) {
            foreach ($values as $key => $value) {
                $isEmpty = $value === null || $value === '';
                $isDefault = Arr::get(config('edudev.brand'), $key) === $value;

                if ($isEmpty || $isDefault) {
                    SystemSetting::query()->where('group', self::GROUP)->where('key', $key)->delete();

                    continue;
                }

                SystemSetting::query()->updateOrCreate(
                    ['group' => self::GROUP, 'key' => $key],
                    ['value' => $value, 'updated_by' => $actor?->id],
                );
            }
        });

        $this->flush();

        $after = $this->all();
        $diff = [];
        foreach (array_keys($values) as $key) {
            if (Arr::get($before, $key) !== Arr::get($after, $key)) {
                $diff[$key] = ['from' => Arr::get($before, $key), 'to' => Arr::get($after, $key)];
            }
        }

        if ($diff !== []) {
            $this->audit->record('branding.updated', changes: $diff, actor: $actor);
        }
    }

    public function flush(): void
    {
        $this->resolved = null;
        $this->cache->forget(self::CACHE_KEY);
    }

    /** CSS custom properties for the brand colours, injected in <head>. */
    public function cssVariables(): string
    {
        $map = [
            '--brand-primary' => $this->get('colors.primary'),
            '--brand-secondary' => $this->get('colors.secondary'),
            '--brand-accent' => $this->get('colors.accent'),
        ];

        $rules = [];
        foreach ($map as $var => $hex) {
            // Strict hex check: this string is printed inside a <style> tag.
            if (is_string($hex) && preg_match('/^#[0-9A-Fa-f]{6}$/', $hex)) {
                $rules[] = $var.': '.$hex.';';
            }
        }

        return $rules === [] ? '' : ':root{'.implode('', $rules).'}';
    }

    public function termsUrl(): string
    {
        return $this->get('terms_url') ?: route('legal.show', 'terms');
    }

    public function privacyUrl(): string
    {
        return $this->get('privacy_url') ?: route('legal.show', 'privacy');
    }

    private function merge(array $defaults, array $overrides): array
    {
        foreach ($overrides as $key => $value) {
            Arr::set($defaults, $key, $value);
        }

        return $defaults;
    }
}
