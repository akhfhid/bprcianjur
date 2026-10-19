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

 return response()->json([
 'linked' => $linked,
 'chat_id' => $linked ? $pegawai->tg_chat_id : null,
 ]);
 }

 /**
 * Buat token pairing satu kali dan tampilkan instruksi ke user.
 * User tinggal kirim token itu ke bot Telegram.
 *
 * @return \Illuminate\Http\RedirectResponse
 */
 public function start()
 {
 $pegawai = Pegawai::find(\Auth::user()->pegawai_id);

 if (!$pegawai) {
 return back()->with('error', 'Data pegawai tidak ditemukan');
 }

 $token = strtoupper(Str::random(8));
 Cache::put('tg_link_' . $token, [
 'pegawai_id' => $pegawai->id,
 'user_id' => \Auth::user()->id,
 ], now()->addMinutes(15));

 $botUsername = env('TELEGRAM_BOT_USERNAME', '');

 return back()->with('telegram_link_token', $token)
 ->with('telegram_bot_username', $botUsername)
 ->with('status', 'Token pairing dibuat. Kirim token berikut ke bot Telegram dalam 15 menit.');
 }

 /**
 * Test koneksi: kirim pesan percobaan ke chat ID user.
 *
 * @return \Illuminate\Http\RedirectResponse
 */
 public function test()
 {
 $pegawai = Pegawai::find(\Auth::user()->pegawai_id);

 if (!$pegawai || empty($pegawai->tg_chat_id)) {
 return back()->with('error', 'Telegram belum terhubung. Hubungkan terlebih dahulu.');
 }

 $content = "\xE2\x9C\x85 <b>Test Koneksi Berhasil</b>\n\n"
 . "Halo " . e($pegawai->name) . ", akun Telegram kamu sudah terhubung dengan sistem BPR Cianjur.\n"
 . "Notifikasi cuti dan peraturan akan dikirim ke chat ini.";

 $result = TelegramHelper::sendMessage($pegawai->tg_chat_id, $content);

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
 public function unlink()
 {
 $pegawai = Pegawai::find(\Auth::user()->pegawai_id);

 if ($pegawai && \Illuminate\Support\Facades\Schema::hasColumn('pegawais', 'tg_chat_id')) {
 $pegawai->tg_chat_id = null;
 $pegawai->save();
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

 // Format: /start ABCDEFGH atau langsung token
 if (preg_match('/^(?:\/start\s+)?([A-Z0-9]{8})$/i', $text, $m)) {
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
 * Verifikasi token pairing yang dikirim user ke bot.
 * Dipanggil oleh webhook/bot bridge, bukan oleh user langsung.
 *
 * @param string $token
 * @param string $chatId
 * @return array
 */
 public static function verifyToken($token, $chatId)
 {
 $key = 'tg_link_' . strtoupper($token);
 $data = Cache::get($key);

 if (!$data) {
 return ['success' => false, 'message' => 'Token tidak valid atau kedaluwarsa.'];
 }

 $pegawai = Pegawai::find($data['pegawai_id']);

 if (!$pegawai || !\Illuminate\Support\Facades\Schema::hasColumn('pegawais', 'tg_chat_id')) {
 return ['success' => false, 'message' => 'Data pegawai tidak valid.'];
 }

 $pegawai->tg_chat_id = (string) $chatId;
 $pegawai->save();
 Cache::forget($key);
 Log::info('Telegram terhubung', [
 'pegawai_id' => $pegawai->id,
 'chat_id' => $chatId,
 ]);

 return ['success' => true, 'message' => 'Telegram berhasil terhubung.'];
 }
}

