<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class WhatsAppHelper
{
    const DEFAULT_API_URL = 'https://wa.bprcianjur.co.id/api/send-message';

    public static function convertPhoneNumber($nohp)
    {
        if (empty($nohp)) {
            return null;
        }

        $nohp = trim($nohp);
        $nohp = str_replace(array(' ', '-', '+', '(', ')'), '', $nohp);

        if (strpos($nohp, '0') === 0) {
            $nohp = '62' . substr($nohp, 1);
        } elseif (strpos($nohp, '62') !== 0) {
            $nohp = '62' . $nohp;
        }

        return $nohp;
    }

    public static function getTimeGreeting()
    {
        $hour = (int) date('H');

        if ($hour >= 6 && $hour < 11) {
            return 'Selamat Pagi';
        } elseif ($hour >= 11 && $hour < 15) {
            return 'Selamat Siang';
        } elseif ($hour >= 15 && $hour < 18) {
            return 'Selamat Sore';
        }

        return "Assalamu'alaikum";
    }

    public static function sendMessage($phoneNumber, $message)
    {
        $phone = self::convertPhoneNumber($phoneNumber);
        $apiUrl = env('WA_API_URL', self::DEFAULT_API_URL);
        $apiCode = env('WA_API_CODE', '');

        if (!$phone) {
            return array(
                'success' => false,
                'message' => 'Nomor HP kosong'
            );
        }

        if (empty($apiCode)) {
            Log::error('WhatsApp Config Error', array(
                'message' => 'WA_API_CODE kosong'
            ));

            return array(
                'success' => false,
                'message' => 'WA_API_CODE kosong'
            );
        }

        $data = array(
            'code' => $apiCode,
            'penerima' => array(
                array(
                    'name' => '',
                    'phone' => $phone
                )
            ),
            'message_type' => '1',
            'message_schedule' => 'normal',
            'file' => '',
            'pesan' => $message,
            'delay' => '5000'
        );

        try {
            $response = Http::withOptions(array(
                'verify' => false,
                'timeout' => 30,
            ))
            ->withHeaders(array(
                'Accept' => 'application/json',
            ))
            ->post($apiUrl, $data);

            if ($response->failed()) {
                Log::error('WhatsApp API Error', array(
                    'phone' => $phone,
                    'status' => $response->status(),
                    'error' => $response->body()
                ));

                return array(
                    'success' => false,
                    'message' => 'Request failed with status ' . $response->status()
                );
            }

            Log::info('WhatsApp API Response', array(
                'phone' => $phone,
                'http_code' => $response->status(),
                'response' => $response->json()
            ));

            $responseData = $response->json();
            $apiStatus = is_array($responseData) && isset($responseData['status'])
                ? (int) $responseData['status']
                : null;
            $apiMessage = is_array($responseData) && isset($responseData['message'])
                ? $responseData['message']
                : null;

            // Penting: HTTP 200 belum tentu sukses kirim WA.
            if (!is_null($apiStatus) && $apiStatus !== 200) {
                Log::warning('WhatsApp API Reject', array(
                    'phone' => $phone,
                    'http_code' => $response->status(),
                    'api_status' => $apiStatus,
                    'api_message' => $apiMessage
                ));

                return array(
                    'success' => false,
                    'http_code' => $response->status(),
                    'response' => $responseData,
                    'message' => $apiMessage ? $apiMessage : 'API WA menolak request'
                );
            }

            return array(
                'success' => true,
                'http_code' => $response->status(),
                'response' => $responseData
            );
        } catch (\Exception $e) {
            Log::error('WhatsApp Exception', array(
                'phone' => $phoneNumber,
                'message' => $e->getMessage()
            ));

            return array(
                'success' => false,
                'message' => $e->getMessage()
            );
        }
    }

    public static function sendCutiNotificationAtasan1($pegawai, $order, $atasan1)
    {
        if (!$atasan1 || empty($atasan1->nohp)) {
            return array(
                'success' => false,
                'message' => 'Atasan tidak ditemukan atau nomor HP kosong'
            );
        }

        $message = self::getTimeGreeting() . ', ' . $atasan1->name . "\n\n";
        $message .= 'Terdapat permohonan cuti dari: ' . $pegawai->name . "\n";
        $message .= 'Jenis Cuti: ' . $order->jeniscuti . "\n";
        $message .= 'Tanggal Cuti: ' . date('d-m-Y', strtotime($order->tglawal)) . ' s.d. ' . date('d-m-Y', strtotime($order->tglakhir)) . "\n";
        $message .= 'Jumlah Hari: ' . $order->jmlcuti . " hari\n";
        $message .= 'Alasan: ' . $order->alasan . "\n";
        $message .= 'Sisa Cuti: ' . $pegawai->scuti . " hari\n\n";
        $message .= 'Mohon dapat dilakukan persetujuan pada Aplikasi SIKAP BPR Cianjur';

        return self::sendMessage($atasan1->nohp, $message);
    }

    public static function sendCutiNotificationAtasan2($pegawai, $atasan1, $atasan2, $order)
    {
        if (!$atasan2 || empty($atasan2->nohp)) {
            return array(
                'success' => false,
                'message' => 'Atasan 2 tidak ditemukan atau nomor HP kosong'
            );
        }

        $message = self::getTimeGreeting() . ', ' . $atasan2->name . "\n\n";
        $message .= 'Terdapat permohonan cuti dari: ' . $pegawai->name . "\n";
        $message .= 'Jenis Cuti: ' . $order->jeniscuti . "\n";
        $message .= 'Tanggal Cuti: ' . date('d-m-Y', strtotime($order->tglawal)) . ' s.d. ' . date('d-m-Y', strtotime($order->tglakhir)) . "\n";
        $message .= 'Jumlah Hari: ' . $order->jmlcuti . " hari\n";
        $message .= 'Alasan: ' . $order->alasan . "\n";
        $message .= 'Sisa Cuti: ' . $pegawai->scuti . " hari\n";
        $message .= 'Cuti telah disetujui oleh: ' . $atasan1->name . "\n\n";
        $message .= 'Mohon dapat dilakukan persetujuan pada Aplikasi SIKAP BPR Cianjur';

        return self::sendMessage($atasan2->nohp, $message);
    }

    public static function sendCutiApprovalFinalNotification($pegawai, $order, $approver)
    {
        if (!$pegawai || empty($pegawai->nohp)) {
            return array(
                'success' => false,
                'message' => 'Pemohon tidak ditemukan atau nomor HP kosong'
            );
        }

        $message = self::getTimeGreeting() . ', ' . $pegawai->name . "\n\n";
        $message .= "Permohonan cuti Anda telah disetujui.\n\n";
        $message .= 'Jenis Cuti: ' . $order->jeniscuti . "\n";
        $message .= 'Tanggal Cuti: ' . date('d-m-Y', strtotime($order->tglawal)) . ' s.d. ' . date('d-m-Y', strtotime($order->tglakhir)) . "\n";
        $message .= 'Jumlah Hari: ' . $order->jmlcuti . " hari\n";
        $message .= 'Sisa Cuti: ' . $pegawai->scuti . " hari\n";
        $message .= 'Disetujui oleh: ' . $approver->name;

        return self::sendMessage($pegawai->nohp, $message);
    }

    public static function sendCutiRejectedNotification($pegawai, $order, $rejectedByName)
    {
        if (!$pegawai || empty($pegawai->nohp)) {
            return array(
                'success' => false,
                'message' => 'Pemohon tidak ditemukan atau nomor HP kosong'
            );
        }

        $alasan = trim($order->alasan) !== '' ? $order->alasan : '-';
        $sisaCuti = !is_null($pegawai->scuti) ? $pegawai->scuti . ' hari' : '-';
        $atasanPenolak = trim($rejectedByName) !== '' ? $rejectedByName : 'Atasan';

        $message = self::getTimeGreeting() . ', ' . $pegawai->name . "\n";
        $message .= "Permohonan Cuti Anda\n";
        $message .= 'Jenis Cuti: ' . $order->jeniscuti . "\n";
        $message .= 'Tanggal Cuti: ' . date('d-m-Y', strtotime($order->tglawal)) . ' s.d. ' . date('d-m-Y', strtotime($order->tglakhir)) . "\n";
        $message .= 'Jumlah Hari: ' . $order->jmlcuti . " hari\n";
        $message .= 'Alasan: ' . $alasan . "\n";
        $message .= 'Sisa Cuti: ' . $sisaCuti . "\n\n";
        $message .= 'Mohon Maaf tidak disetujui oleh ' . $atasanPenolak;

        return self::sendMessage($pegawai->nohp, $message);
    }

    public static function sendPeraturanBaruNotificationToPegawai($peraturan, $pegawai)
{
    if (!$peraturan) {
        return [
            'success' => false,
            'message' => 'Data peraturan tidak ditemukan'
        ];
    }

    if (!$pegawai || empty($pegawai->nohp)) {
        return [
            'success' => false,
            'message' => 'Data pegawai tidak ditemukan atau nomor HP kosong'
        ];
    }

    $namaPegawai = trim((string) $pegawai->name) !== '' ? $pegawai->name : 'Pegawai';
    $namaPeraturan = trim((string) $peraturan->name) !== '' ? $peraturan->name : '-';
    $kategoriRaw = strtolower(trim((string) $peraturan->kategori));
    $kategori = $kategoriRaw !== '' ? strtoupper($kategoriRaw) : '-';
    $jenisPeraturan = trim((string) $peraturan->jenis_surat) !== '' ? $peraturan->jenis_surat : '-';
    $subJenis = trim((string) $peraturan->jenis_ojk) !== '' ? $peraturan->jenis_ojk : '-';
    $nomorSk = trim((string) $peraturan->nosk) !== '' ? $peraturan->nosk : '-';

    $tanggalSk = $peraturan->tglsk ? date('d-m-Y', strtotime($peraturan->tglsk)) : '-';
    $tanggalBerlaku = $peraturan->tgllaku ? date('d-m-Y', strtotime($peraturan->tgllaku)) : '-';

    $message = self::getTimeGreeting() . ', ' . $namaPegawai . "\n\n";
    $message .= "Terdapat peraturan baru yang ditambahkan\n\n";
    $message .= "Nama Peraturan : " . $namaPeraturan . "\n";
    $message .= "Kategori       : " . $kategori . "\n";
    $message .= "Jenis          : " . $jenisPeraturan . "\n";
    if ($kategoriRaw !== 'internal' && $subJenis !== '-') {
        $message .= "Sub Jenis OJK  : " . $subJenis . "\n";
    }
    $message .= "Nomor SK       : " . $nomorSk . "\n";
    $message .= "Tanggal SK     : " . $tanggalSk . "\n";
    $message .= "Berlaku Mulai  : " . $tanggalBerlaku . "\n\n";
    $message .= "Silakan cek pada aplikasi SIKAP BPR Cianjur.";

    return self::sendMessage($pegawai->nohp, $message);
}
}
