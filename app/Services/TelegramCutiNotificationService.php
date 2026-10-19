<?php

namespace App\Services;

use App\Cabang;
use App\Helpers\NotificationLogHelper;
use App\Helpers\TelegramHelper;
use App\Jabatan;
use App\Pegawai;
use App\ordercuti;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class TelegramCutiNotificationService
{
 /**
 * Kirim notifikasi telegram untuk satu order cuti.
 * Menangani: permintaan baru, persetujuan, dan penolakan.
 *
 * @param \App\ordercuti $cuti
 * @param string $event SUBMIT|DISETUJUI|DITOLAK
 * @return void
 */
 public function notify(ordercuti $cuti, $event = 'SUBMIT')
 {
 try {
 if (!TelegramHelper::isEnabled()) {
 return;
 }

 $pegawai = Pegawai::find($cuti->pegawai_id);
 if (!$pegawai) {
 Log::warning('Notif telegram cuti dilewati: pegawai tidak ditemukan', [
 'order_cuti_id' => $cuti->id,
 ]);
 return;
 }

 $content = $this->buildMessage($cuti, $pegawai, $event);
 $chatIds = $this->resolveRecipientChatIds($cuti, $pegawai);

 if (empty($chatIds)) {
 Log::warning('Notif telegram cuti dilewati: tidak ada chat ID penerima', [
 'order_cuti_id' => $cuti->id,
 ]);
 return;
 }

 $title = 'Notifikasi Cuti - ' . $event;
 $result = TelegramHelper::sendBulk($chatIds, $title, $content);

 NotificationLogHelper::write([
 'actor_id' => $cuti->user_id,
 'category' => 'cuti',
 'channel' => 'telegram',
 'status' => $result['success'] ? 'success' : 'error',
 'reference_type' => 'ordercuti',
 'reference_id' => $cuti->id,
 'cabang_id' => $cuti->cabang,
 'recipient_pegawai_id' => $pegawai->id,
 'recipient_name' => $pegawai->name,
 'subject' => $title,
 'message' => $result['message'],
 'error_message' => $result['success'] ? null : $result['message'],
 'meta' => [
 'event' => $event,
 'recipients' => $chatIds,
 ],
 ]);
 } catch (\Throwable $e) {
 Log::error('Gagal notifikasi telegram cuti', [
 'order_cuti_id' => $cuti->id ?? null,
 'event' => $event,
 'message' => $e->getMessage(),
 ]);
 }
 }

 /**
 * Bangun isi pesan telegram.
 *
 * @param \App\ordercuti $cuti
 * @param \App\Pegawai $pegawai
 * @param string $event
 * @return string
 */
 protected function buildMessage(ordercuti $cuti, Pegawai $pegawai, $event)
 {
 $cabangName = optional(Cabang::find($cuti->cabang))->name ?? '-';

 $statusLine = [
 'SUBMIT' => "\xF0\x9F\x93\xA9 <b>PERMINTAAN CUTI BARU</b>",
 'DISETUJUI' => "\xE2\x9C\x85 <b>CUTI DISETUJUI</b>",
 'DITOLAK' => "\xE2\x9D\x8C <b>CUTI DITOLAK</b>",
 ];

 $lines = [
 $statusLine[$event] ?? '<b>Notifikasi Cuti</b>',
 '',
 '<b>Pegawai</b>: ' . e($pegawai->name),
 '<b>Cabang</b>: ' . e($cabangName),
 '<b>Jenis Cuti</b>: ' . e($cuti->jeniscuti),
 '<b>Tanggal</b>: ' . e($cuti->tglawal) . ' s/d ' . e($cuti->tglakhir),
 '<b>Jumlah Hari</b>: ' . e($cuti->jmlcuti),
 '<b>Alasan</b>: ' . e($cuti->alasan),
 '<b>Status</b>: ' . e($cuti->status),
 ];

 if ($event === 'SUBMIT') {
 $lines[] = '';
 $lines[] = 'Mohon untuk ditindaklanjuti (approval atasan).';
 }

 return implode("\n", $lines);
 }

 /**
 * Kumpulkan chat ID penerima: pemohon, atasan langsung, atasan diketahui.
 *
 * @param \App\ordercuti $cuti
 * @param \App\Pegawai $pegawai
 * @return array
 */
 protected function resolveRecipientChatIds(ordercuti $cuti, Pegawai $pegawai)
 {
 $chatIds = [];

 $addFromPegawai = function ($p) use (&$chatIds) {
 if (!$p) {
 return;
 }
 foreach (TelegramHelper::resolvePegawaiChatId($p) as $chatId) {
 if (!in_array($chatId, $chatIds)) {
 $chatIds[] = $chatId;
 }
 }
 };

 // Pemohon selalu menerima kabar status.
 $addFromPegawai($pegawai);

 // Atasan langsung & atasan diketahui (berdasarkan jabatan).
 $jabatanPemohon = Jabatan::find($pegawai->jabatan);

 if ($jabatanPemohon && $jabatanPemohon->atasan) {
 Pegawai::where('jabatan', $jabatanPemohon->atasan)
 ->where('status_active', 1)
 ->get()
 ->each($addFromPegawai);

 $jabatanAtasan = Jabatan::find($jabatanPemohon->atasan);
 if ($jabatanAtasan && $jabatanAtasan->atasan) {
 Pegawai::where('jabatan', $jabatanAtasan->atasan)
 ->where('status_active', 1)
 ->get()
 ->each($addFromPegawai);
 }
 }

 // Atasan yang ditunjuk pada order cuti (jika ada chat ID).
 if ($cuti->otoatasan) {
 Pegawai::where('jabatan', $cuti->otoatasan)->get()->each($addFromPegawai);
 }
 if ($cuti->diketatasan) {
 Pegawai::where('jabatan', $cuti->diketatasan)->get()->each($addFromPegawai);
 }

 return $chatIds;
 }
}
