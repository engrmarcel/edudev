<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

/**
 * Machine-readable health check for uptime monitors and load balancers.
 *
 * Public output is deliberately minimal (ok/fail per check, no versions,
 * hostnames or error messages) so it cannot be used for reconnaissance.
 */
class HealthController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $checks = [
            'database' => $this->check(fn () => DB::select('select 1')),
            'cache' => $this->check(function () {
                $key = 'health:'.Str::random(8);
                Cache::put($key, 1, 10);
                $ok = Cache::get($key) === 1;
                Cache::forget($key);

                return $ok;
            }),
            'storage' => $this->check(function () {
                $path = 'health/'.Str::random(8).'.txt';
                Storage::disk('local')->put($path, 'ok');
                $ok = Storage::disk('local')->get($path) === 'ok';
                Storage::disk('local')->delete($path);

                return $ok;
            }),
        ];

        $healthy = ! in_array('fail', $checks, true);

        return response()->json([
            'status' => $healthy ? 'ok' : 'degraded',
            'checks' => $checks,
            'time' => now()->toIso8601String(),
        ], $healthy ? 200 : 503)->header('Cache-Control', 'no-store');
    }

    private function check(callable $probe): string
    {
        try {
            return $probe() === false ? 'fail' : 'ok';
        } catch (Throwable $e) {
            report($e);

            return 'fail';
        }
    }
}
