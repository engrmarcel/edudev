<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Services\Audit\AuditLogger;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;

/**
 * Time-based one-time passwords (RFC 6238, the standard used by Google
 * Authenticator, Microsoft Authenticator, Authy, etc.) plus single-use
 * recovery codes.
 */
class TwoFactorService
{
    public function __construct(
        private readonly Google2FA $engine,
        private readonly AuditLogger $audit,
    ) {}

    public static function featureEnabled(): bool
    {
        return (bool) config('edudev.two_factor.enabled');
    }

    /** Step 1 of setup: create an unconfirmed secret. */
    public function begin(User $user): void
    {
        $user->forceFill([
            'two_factor_secret' => $this->engine->generateSecretKey(32),
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
            'two_factor_last_step' => null,
        ])->save();
    }

    /**
     * Step 2 of setup: the user proves their app works. Returns the
     * recovery codes (shown once), or null if the code is wrong.
     *
     * @return list<string>|null
     */
    public function confirm(User $user, string $code): ?array
    {
        if ($user->two_factor_secret === null || $user->hasTwoFactorEnabled()) {
            return null;
        }
        if (! $this->verifyTotp($user, $code)) {
            return null;
        }

        $codes = $this->freshRecoveryCodes();
        $user->forceFill([
            'two_factor_confirmed_at' => now(),
            'two_factor_recovery_codes' => $codes,
        ])->save();

        $this->audit->record('two_factor.enabled', $user, actor: $user);

        return $codes;
    }

    public function disable(User $user, ?User $actor = null): void
    {
        $user->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
            'two_factor_last_step' => null,
        ])->save();

        $this->audit->record(
            $actor && ! $actor->is($user) ? 'two_factor.reset_by_admin' : 'two_factor.disabled',
            $user,
            actor: $actor ?? $user,
        );
    }

    /** @return list<string> */
    public function regenerateRecoveryCodes(User $user): array
    {
        $codes = $this->freshRecoveryCodes();
        $user->forceFill(['two_factor_recovery_codes' => $codes])->save();
        $this->audit->record('two_factor.recovery_codes_regenerated', $user, actor: $user);

        return $codes;
    }

    /** Sign-in check: an authenticator code OR an unused recovery code. */
    public function verifyLogin(User $user, string $code): bool
    {
        if (! $user->hasTwoFactorEnabled()) {
            return false;
        }

        $code = trim($code);

        if (preg_match('/^\d{6}$/', str_replace(' ', '', $code))) {
            return $this->verifyTotp($user, str_replace(' ', '', $code));
        }

        return $this->useRecoveryCode($user, $code);
    }

    public function qrCodeSvg(User $user): string
    {
        $url = $this->engine->getQRCodeUrl(
            (string) brand('product_name'),
            $user->email,
            (string) $user->two_factor_secret,
        );

        $writer = new Writer(new ImageRenderer(new RendererStyle(192, 1), new SvgImageBackEnd));

        // Generated locally: no third-party QR service ever sees the secret.
        return trim((string) preg_replace('/^<\?xml[^>]*\?>\s*/', '', $writer->writeString($url)));
    }

    /** Secret formatted in groups of four for manual entry. */
    public function displaySecret(User $user): string
    {
        return trim(chunk_split((string) $user->two_factor_secret, 4, ' '));
    }

    private function verifyTotp(User $user, string $code): bool
    {
        if (! preg_match('/^\d{6}$/', $code) || $user->two_factor_secret === null) {
            return false;
        }

        // verifyKeyNewer rejects any step at or before the last accepted one
        // (replay protection) and returns the new step on success.
        // Passing 0 (not null) for "never used" makes the library return the
        // matched step, which is stored so that code can never be used again.
        $step = $this->engine->verifyKeyNewer(
            $user->two_factor_secret,
            $code,
            (int) ($user->two_factor_last_step ?? 0),
            (int) config('edudev.two_factor.window', 1),
        );

        if (! is_int($step)) {
            return false;
        }

        $user->forceFill(['two_factor_last_step' => $step])->save();

        return true;
    }

    private function useRecoveryCode(User $user, string $code): bool
    {
        $codes = collect($user->two_factor_recovery_codes ?? []);
        $normalised = strtolower(str_replace(' ', '', $code));

        $match = $codes->first(fn (string $c) => hash_equals(strtolower($c), $normalised));
        if ($match === null) {
            return false;
        }

        $user->forceFill(['two_factor_recovery_codes' => $codes->reject(fn ($c) => $c === $match)->values()->all()])->save();
        $this->audit->record('two_factor.recovery_code_used', $user, context: ['remaining' => $codes->count() - 1], actor: $user);

        return true;
    }

    /** @return list<string> */
    private function freshRecoveryCodes(): array
    {
        return Collection::times(
            (int) config('edudev.two_factor.recovery_codes', 8),
            fn () => Str::lower(Str::random(5).'-'.Str::random(5)),
        )->all();
    }
}
