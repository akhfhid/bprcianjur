<?php

namespace App\Services;

use App\Helpers\NotificationLogHelper;
use App\Helpers\TelegramHelper;
use App\Jabatan;
use App\Pegawai;
use App\orderatur;
use App\peraturan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class TelegramPeraturanDownloadNotificationService
{
 /**
 * Jabatan divisi penerima notifikasi permintaan download peraturan.
 *
 * @var array
 */
 protected $jabatanKepatuhan = [
 'Kepatuhan',
 'Kepala Divisi Kepatuhan',
 'Kepala Divisi Kepatuhan & Manajemen Resiko',
 'Manajemen Resiko',
 ];

 /**
 * Kirim notifikasi telegram ke divisi Kepatuhan & Manajemen Resiko
 * ketika ada permintaan download peraturan (orderatur).
 *
 * @param \App\orderatur $order
 * @param string $event SUBMIT|SETUJU|TOLAK
 * @return void
 */
 public function notify(orderatur $order, $event = 'SUBMIT')
 {
 try {
 if (!TelegramHelper::isEnabled()) {
 return;
 }

 $pegawai = Pegawai::find($order->pegawai_id);
 $peraturan = peraturan::find($order->peraturan_id);

 $content = $this->buildMessage($order, $pegawai, $peraturan, $event);
 $chatIds = $this->resolveKepatuhanChatIds();

 if (empty($chatIds)) {
 Log::warning('Notif telegram permintaan download peraturan dilewati: tidak ada chat ID divisi kepatuhan', [
 'orderatur_id' => $order->id,
 ]);
 return;
 }

 $title = 'Permintaan Download Peraturan - ' . $event;
 $result = TelegramHelper::sendBulk($chatIds, $title, $content);

 NotificationLogHelper::write([
 'actor_id' => $order->user_id,
 'category' => 'peraturan',
 'channel' => 'telegram',
 'status' => $result['success'] ? 'success' : 'error',
 'reference_type' => 'orderatur',
 'reference_id' => $order->id,
 'cabang_id' => $order->cabang_id,
 'recipient_pegawai_id' => $pegawai->id ?? null,
 'recipient_name' => $pegawai->name ?? null,
 'subject' => $title,
 'message' => $result['message'],
 'error_message' => $result['success'] ? null : $result['message'],
 'meta' => [
 'event' => $event,
 'recipients' => $chatIds,
 ],
 ]);
 } catch (\Throwable $e) {
 Log::error('Gagal notifikasi telegram permintaan download peraturan', [
 'orderatur_id' => $order->id ?? null,
 'event' => $event,
 'message' => $e->getMessage(),
 ]);
 }
 }

 /**
 * Bangun isi pesan telegram.
 *
 * @param \App\orderatur $order
 * @param \App\Pegawai|null $pegawai
 * @param \App\peraturan|null $peraturan
 * @param string $event
 * @return string
 */
 protected function buildMessage(orderatur $order, $pegawai, $peraturan, $event)
 {
 $header = [
 'SUBMIT' => "\xF0\x9F\x94\x94 <b>PERMINTAAN DOWNLOAD PERATURAN</b>",
 'SETUJU' => "\xE2\x9C\x85 <b>PERMINTAAN DOWNLOAD DISETUJUI</b>",
 'TOLAK' => "\xE2\x9D\x8C <b>PERMINTAAN DOWNLOAD DITOLAK</b>",
 ];

 $lines = [
 $header[$event] ?? '<b>Notifikasi Permintaan Download</b>',
 '',
 '<b>Pemohon</b>: ' . e($pegawai->name ?? '-'),
 '<b>Peraturan</b>: ' . e($peraturan->name ?? '-'),
 '<b>No. SK</b>: ' . e($peraturan->nosk ?? '-'),
 '<b>Keterangan</b>: ' . e($order->ket ?? '-'),
 '<b>Status</b>: ' . e($order->status),
 ];

 if ($event === 'SUBMIT') {
 $lines[] = '';
 $lines[] = 'Mohon divisi Kepatuhan & Manajemen Resiko untuk menindaklanjuti permintaan ini.';
 }

 return implode("\n", $lines);
 }

 /**
 * Kumpulkan chat ID telegram pegawai divisi Kepatuhan & Manajemen Resiko.
 *
 * @return array
 */
 protected function resolveKepatuhanChatIds()
 {
 $hasTgColumn = Schema::hasColumn('pegawais', 'tg_chat_id');

 if (!$hasTgColumn) {
 return [];
 }

 $query = Pegawai::where('status_active', 1)
 ->whereNotNull('tg_chat_id')
 ->where('tg_chat_id', '<>', '')
 ->whereHas('jabatan', function ($q) {
 $q->where(function ($sub) {
 foreach ($this->jabatanKepatuhan as $jabatanName) {
 $sub->orWhere('name', 'like', '%' . $jabatanName . '%');
 }
 });
 });

 return $query->pluck('tg_chat_id')
 ->map(function ($id) {
 return (string) $id;
 })
 ->unique()
 ->values()
 ->all();
 }
}
