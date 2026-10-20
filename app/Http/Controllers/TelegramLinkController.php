<?php

namespace App\Http\Controllers;

use App\Helpers\TelegramHelper;
use App\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TelegramLinkController extends Controller
{
 /**
 * Halaman mandiri koneksi Telegram (tersedia untuk semua role,
 * termasuk role yang tidak memiliki halaman profile).
 *
 * @return \Illuminate\View\View
 */
 public function index()
 {
 $pegawai = Pegawai::find(\Auth::user()->pegawai_id);

 return view('telegram.connect', ['pegawai' => $pegawai]);
 }

 /**
 * Tampilkan status koneksi telegram user yang login.
 *
 * @return \Illuminate\Http\RedirectResponse
 */
 public function status()
 {
 $pegawai = Pegawai::find(\Auth::user()->pegawai_id);

 $linked = $pegawai && \Illuminate\Support\Facades\Schema::hasColumn('pegawais', 'tg_chat_id') && !empty($pegawai->tg_chat_id);

 // HTML kartu dirender ulang agar front-end bisa mengganti tampilan
 // tanpa perlu refresh halaman setelah akun terhubung.
 $html = '';
 try {
 $html = view('partials.telegram_connect_body', ['pegawai' => $pegawai])->render();
 } catch (\Throwable $e) {
 Log::warning('Gagal render kartu telegram', ['message' => $e->getMessage()]);
 }

 return response()->json([
 'linked' => $linked,
 'chat_id' => $linked ? $pegawai->tg_chat_id : null,
 'html' => $html,
 ]);
 }

 /**
 * Buat token pairing satu kali dan tampilkan instruksi ke user.
 * User tinggal kirim token itu ke bot Telegram.
 *
 * @return \Illuminate\Http\RedirectResponse
 */
 public function start(Request $request)
 {
 $pegawai = Pegawai::find(\Auth::user()->pegawai_id);

 if (!$pegawai) {
 return back()->with('error', 'Data pegawai tidak ditemukan');
 }

 $token = 'SIKAP_' . strtoupper(Str::random(8));
 $tokenTtl = (int) config('telegram_pulse.token_ttl_minutes', 15);

 $payload = [
 'pegawai_id' => $pegawai->id,
 'user_id' => \Auth::user()->id,
 ];

 // Simpan dua kunci: token lengkap (SIKAP_XXXXXXXX) dan token tanpa prefix.
 // Bot Telegram memotong prefix "sikap_" sebelum memverifikasi.
 Cache::put('tg_link_' . $token, $payload, now()->addMinutes($tokenTtl));
 Cache::put('tg_link_' . self::stripPrefix($token), $payload, now()->addMinutes($tokenTtl));

 $botUsername = config('telegram_pulse.bot_username', env('TELEGRAM_BOT_USERNAME', ''));
 $linkUrl = $botUsername ? 'https://t.me/' . $botUsername . '?start=' . $token : null;

 // Respons AJAX: kembalikan token + HTML kotak token agar bisa tampil tanpa refresh
 if ($request->ajax() || $request->wantsJson()) {
 $tokenHtml = view('partials.telegram_token_box', [
 'token' => $token,
 'linkUrl' => $linkUrl,
 'botUsername' => $botUsername,
 ])->render();

 return response()->json([
 'success' => true,
 'token' => $token,
 'link_url' => $linkUrl,
 'bot_username' => $botUsername,
 'token_html' => $tokenHtml,
 'ttl_minutes' => $tokenTtl,
 'message' => 'Token pairing dibuat. Buka tautan Telegram berikut dalam ' . $tokenTtl . ' menit.',
 ]);
 }

 return back()->with('telegram_link_token', $token)
 ->with('telegram_link_url', $linkUrl)
 ->with('telegram_bot_username', $botUsername)
 ->with('status', 'Token pairing dibuat. Buka tautan Telegram berikut dalam ' . $tokenTtl . ' menit.');
 }

 /**
 * Test koneksi: kirim pesan percobaan ke chat ID user.
 *
 * @return \Illuminate\Http\RedirectResponse
 */
 public function test(Request $request)
 {
 $pegawai = Pegawai::find(\Auth::user()->pegawai_id);

 if (!$pegawai || empty($pegawai->tg_chat_id)) {
 if ($request->ajax() || $request->wantsJson()) {
 return response()->json([
 'success' => false,
 'message' => 'Telegram belum terhubung. Hubungkan terlebih dahulu.',
 ]);
 }

 return back()->with('error', 'Telegram belum terhubung. Hubungkan terlebih dahulu.');
 }

 $content = "\xE2\x9C\x85 <b>Test Koneksi Berhasil</b>\n\n"
 . "Halo " . e($pegawai->name) . ", akun Telegram kamu sudah terhubung dengan sistem BPR Cianjur.\n"
 . "Notifikasi cuti dan peraturan akan dikirim ke chat ini.";

 $result = TelegramHelper::sendMessage($pegawai->tg_chat_id, $content);

 if ($request->ajax() || $request->wantsJson()) {
 return response()->json([
 'success' => !empty($result['success']),
 'message' => !empty($result['success'])
 ? 'Test koneksi berhasil. Cek Telegram kamu.'
 : 'Test koneksi gagal: ' . ($result['message'] ?? 'tidak diketahui'),
 ]);
 }

 if ($result['success']) {
 return back()->with('status', 'Test koneksi berhasil. Cek Telegram kamu.');
 }

 return back()->with('error', 'Test koneksi gagal: ' . $result['message']);
 }

 /**
 * Putuskan koneksi telegram user.
 *
 * @return \Illuminate\Http\RedirectResponse
 */
 public function unlink(Request $request)
 {
 $pegawai = Pegawai::find(\Auth::user()->pegawai_id);

 if ($pegawai && \Illuminate\Support\Facades\Schema::hasColumn('pegawais', 'tg_chat_id')) {
 $pegawai->tg_chat_id = null;
 $pegawai->save();
 }

 if ($request->ajax() || $request->wantsJson()) {
 return response()->json([
 'success' => true,
 'message' => 'Koneksi Telegram berhasil diputus.',
 ]);
 }

 return back()->with('status', 'Koneksi Telegram berhasil diputus.');
 }

 /**
 * Webhook Telegram: user kirim /start <TOKEN> ke bot,
 * bot meneruskan pesan ke sini untuk dipasangkan chat ID-nya.
 *
 * @param \Illuminate\Http\Request $request
 * @return \Illuminate\Http\Response
 */
 public function webhook(Request $request)
 {
 try {
 $update = $request->all();

 $chatId = $update['message']['chat']['id'] ?? null;
 $text = trim($update['message']['text'] ?? '');

 Log::info('Telegram webhook diterima', ['chat_id' => $chatId]);

 if (!$chatId || !$text) {
 return response()->json(['ok' => true]);
 }

 // Format: /start ABCDEFGH, /start SIKAP_ABCDEFGH, atau langsung token
 if (preg_match('/^(?:\/start\s+)?([A-Za-z0-9_-]{8,40})$/i', $text, $m)) {
 $token = strtoupper($m[1]);
 $result = self::verifyToken($token, $chatId);

 if ($result['success']) {
 $pegawai = Pegawai::find(Pegawai::where('tg_chat_id', (string) $chatId)->value('id'));
 $name = $pegawai ? $pegawai->name : 'BPR Cianjur';
 self::reply($chatId, "\xE2\x9C\x85 <b>Terhubung!</b>\n\nHalo " . e($name) . ", akun Telegram kamu sudah terhubung dengan sistem.");
 } else {
 self::reply($chatId, "\xE2\x9D\x8C " . e($result['message']));
 }
 } else {
 self::reply($chatId, "Kirim perintah /start diikuti token pairing dari menu Profile untuk menghubungkan akun.");
 }

 return response()->json(['ok' => true]);
 } catch (\Throwable $e) {
 Log::error('Telegram webhook error', ['message' => $e->getMessage()]);
 return response()->json(['ok' => true]);
 }
 }

 /**
 * Balas pesan ke chat via Telegram Pulse.
 *
 * @param string $chatId
 * @param string $content
 * @return void
 */
 protected static function reply($chatId, $content)
 {
 TelegramHelper::sendMessage($chatId, $content);
 }

 /**
 * Normalisasi token: hapus prefix aplikasi (SIKAP_/SEMATA_) dan spasi.
 *
 * @param string $token
 * @return string
 */
 protected static function stripPrefix($token)
 {
 return strtoupper(preg_replace('/^(SIKAP|SEMATA)[_-]/i', '', trim((string) $token)));
 }

 /**
 * Verifikasi token pairing yang dikirim user ke bot.
 * Dipanggil oleh webhook/bot bridge, bukan oleh user langsung.
 *
 * @param string $token
 * @param string $chatId
 * @return array
 */
 public static function verifyToken($token, $chatId)
 {
 $normalized = self::stripPrefix($token);
 $key = 'tg_link_' . $normalized;
 $data = Cache::get($key);

 if (!$data) {
 // Fallback: token asli (bila bot mengirim apa adanya)
 $rawKey = 'tg_link_' . strtoupper(trim((string) $token));
 $data = $rawKey !== $key ? Cache::get($rawKey) : null;
 $key = $rawKey;
 }

 if (!$data) {
 return ['success' => false, 'message' => 'Token tautan tidak valid atau sudah kedaluwarsa. Silakan buat tautan baru di profil SIKAP.'];
 }

 $pegawai = Pegawai::find($data['pegawai_id']);

 if (!$pegawai || !\Illuminate\Support\Facades\Schema::hasColumn('pegawais', 'tg_chat_id')) {
 return ['success' => false, 'message' => 'Data pegawai tidak valid.'];
 }

 $pegawai->tg_chat_id = (string) $chatId;
 $pegawai->save();

 // Hapus kedua varian kunci (dengan & tanpa prefix) agar token sekali pakai.
 Cache::forget('tg_link_' . $normalized);
 Cache::forget('tg_link_' . strtoupper(trim((string) $token)));

 $user = !empty($data['user_id']) ? \App\User::find($data['user_id']) : null;

 Log::info('Telegram terhubung', [
 'pegawai_id' => $pegawai->id,
 'chat_id' => $chatId,
 ]);

 return [
 'success' => true,
 'message' => 'Telegram berhasil terhubung.',
 'user' => [
 'id' => $pegawai->id,
 'name' => $pegawai->name,
 'username' => $user ? $user->name : null,
 'role' => $user ? $user->roles : null,
 'jabatan' => $pegawai->relJabatan ? $pegawai->relJabatan->name : null,
 ],
 ];
 }
}

