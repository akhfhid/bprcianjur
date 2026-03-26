<?php

namespace App\Http\Controllers;

use App\PeraturanViewSession;
use Illuminate\Http\Request;

class PeraturanViewSessionController extends Controller
{
    public function start(Request $request)
    {
        $request->validate([
            'peraturan_id' => 'required|integer|exists:peraturans,id',
            'page_url' => 'nullable|string|max:255',
        ]);

        $session = PeraturanViewSession::create([
            'user_id' => optional(auth()->user())->id,
            'pegawai_id' => optional(auth()->user())->pegawai_id,
            'peraturan_id' => (int) $request->peraturan_id,
            'role' => optional(auth()->user())->roles,
            'page_url' => $request->page_url,
            'started_at' => now(),
            'last_seen_at' => now(),
            'active_seconds' => 0,
            'idle_seconds' => 0,
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 65000),
        ]);

        return response()->json([
            'ok' => true,
            'session_id' => $session->id,
        ]);
    }

    public function ping(Request $request)
    {
        $request->validate([
            'session_id' => 'required|integer',
            'active_seconds' => 'required|integer|min:0',
            'idle_seconds' => 'nullable|integer|min:0',
        ]);

        $session = PeraturanViewSession::where('id', (int) $request->session_id)
            ->where('user_id', optional(auth()->user())->id)
            ->whereNull('ended_at')
            ->first();

        if (!$session) {
            return response()->json(['ok' => false, 'message' => 'Session not found'], 404);
        }

        $session->active_seconds = max((int) $session->active_seconds, (int) $request->active_seconds);
        if ($request->filled('idle_seconds')) {
            $session->idle_seconds = max((int) $session->idle_seconds, (int) $request->idle_seconds);
        }
        $session->last_seen_at = now();
        $session->save();

        return response()->json(['ok' => true]);
    }

    public function end(Request $request)
    {
        $request->validate([
            'session_id' => 'required|integer',
            'active_seconds' => 'required|integer|min:0',
            'idle_seconds' => 'nullable|integer|min:0',
        ]);

        $session = PeraturanViewSession::where('id', (int) $request->session_id)
            ->where('user_id', optional(auth()->user())->id)
            ->first();

        if (!$session) {
            return response()->json(['ok' => false, 'message' => 'Session not found'], 404);
        }

        $session->active_seconds = max((int) $session->active_seconds, (int) $request->active_seconds);
        if ($request->filled('idle_seconds')) {
            $session->idle_seconds = max((int) $session->idle_seconds, (int) $request->idle_seconds);
        }
        $session->last_seen_at = now();
        if ($session->ended_at === null) {
            $session->ended_at = now();
        }
        $session->save();

        return response()->json(['ok' => true]);
    }
}

