<?php

namespace App\Http\Controllers;

use App\PeraturanViewSession;
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
        $user = $request->user();
        $userId = (int) optional($user)->id;
        $sessionId = (string) Str::uuid();

        if ($userId <= 0 || $peraturanId <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Parameter peraturan_id atau user tidak valid.',
            ], 422);
        }

        $now = now();
        $viewSession = PeraturanViewSession::create([
            'user_id' => $userId,
            'pegawai_id' => (int) optional($user)->pegawai_id ?: null,
            'peraturan_id' => $peraturanId,
            'role' => $this->resolveUserRole($user),
            'page_url' => (string) $request->input('page_url', ''),
            'started_at' => $now,
            'last_seen_at' => $now,
            'ended_at' => null,
            'active_seconds' => 0,
            'idle_seconds' => 0,
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
        ]);

        $payload = [
            'session_id' => $sessionId,
            'db_session_id' => (int) $viewSession->id,
            'user_id' => $userId,
            'peraturan_id' => $peraturanId,
            'started_at' => $now->toDateTimeString(),
            'last_activity_at' => $now->toDateTimeString(),
            'ip' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
        ];

        $this->storePayloadCache($payload);

        return response()->json([
            'success' => true,
            'ok' => true,
            'message' => 'View session started.',
            'session_id' => $sessionId,
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
        $sessionId = (string) $request->input('session_id', '');
        $activeSeconds = max(0, (int) $request->input('active_seconds', 0));
        $idleSeconds = max(0, (int) $request->input('idle_seconds', 0));

        if ($userId <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Parameter peraturan_id atau user tidak valid.',
            ], 422);
        }

        $payload = null;
        if ($peraturanId > 0) {
            $payload = Cache::get($this->cacheKey($userId, $peraturanId));
        }
        if (!$payload && $sessionId !== '') {
            $payload = Cache::get($this->sessionCacheKey($sessionId));
            if ($payload && $peraturanId <= 0) {
                $peraturanId = (int) ($payload['peraturan_id'] ?? 0);
            }
        }

        if (!$payload) {
            if ($peraturanId <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session tidak ditemukan.',
                ], 422);
            }

            $existingOpenSession = PeraturanViewSession::query()
                ->where('user_id', $userId)
                ->where('peraturan_id', $peraturanId)
                ->whereNull('ended_at')
                ->orderByDesc('id')
                ->first();

            if ($existingOpenSession) {
                $payload = [
                    'session_id' => (string) Str::uuid(),
                    'db_session_id' => (int) $existingOpenSession->id,
                    'user_id' => $userId,
                    'peraturan_id' => $peraturanId,
                    'started_at' => optional($existingOpenSession->started_at)->toDateTimeString(),
                    'last_activity_at' => now()->toDateTimeString(),
                    'ip' => $request->ip(),
                    'user_agent' => (string) $request->userAgent(),
                ];
            } else {
                return $this->start($request);
            }
        }

        $sessionId = (string) ($payload['session_id'] ?? $sessionId ?: (string) Str::uuid());
        $payload['last_activity_at'] = now()->toDateTimeString();
        $payload['session_id'] = $sessionId;
        $payload['ip'] = $request->ip();
        $payload['user_agent'] = (string) $request->userAgent();

        $sessionRow = null;
        if (!empty($payload['db_session_id'])) {
            $sessionRow = PeraturanViewSession::find((int) $payload['db_session_id']);
        }
        if (!$sessionRow && $peraturanId > 0) {
            $sessionRow = PeraturanViewSession::query()
                ->where('user_id', $userId)
                ->where('peraturan_id', $peraturanId)
                ->whereNull('ended_at')
                ->orderByDesc('id')
                ->first();
        }

        if ($sessionRow) {
            $sessionRow->last_seen_at = now();
            $sessionRow->active_seconds = $activeSeconds;
            $sessionRow->idle_seconds = $idleSeconds;
            $sessionRow->ip_address = $request->ip();
            $sessionRow->user_agent = (string) $request->userAgent();
            $sessionRow->save();

            $payload['db_session_id'] = (int) $sessionRow->id;
            $payload['peraturan_id'] = (int) $sessionRow->peraturan_id;
        }

        $this->storePayloadCache($payload);

        return response()->json([
            'success' => true,
            'ok' => true,
            'message' => 'View session updated.',
            'session_id' => $sessionId,
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
        $sessionId = (string) $request->input('session_id', '');
        $activeSeconds = max(0, (int) $request->input('active_seconds', 0));
        $idleSeconds = max(0, (int) $request->input('idle_seconds', 0));

        if ($userId <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Parameter peraturan_id atau user tidak valid.',
            ], 422);
        }

        $payload = null;
        if ($peraturanId > 0) {
            $payload = Cache::get($this->cacheKey($userId, $peraturanId));
        }
        if (!$payload && $sessionId !== '') {
            $payload = Cache::get($this->sessionCacheKey($sessionId));
            if ($payload && $peraturanId <= 0) {
                $peraturanId = (int) ($payload['peraturan_id'] ?? 0);
            }
        }

        $sessionRow = null;
        if ($payload && !empty($payload['db_session_id'])) {
            $sessionRow = PeraturanViewSession::find((int) $payload['db_session_id']);
        }
        if (!$sessionRow && $peraturanId > 0) {
            $sessionRow = PeraturanViewSession::query()
                ->where('user_id', $userId)
                ->where('peraturan_id', $peraturanId)
                ->whereNull('ended_at')
                ->orderByDesc('id')
                ->first();
        }

        if ($sessionRow) {
            $sessionRow->ended_at = now();
            $sessionRow->last_seen_at = now();
            $sessionRow->active_seconds = $activeSeconds;
            $sessionRow->idle_seconds = $idleSeconds;
            $sessionRow->ip_address = $request->ip();
            $sessionRow->user_agent = (string) $request->userAgent();
            $sessionRow->save();
        }

        if ($peraturanId > 0) {
            Cache::forget($this->cacheKey($userId, $peraturanId));
        }
        if ($sessionId !== '') {
            Cache::forget($this->sessionCacheKey($sessionId));
        }

        return response()->json([
            'success' => true,
            'ok' => true,
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

    /**
     * @param  string  $sessionId
     * @return string
     */
    protected function sessionCacheKey($sessionId)
    {
        return 'peraturan:view-session:id:'.$sessionId;
    }

    /**
     * @param  array  $payload
     * @return void
     */
    protected function storePayloadCache(array $payload)
    {
        $ttl = now()->addHours(12);
        if (!empty($payload['user_id']) && !empty($payload['peraturan_id'])) {
            Cache::put($this->cacheKey((int) $payload['user_id'], (int) $payload['peraturan_id']), $payload, $ttl);
        }
        if (!empty($payload['session_id'])) {
            Cache::put($this->sessionCacheKey((string) $payload['session_id']), $payload, $ttl);
        }
    }

    /**
     * @param  mixed  $user
     * @return string|null
     */
    protected function resolveUserRole($user)
    {
        if (!$user) {
            return null;
        }

        $role = null;
        if (!empty($user->roles)) {
            $role = is_string($user->roles) ? $user->roles : json_encode($user->roles);
        } elseif (!empty($user->status)) {
            $role = (string) $user->status;
        }

        if ($role === null) {
            return null;
        }

        return substr($role, 0, 30);
    }
}
