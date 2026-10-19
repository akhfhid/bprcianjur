<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\TelegramLinkController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelegramLinkApiController extends Controller
{
    /**
     * Verifikasi token pairing dari bot Telegram dan pasang chat ID ke pegawai.
     *
     * Endpoint ini dipanggil oleh server bot Telegram (Telegram Pulse) saat user
     * mengirim /start <TOKEN> ke bot. Kontrak request/response disamakan dengan
     * SEMATA agar satu bot bisa melayani beberapa aplikasi.
     *
     * POST /api/v1/telegram/link-verify
     * Header: X-API-KEY
     * Body: { token, chatId|chat_id, username?, firstName?, lastName? }
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verify(Request $request): JsonResponse
    {
        $configuredKey = (string) config('telegram_pulse.verify_api_key', '');

        if ($configuredKey !== '') {
            $incomingKey = $request->header('X-API-KEY') ?: $request->bearerToken();

            if (!hash_equals($configuredKey, (string) $incomingKey)) {
                Log::warning('TelegramLinkApiController: API key mismatch', [
                    'ip' => $request->ip(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized: Invalid API Key.',
                ], 401);
            }
        }

        $validated = $request->validate([
            'token'     => 'required|string',
            'chatId'    => 'nullable|string',
            'chat_id'   => 'nullable|string',
            'username'  => 'nullable|string',
            'firstName' => 'nullable|string',
            'lastName'  => 'nullable|string',
        ]);

        $chatId = $validated['chatId'] ?? $validated['chat_id'] ?? null;

        if (empty($chatId)) {
            return response()->json([
                'success' => false,
                'message' => 'Field chatId atau chat_id wajib diisi.',
            ], 422);
        }

        $result = TelegramLinkController::verifyToken(trim($validated['token']), (string) $chatId);

        if (empty($result['success'])) {
            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Token tautan tidak valid atau sudah kedaluwarsa.',
            ], 404);
        }

        $user = $result['user'] ?? [];

        return response()->json([
            'success'  => true,
            'app_name' => 'SIKAP',
            'message'  => 'Akun berhasil dihubungkan!',
            'user'     => [
                'name'    => $user['name'] ?? null,
                'jabatan' => $user['jabatan'] ?? $user['role'] ?? null,
                'role'    => $user['role'] ?? null,
                'username'=> $user['username'] ?? null,
                'chat_id' => (string) $chatId,
            ],
        ]);
    }
}
