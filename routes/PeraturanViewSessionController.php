<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class PeraturanViewSessionController extends Controller
{
    /**
     * Mulai sesi lihat dokumen peraturan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function start(Request $request)
    {
        $peraturanId = (int) $request->input('peraturan_id', 0);
        $userId = (int) optional($request->user())->id;
        $sessionId = (string) Str::uuid();

        if ($userId <= 0 || $peraturanId <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Parameter peraturan_id atau user tidak valid.',
            ], 422);
        }

        $payload = [
            'session_id' => $sessionId,
            'user_id' => $userId,
            'peraturan_id' => $peraturanId,
            'started_at' => now()->toDateTimeString(),
            'last_activity_at' => now()->toDateTimeString(),
            'ip' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
        ];

        Cache::put($this->cacheKey($userId, $peraturanId), $payload, now()->addHours(12));

        return response()->json([
            'success' => true,
            'message' => 'View session started.',
            'data' => $payload,
        ]);
    }

    /**
     * Update aktivitas sesi lihat dokumen.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $peraturanId = (int) $request->input('peraturan_id', 0);
        $userId = (int) optional($request->user())->id;

        if ($userId <= 0 || $peraturanId <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Parameter peraturan_id atau user tidak valid.',
            ], 422);
        }

        $key = $this->cacheKey($userId, $peraturanId);
        $payload = Cache::get($key);

        if (!$payload) {
            $payload = [
                'session_id' => (string) Str::uuid(),
                'user_id' => $userId,
                'peraturan_id' => $peraturanId,
                'started_at' => now()->toDateTimeString(),
            ];
        }

        $payload['last_activity_at'] = now()->toDateTimeString();
        $payload['ip'] = $request->ip();
        $payload['user_agent'] = (string) $request->userAgent();

        Cache::put($key, $payload, now()->addHours(12));

        return response()->json([
            'success' => true,
            'message' => 'View session updated.',
            'data' => $payload,
        ]);
    }

    /**
     * Kompatibilitas untuk route lama "ping".
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ping(Request $request)
    {
        return $this->update($request);
    }

    /**
     * Akhiri sesi lihat dokumen.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function end(Request $request)
    {
        $peraturanId = (int) $request->input('peraturan_id', 0);
        $userId = (int) optional($request->user())->id;

        if ($userId <= 0 || $peraturanId <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Parameter peraturan_id atau user tidak valid.',
            ], 422);
        }

        Cache::forget($this->cacheKey($userId, $peraturanId));

        return response()->json([
            'success' => true,
            'message' => 'View session ended.',
        ]);
    }

    /**
     * @param  int  $userId
     * @param  int  $peraturanId
     * @return string
     */
    protected function cacheKey($userId, $peraturanId)
    {
        return 'peraturan:view-session:'.$userId.':'.$peraturanId;
    }
}
