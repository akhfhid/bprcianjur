<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;

class TelegramHelper
{
 /**
 * Batas aman pengiriman pesan (Telegram: 30 pesan/detik global,
 * 20 pesan/menit per grup/channel yang sama).
 *
 * @var int
 */
 const RATE_LIMIT_PER_SECOND = 25;

 /**
 * Jeda minimum antar pesan ke chat yang sama (detik).
 *
 * @var float
 */
 const MIN_DELAY_PER_CHAT = 1.1;
 /**
 * Base URL API Telegram Pulse.
 *
 * @var string
 */
 protected static function baseUrl()
 {
 return rtrim(env('TELEGRAM_PULSE_URL', 'http://192.168.21.24:5173'), '/');
 }

 /**
 * API key Telegram Pulse.
 *
 * @return string
 */
 protected static function apiKey()
 {
 return env('TELEGRAM_PULSE_API_KEY', '');
 }

 /**
 * Cek apakah notifikasi telegram aktif.
 *
 * @return bool
 */
 public static function isEnabled()
 {
 return filter_var(env('TELEGRAM_PULSE_ENABLED', true), FILTER_VALIDATE_BOOLEAN)
 && self::apiKey() !== '';
 }

 /**
 * Throttle global agar tidak melewati batas rate API Telegram.
 * Maksimum RATE_LIMIT_PER_SECOND pesan per detik.
 *
 * @return void
 */
 protected static function throttleGlobal()
 {
 $key = 'telegram_pulse_rate_' . floor(microtime(true));
 $count = (int) Cache::get($key, 0);

 if ($count >= self::RATE_LIMIT_PER_SECOND) {
 // Tunggu sampai detik berikutnya
 usleep((int) ((1 - (microtime(true) - floor(microtime(true)))) * 1000000) + 50000);
 }

 Cache::put($key, $count + 1, 2);
 }

 /**
 * Throttle per chat agar aman dari limit 20 pesan/menit per grup.
 *
 * @param string $chatId
 * @return void
 */
 protected static function throttlePerChat($chatId)
 {
 $key = 'telegram_pulse_chat_' . $chatId;
 $lastSent = (float) Cache::get($key, 0);
 $elapsed = microtime(true) - $lastSent;

 if ($elapsed < self::MIN_DELAY_PER_CHAT) {
 usleep((int) ((self::MIN_DELAY_PER_CHAT - $elapsed) * 1000000));
 }

 Cache::put($key, microtime(true), 120);
 }

 /**
 * Kirim satu pesan ke Chat ID tertentu.
 *
 * @param string $chatId
 * @param string $content
 * @param string $parseMode HTML|Markdown|MarkdownV2
 * @return array ['success' => bool, 'message' => string]
 */
 public static function sendMessage($chatId, $content, $parseMode = 'HTML')
 {
 if (!self::isEnabled()) {
 return ['success' => false, 'message' => 'Notifikasi telegram dinonaktifkan atau API key kosong.'];
 }

 try {
 self::throttleGlobal();
 self::throttlePerChat($chatId);

 $response = Http::withHeaders([
 'X-API-KEY' => self::apiKey(),
 ])
 ->timeout((int) env('TELEGRAM_PULSE_TIMEOUT', 10))
 ->post(self::baseUrl() . '/api/v1/messages/send', [
 'chatId' => (string) $chatId,
 'content' => $content,
 'parseMode' => $parseMode,
 ]);

 if ($response->successful()) {
 return ['success' => true, 'message' => 'Pesan telegram terkirim.'];
 }

 return [
 'success' => false,
 'message' => 'API telegram merespons gagal (HTTP ' . $response->status() . '): ' . $response->body(),
 ];
 } catch (\Throwable $e) {
 Log::warning('Gagal kirim notifikasi telegram', [
 'chat_id' => $chatId,
 'message' => $e->getMessage(),
 ]);

 return ['success' => false, 'message' => $e->getMessage()];
 }
 }

 /**
 * Kirim pesan ke banyak Chat ID sekaligus via endpoint bulk.
 *
 * @param array $chatIds
 * @param string $title
 * @param string $content
 * @param string $parseMode
 * @return array
 */
 public static function sendBulk(array $chatIds, $title, $content, $parseMode = 'HTML')
 {
 if (!self::isEnabled()) {
 return ['success' => false, 'message' => 'Notifikasi telegram dinonaktifkan atau API key kosong.'];
 }

 if (empty($chatIds)) {
 return ['success' => false, 'message' => 'Tidak ada chat ID penerima.'];
 }

 try {
 self::throttleGlobal();

 foreach ($chatIds as $chatId) {
 self::throttlePerChat($chatId);
 }

 $response = Http::withHeaders([
 'X-API-KEY' => self::apiKey(),
 ])
 ->timeout((int) env('TELEGRAM_PULSE_TIMEOUT', 10))
 ->post(self::baseUrl() . '/api/v1/messages/bulk', [
 'title' => $title,
 'content' => $content,
 'parseMode' => $parseMode,
 'recipients' => array_map('strval', $chatIds),
 ]);

 if ($response->successful()) {
 return ['success' => true, 'message' => 'Pesan bulk telegram masuk antrean.'];
 }

 return [
 'success' => false,
 'message' => 'API bulk telegram merespons gagal (HTTP ' . $response->status() . '): ' . $response->body(),
 ];
 } catch (\Throwable $e) {
 Log::warning('Gagal kirim bulk telegram', [
 'recipients' => $chatIds,
 'message' => $e->getMessage(),
 ]);

 return ['success' => false, 'message' => $e->getMessage()];
 }
 }

 /**
 * Ambil daftar chat ID telegram milik pegawai (kolom tg_chat_id, fallback notifikasi_group).
 *
 * @param mixed $pegawai
 * @return array
 */
 public static function resolvePegawaiChatId($pegawai)
 {
 $chatId = null;

 if (Schema::hasColumn('pegawais', 'tg_chat_id')) {
 $chatId = $pegawai->tg_chat_id ?? null;
 }

 if (empty($chatId) && Schema::hasColumn('pegawais', 'notifikasi_group')) {
 $chatId = $pegawai->notifikasi_group ?? null;
 }

 return $chatId ? [(string) $chatId] : [];
 }
}
